<?php

namespace App\Services;

use App\Enums\QuestionType;
use App\Models\Question;

/**
 * One single place that decides "is this answer correct?".
 *
 * It ignores spacing / indentation / line breaks / upper-lower case / quote style,
 * and for HTML answers it compares the page STRUCTURE instead of the raw text.
 * Everything here is local (no internet, no AI), so it is instant and free.
 */
class AnswerGrader
{
    /** Pull the typed value out of any of the old / new answer shapes. */
    public static function valueOf(mixed $answerData): string
    {
        if (is_array($answerData)) {
            $v = $answerData['value'] ?? $answerData['raw_answer'] ?? '';

            return is_string($v) ? $v : '';
        }

        return is_string($answerData) ? $answerData : '';
    }

    /** Is the stored answer correct? (used when grading AND on every results screen) */
    public function isCorrect(Question $question, mixed $answerData): bool
    {
        $given = self::valueOf($answerData);

        if (trim($given) === '') {
            return false;
        }

        if ($question->type === QuestionType::Coding) {
            return $this->isCodingCorrect($question, $answerData, $given);
        }

        if ($this->matches($given, (string) $question->correct_answer, $question->type === QuestionType::ShortAnswer)) {
            return true;
        }

        // Short answers that were graded by AI keep their AI verdict.
        if (is_array($answerData) && ($answerData['ai_graded'] ?? false)) {
            return ($answerData['ai_score'] ?? 0) >= 50;
        }

        return false;
    }

    /**
     * Lenient comparison of text / code.
     * For short answers the teacher may write several accepted answers separated by ||
     */
    public function matches(string $given, string $expected, bool $allowAlternatives = true): bool
    {
        if ($this->singleMatch($given, $expected)) {
            return true;
        }

        if (! $allowAlternatives || ! str_contains($expected, '||')) {
            return false;
        }

        foreach (explode('||', $expected) as $candidate) {
            if (trim($candidate) === '') {
                continue;
            }

            if ($this->singleMatch($given, $candidate)) {
                return true;
            }
        }

        return false;
    }

    private function singleMatch(string $given, string $expected): bool
    {
        $a = $this->normalize($given);
        $b = $this->normalize($expected);

        if ($a === $b) {
            return true;
        }

        if ($this->looksLikeCode($expected)) {
            // Code: spaces and line breaks do not matter at all.
            if ($this->squash($a) === $this->squash($b)) {
                return true;
            }

            if ($this->looksLikeHtml($expected) && $this->looksLikeHtml($given)) {
                return $this->htmlMatches($given, $expected);
            }
        }

        return false;
    }

    /** lower-case, fancy quotes -> normal quotes, collapse every run of spaces/newlines. */
    public function normalize(string $text): string
    {
        $text = str_replace(
            ["\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}", "\u{00A0}", "\u{200B}", "\u{FEFF}"],
            ["'", "'", '"', '"', ' ', '', ''],
            $text
        );
        $text = mb_strtolower($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim($text);

        return rtrim($text, " .;");
    }

    private function squash(string $text): string
    {
        // single and double quotes are treated the same: <a href='x'> == <a href="x">
        return str_replace("'", '"', preg_replace('/\s+/u', '', $text) ?? $text);
    }

    public function looksLikeCode(string $text): bool
    {
        return (bool) preg_match('/[<>{};=()]/', $text) || str_contains($text, "\n");
    }

    public function looksLikeHtml(string $text): bool
    {
        return (bool) preg_match('/<\s*[a-zA-Z!][^>]*>/', $text);
    }

    // ───────────────────────── HTML structure comparison ─────────────────────────

    /**
     * True when the student's page contains everything the reference page has
     * (same tags, same text, same attributes). Extra tags/attributes are allowed.
     * Spacing, indentation, attribute order, upper/lower case are ignored.
     */
    public function htmlMatches(string $given, string $reference): bool
    {
        $ref = $this->htmlTree($reference);
        $stu = $this->htmlTree($given);

        if ($ref === null || $stu === null) {
            return false;
        }

        return $this->nodeContains($stu, $ref);
    }

    /** @return array<string, mixed>|null */
    private function htmlTree(string $html): ?array
    {
        $html = trim($html);

        if ($html === '') {
            return null;
        }

        // libxml does not know HTML5 "void" tags; close them so they cannot swallow siblings.
        $html = preg_replace(
            '/<(source|track|embed|wbr)\b([^>]*?)(?<!\/)>/i',
            '<$1$2/>',
            $html
        ) ?? $html;

        $previous = libxml_use_internal_errors(true);
        $doc = new \DOMDocument;
        $ok = $doc->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $ok || ! $doc->documentElement) {
            return null;
        }

        return $this->toTree($doc->documentElement);
    }

