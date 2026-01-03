<?php

if (!function_exists('year')) {
    function year(): string
    {
        return config('app.year');
    }
}

if (!function_exists('redact_ip')) {
    function redact_ip(string $ip, string $salt = ''): string
    {
        if (preg_match('/^user_(\d+)$/', $ip, $matches)) {
            return 'user_' . substr(hash('sha256', $salt . $matches[1] . config('app.key')), 0, 4);
        }

        // @TODO: should use a library for IP address parsing

        // IPv4
        if (preg_match('/^(ip_)?(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $ip, $matches)) {
            return "{$matches[2]}.{$matches[3]}.*";
        }

        // IPv6
        if (preg_match('/^ip_([0-9a-f]{1,4}):([0-9a-f]{0,4}):/', $ip, $matches)) {
            return "$matches[1]:$matches[2]:*";
        }

        return 'redaction error';
    }
}
