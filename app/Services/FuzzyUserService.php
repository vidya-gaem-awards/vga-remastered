<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class FuzzyUserService
{
    private string $randomId;
    private ?string $votingCode = null;

    public function setRandomId(string $randomId): void
    {
        $this->randomId = $randomId;
    }

    public function id(): string
    {
        if (Auth::user()) {
            return 'user_' . Auth::user()->id;
        }
        return 'ip_' . $this->randomId;
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
