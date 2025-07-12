<?php

namespace App\Services;

use App\Exceptions\GenerationMaxAttemptReached;
use App\Models\User;
use Illuminate\Support\Str;

class AccountHashGenerator
{
    private const int MAX_ATTEMPTS = 100;
    private const int ACCOUNT_HASH_LENGTH = 8;

    public function generateAccountHash(): string
    {
        return Str::random(static::ACCOUNT_HASH_LENGTH);
    }

    public function generate(): string
    {
        $attempt = 0;

        do {
            $accountHash = $this->generateAccountHash();

            if ($attempt >= self::MAX_ATTEMPTS) {
                throw new GenerationMaxAttemptReached;
            }

            $attempt++;
        } while (User::where('account_hash', $accountHash)->exists());

        return $accountHash;
    }
}
