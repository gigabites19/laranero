<?php

use App\Exceptions\GenerationMaxAttemptReached;
use App\Services\AccountHashGenerator;
use App\Models\User;
use Mockery\MockInterface;

test('it should generate an account hash', function () {
    $accountHash = (new AccountHashGenerator)->generate();

    expect($accountHash)
        ->toHaveLength(8)
        ->toBeString();
});

test('it should throw an exception if an unique hash can not be generated', function () {
    $accountHash = '8CH8CH8C';
    // Set up a mock that makes `generateAccountHash` always return `$accountHash`
    $this->instance(
        AccountHashGenerator::class,
        Mockery::mock(AccountHashGenerator::class, function (MockInterface $mock) use ($accountHash) {
            $mock
                ->makePartial()
                ->shouldReceive('generateAccountHash')
                ->andReturn($accountHash);
        }),
    );

    // Make user with that has already exist
    User::factory()->create();

    $this->expectException(GenerationMaxAttemptReached::class);
    // This should fail because unique account hash generation will be impossible
    User::factory()->create();
});
