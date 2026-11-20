<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileVerifier
{
    /**
     * Verify a Cloudflare Turnstile token. Skipped when no secret is configured.
     */
    public function passes(?string $token): bool
    {
        $secret = config('portfolio.turnstile_secret');

        if (!$secret) {
            return true;
        }

        if (!$token) {
            return false;
        }

        $response = Http::asForm()->timeout(5)->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            ['secret' => $secret, 'response' => $token]
        );

        return (bool) $response->json('success');
    }
}