    /** @return array<string, mixed> */
    private function toTree(\DOMNode $node): array
    {
        $attrs = [];
        foreach ($node->attributes ?? [] as $attr) {
            $attrs[strtolower($attr->nodeName)] = $this->normalizeAttr(strtolower($attr->nodeName), (string) $attr->nodeValue);
        }

        return [
            'tag' => strtolower($node->nodeName),
            'attrs' => $attrs,
            'children' => $this->childTrees($node),
        ];
    }

    /** @return array<int, array<string, mixed>|string> */
    private function childTrees(\DOMNode $node): array
    {
        $out = [];

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                $text = $this->normalize((string) $child->nodeValue);
                if ($text !== '') {
                    $out[] = $text;
                }
            } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                // <thead>/<tbody>/<tfoot> are optional wrappers – look straight through them.
                if (in_array(strtolower($child->nodeName), ['thead', 'tbody', 'tfoot'], true)) {
                    array_push($out, ...$this->childTrees($child));
                } else {
                    $out[] = $this->toTree($child);
                }
            }
        }

        return $out;
    }

    private function normalizeAttr(string $name, string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);

        if ($name === 'class') {
            $parts = array_filter(explode(' ', mb_strtolower($value)));
            sort($parts);

            return implode(' ', $parts);
        }

        if ($name === 'style') {
            return mb_strtolower(str_replace([' ', ';'], '', $value));
        }

        if ($value === $name) { // controls="controls" == controls
            return '';
        }

        return in_array($name, ['src', 'href', 'id', 'alt', 'placeholder', 'value', 'name', 'for', 'title'], true)
            ? $value
            : mb_strtolower($value);
    }

    /**
     * @param  array<string, mixed>  $stu
     * @param  array<string, mixed>  $ref
     */
    private function nodeContains(array $stu, array $ref): bool
    {
        if ($stu['tag'] !== $ref['tag']) {
            return false;
        }

        foreach ($ref['attrs'] as $name => $value) {
            if (! array_key_exists($name, $stu['attrs'])) {
                return false;
            }

            $have = $stu['attrs'][$name];
            if ($have !== $value && ! (in_array($name, ['src', 'href', 'id', 'alt', 'placeholder', 'value', 'name', 'for', 'title'], true)
                && mb_strtolower($have) === mb_strtolower($value))) {
                return false;
            }
        }

        // reference children must appear, in the same order, among the student's children
        $position = 0;
        $studentChildren = $stu['children'];

        foreach ($ref['children'] as $refChild) {
            $found = false;

            while ($position < count($studentChildren)) {
                $candidate = $studentChildren[$position++];

                if (is_string($refChild)) {
                    $match = is_string($candidate) && $candidate === $refChild;
                } else {
                    $match = is_array($candidate) && $this->nodeContains($candidate, $refChild);
                }

                if ($match) {
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                return false;
            }
        }

        return true;
    }

    // ───────────────────────────── Coding questions ─────────────────────────────

    private function isCodingCorrect(Question $question, mixed $answerData, string $code): bool
    {
        $config = $question->codingConfig() ?? [];
        $language = is_array($answerData) ? (string) ($answerData['language'] ?? '') : '';

        if ($language === 'html') {
            return $this->htmlMatches($code, (string) ($config['html_reference'] ?? ''));
        }

        return (bool) (($answerData['coding']['passed'] ?? false));
    }

    /** Compare program output: ignore Windows line endings and trailing spaces. */
    public function outputMatches(string $actual, string $expected): bool
    {
        $clean = fn (string $s): string => rtrim(implode("\n", array_map('rtrim', explode("\n", str_replace("\r\n", "\n", $s)))));

        return $clean($actual) === $clean($expected);
    }
}
