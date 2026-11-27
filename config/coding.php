<?php

return [
    /*
    | The online compiler ("Judge0") that runs student code safely, away from your website.
    | Leave JUDGE0_URL empty and the Run button for C / C++ / Java / Python / JS / PHP is switched off
    | (HTML questions keep working without it).
    */
    'url' => env('JUDGE0_URL'),

    // FREE default: when JUDGE0_URL is empty, code runs on the public Wandbox compiler (no account, no key).
    'wandbox_url' => env('WANDBOX_URL', 'https://wandbox.org'),

    // Header name + token (Judge0 Cloud/Sulu or self-hosted use X-Auth-Token by default).
    'auth_header' => env('JUDGE0_AUTH_HEADER', 'X-Auth-Token'),
    'auth_token' => env('JUDGE0_AUTH_TOKEN'),

    // Only for RapidAPI: set e.g. judge0-ce.p.rapidapi.com (JUDGE0_AUTH_TOKEN then holds your RapidAPI key).
    'rapidapi_host' => env('JUDGE0_RAPIDAPI_HOST'),

    // Stops one student from hammering the compiler.
    'runs_per_minute' => (int) env('CODING_RUNS_PER_MINUTE', 10),

    'max_code_length' => 20000,
];
