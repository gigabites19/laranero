<?php

namespace App\Services;

use App\Exceptions\GenerationMaxAttemptReached;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Generates unique account hashes for users.
 *
 * Ensures generated hashes do not collide with existing users'
 * account hashes, retrying a number of times before throwing an exception.
 */
class AccountHashGenerator
{
    private const int MAX_ATTEMPTS = 100;
    private const int ACCOUNT_HASH_LENGTH = 8;

    /**
     * Generate a random string that has a length
     * equal to ACCOUNT_HASH_LENGTH.
     */
    public function generateAccountHash(): string
    {
        return Str::random(static::ACCOUNT_HASH_LENGTH);
    }

    /**
     * Generate a unique account hash and ensure that the account hash
     * does not already exist.
     *
     * @throws GenerationMaxAttemptReached When account hash generation fails after multiple attempts
     */
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
