<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AccountHashGenerator;
use Filament\Facades\Filament;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeUserCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for logging into Laranero admin panel';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:laranero-admin
                            {--account_hash= : The account hash for the admin account}
                            {--password= : The password for the admin account (min. 8 characters)}';

    /** @var array{'account_hash': string, 'password': string} */
    protected array $options;

    /**
     * @return array{'account_hash': string, 'password': string}
     */
    protected function getUserData(): array
    {
        $accountHash = resolve(AccountHashGenerator::class)->generate();

        $accountHash = $this->option('account_hash') ?? text(
            label: 'Account hash',
            hint: 'This will be used to login to the admin panel, leave empty to auto-generate',
            default: $accountHash,
            required: true,
            validate: fn (string $accountHash): ?string => match (true) {
                User::where('account_hash', $accountHash)->exists() => 'A user with this account hash already exists',
                strlen($accountHash) !== 8 => 'Account hash must be exactly 8 characters long',
                default => null,
            }
        );

        return [
            'account_hash' => $accountHash,
            'password' => Hash::make($this->option('password') ?? password(
                label: 'Password',
                required: true,
            )),
        ];
    }

    /**
     * Create a new user based on supplied options.
     */
    protected function createUser(): Authenticatable
    {
        return User::create($this->getUserData());
    }

    /**
     * Notify the caller of success.
     */
    protected function sendSuccessMessage(Authenticatable $user): void
    {
        $loginUrl = Filament::getLoginUrl();

        $this->components->info('Success! ' . ($user->getAttribute('account_hash') ?? 'You') . " may now log in at {$loginUrl}");
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->options = $this->options();

        if (! Filament::getCurrentPanel()) {
            $this->error('Filament has not been installed yet: php artisan filament:install --panels');

            return static::INVALID;
        }

        $user = $this->createUser();
        $this->sendSuccessMessage($user);

        return static::SUCCESS;
    }
}
