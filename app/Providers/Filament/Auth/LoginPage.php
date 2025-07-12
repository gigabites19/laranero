<?php

namespace App\Providers\Filament\Auth;

use Filament\Forms;
use Filament\Pages\Auth\Login as BaseLogin;

class LoginPage extends BaseLogin
{
    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('account_hash')
                ->required()
                ->extraInputAttributes(['tabindex'=> 1]),
            $this->getPasswordFormComponent(),
        ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'account_hash' => $data['account_hash'],
            'password' => $data['password'],
        ];
    }
}
