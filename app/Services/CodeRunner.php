<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Runs student code on a Judge0 compiler server (never on your own website).
 * HTML is not run here: it is shown live in the browser and graded by AnswerGrader.
 */
class CodeRunner
{
    /**
     * key => label, the Judge0 language-name pattern, and a fallback id for Judge0 CE 1.13.
     *
     * @var array<string, array<string, mixed>>
     */
    public const LANGUAGES = [
        'html' => ['label' => 'HTML / CSS / JavaScript (web page)'],
        'python' => ['label' => 'Python 3', 'pattern' => '/^Python \(3/', 'fallback' => 71],
        'javascript' => ['label' => 'JavaScript (Node.js)', 'pattern' => '/^JavaScript \(Node/', 'fallback' => 63],
        'c' => ['label' => 'C', 'pattern' => '/^C \(GCC/', 'fallback' => 50],
        'cpp' => ['label' => 'C++', 'pattern' => '/^C\+\+ \(GCC/', 'fallback' => 54],
        'java' => ['label' => 'Java (class must be named Main)', 'pattern' => '/^Java \((OpenJDK|JDK)/', 'fallback' => 62],
        'php' => ['label' => 'PHP', 'pattern' => '/^PHP \(/', 'fallback' => 68],
    ];

    /** @return array<string, string> key => label */
    public static function labels(): array
    {
        return array_map(fn (array $l): string => $l['label'], self::LANGUAGES);
    }

    public function isConfigured(): bool
    {
        return filled(config('coding.url'));
    }

    /**
     * Run once.
     *
     * @return array{ok: bool, status: string, stdout: string, stderr: string, time: ?string, error: ?string}
     */
    public function run(string $language, string $code, string $stdin = ''): array
    {
        $blank = ['ok' => false, 'status' => '', 'stdout' => '', 'stderr' => '', 'time' => null, 'error' => null];

        if (! $this->isConfigured()) {
            return [...$blank, 'error' => 'The code compiler is not set up yet. Please tell your teacher.'];
        }

        if (! isset(self::LANGUAGES[$language]['pattern'])) {
            return [...$blank, 'error' => 'This language cannot be run on the server.'];
        }

        try {
            $payload = [
                'language_id' => $this->languageId($language),
                'source_code' => base64_encode($code),
                'stdin' => base64_encode($stdin),
            ];

            $base = rtrim((string) config('coding.url'), '/');
            $response = $this->client()->post($base.'/submissions?base64_encoded=true&wait=true', $payload);

            if ($response->failed() && ! $response->json('token')) {
                return [...$blank, 'error' => 'Compiler error ('.$response->status().'). Please try again in a moment.'];
            }

            $data = $response->json() ?? [];
            $token = $data['token'] ?? null;

            // Some servers do not allow wait=true: poll for the result instead.
            for ($i = 0; $i < 20 && $token && (($data['status']['id'] ?? 1) <= 2); $i++) {
                usleep(800_000);
                $poll = $this->client()->get($base.'/submissions/'.$token.'?base64_encoded=true');
                $data = $poll->json() ?? $data;
            }
        } catch (\Throwable $e) {
            logger()->warning('Code runner failed', ['error' => $e->getMessage()]);

            return [...$blank, 'error' => 'Could not reach the compiler. Please try again.'];
        }

        $decode = fn (?string $v): string => $v === null ? '' : (string) base64_decode(preg_replace('/\s+/', '', $v) ?? '', false);
        $statusId = (int) ($data['status']['id'] ?? 0);

        if ($statusId === 0 || $statusId <= 2) {
            return [...$blank, 'error' => 'The compiler took too long to answer. Please try again.'];
        }

        $stderr = trim($decode($data['compile_output'] ?? null)."\n".$decode($data['stderr'] ?? null)."\n".$decode($data['message'] ?? null));

        return [
            // 3 = ran to the end with no error (a wrong answer is decided by us, not by Judge0)
            'ok' => $statusId === 3,
            'status' => (string) ($data['status']['description'] ?? ''),
            'stdout' => $decode($data['stdout'] ?? null),
            'stderr' => Str::limit($stderr, 2000, '…'),
            'time' => $data['time'] ?? null,
            'error' => null,
        ];
    }

    /**
     * Run every test case and compare the output.
     *
     * @param  array<int, array{input?: string, output?: string}>  $tests
     * @return array{passed: bool, total: int, passed_count: int, results: array<int, array<string, mixed>>, error: ?string}
     */
    public function grade(string $language, string $code, array $tests, AnswerGrader $grader): array
    {
        $out = ['passed' => false, 'total' => count($tests), 'passed_count' => 0, 'results' => [], 'error' => null];

        foreach ($tests as $i => $test) {
            $run = $this->run($language, $code, (string) ($test['input'] ?? ''));

            if ($run['error']) {
                $out['error'] = $run['error'];
                break;
            }

            $ok = $run['ok'] && $grader->outputMatches($run['stdout'], (string) ($test['output'] ?? ''));

            $out['results'][] = [
                'test' => $i + 1,
                'passed' => $ok,
                'status' => $run['ok'] ? ($ok ? 'Passed' : 'Wrong output') : $run['status'],
                'message' => $run['ok'] ? null : Str::limit($run['stderr'], 300, '…'),
            ];

            $out['passed_count'] += $ok ? 1 : 0;
        }

        $out['passed'] = $out['error'] === null && $out['total'] > 0 && $out['passed_count'] === $out['total'];

        return $out;
    }

    private function client(): PendingRequest
    {
        $headers = [];
        $token = config('coding.auth_token');

        if (config('coding.rapidapi_host')) {
            $headers['X-RapidAPI-Key'] = $token;
            $headers['X-RapidAPI-Host'] = config('coding.rapidapi_host');
        } elseif ($token) {
            $headers[config('coding.auth_header')] = $token;
        }

        return Http::withHeaders($headers)->acceptJson()->timeout(30);
    }

    private function languageId(string $key): int
    {
        $def = self::LANGUAGES[$key];
        $list = Cache::get('coding.judge0.languages');

        if (! is_array($list) || $list === []) {
            try {
                $res = $this->client()->get(rtrim((string) config('coding.url'), '/').'/languages');
                $list = $res->successful() ? ($res->json() ?? []) : [];
                if ($list) {
                    Cache::put('coding.judge0.languages', $list, now()->addDay());
                }
            } catch (\Throwable) {
                $list = [];
            }
        }

        $best = null;
        $bestVersion = '0';

        foreach ($list as $lang) {
            $name = (string) ($lang['name'] ?? '');

            if (! preg_match($def['pattern'], $name) || str_contains($name, 'ML')) {
                continue;
            }

            preg_match('/(\d+(?:\.\d+)*)/', $name, $m);
            $version = $m[1] ?? '0';

            if ($best === null || version_compare($version, $bestVersion, '>')) {
                $best = (int) $lang['id'];
                $bestVersion = $version;
            }
        }

        return $best ?? $def['fallback'];
    }
}
