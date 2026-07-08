<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\Cookie;

class ValidateCsrfToken extends Middleware
{
    protected function newCookie($request, $config)
    {
        $secure = $config['secure'];
        if ($secure === null) {
            $secure = $request->isSecure()
                || $request->header('X-Forwarded-Proto') === 'https';
        }

        return new Cookie(
            'XSRF-TOKEN',
            $request->session()->token(),
            $this->availableAt(60 * $config['lifetime']),
            $config['path'],
            $config['domain'],
            (bool) $secure,
            true,
            false,
            $config['same_site'] ?? 'lax',
            $config['partitioned'] ?? false
        );
    }
}
