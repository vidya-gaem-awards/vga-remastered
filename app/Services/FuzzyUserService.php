<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class FuzzyUserService
{
    private string $ip;
    private string $cookieId;
    private ?string $votingCode = null;

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function setCookieId(string $cookieId): void
    {
        $this->cookieId = $cookieId;
    }

    public function cookieId(): string
    {
        return $this->cookieId;
    }

    public function id(): string
    {
        if (Auth::user()) {
            return 'user_' . Auth::user()->id;
        }
        return 'ip_' . $this->ip;
    }

    public function setVotingCode(?string $votingCode): void
    {
        $this->votingCode = $votingCode;
    }

    public function votingCode(): ?string
    {
        return $this->votingCode;
    }
}
