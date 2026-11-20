<?php

namespace App\Services;

use Illuminate\Http\Request;

class VisitorHasher
{
    public function make(Request $request): string
    {
        $salt = config('portfolio.visitor_salt') ?: config('app.key');

        return hash_hmac('sha256', $request->ip() . '|' . $request->userAgent(), $salt);
    }
}

