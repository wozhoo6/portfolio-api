<?php

return [
     'visitor_salt' => env('VISITOR_HASH_SALT', ''),
    'view_dedupe_minutes' => 30,
    'reaction_types' => ['like', 'love', 'fire', 'clap'],
    'turnstile_secret' => env('TURNSTILE_SECRET'),
    'admin_token' => env('ADMIN_TOKEN'),
];