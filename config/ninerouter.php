<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 9Router Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | 9Router is an OpenAI-compatible AI gateway.
    | Default local URL is http://localhost:20128/v1
    |
    */

    'base_url' => env('NINEROUTER_BASE_URL', 'http://localhost:20128/v1'),
    'api_key' => env('NINEROUTER_API_KEY', '9router-local-key'),
    'default_model' => env('NINEROUTER_DEFAULT_MODEL', 'gpt-4o'),
    'system_prompt' => env('NINEROUTER_SYSTEM_PROMPT', 'Anda adalah asisten AI profesional dan serba bisa yang terintegrasi ke dalam sistem Ekscoder. Berikan jawaban yang tepat, terstruktur, ramah, dan solutif dalam bahasa Indonesia.'),
];
