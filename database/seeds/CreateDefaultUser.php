<?php

use Illuminate\Database\Seeder;

// @codingStandardsIgnoreLine
class CreateDefaultUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDefaultAdminUser();
        $this->createDefaultOperatorUser();
        $this->createDefaultEditorUser();
    }

    private function createDefaultAdminUser()
    {
        $user = new App\Models\User();

        $user->name = 'Administrator User';
        $user->email = 'admin+' . rand() . '@bramonmeteor.org';
        $user->password = $user->generatePassword();
        $user->api_token = $user->generateApiToken();
        $user->role = \App\Models\User::ROLE_ADMIN;
        $user->save();

        dump([
            'EMAIl'         => $user->email,
            'USER_TOKEN'    => $user->api_token,
        ]);
    }

    private function createDefaultOperatorUser()
    {
        $user = new App\Models\User();

        $user->name = 'Operator User';
        $user->email = 'operator+' . rand() . '@bramonmeteor.org';
        $user->password = $user->generatePassword();
        $user->api_token = $user->generateApiToken();
        $user->role = \App\Models\User::ROLE_OPERATOR;
        $user->save();

        dump([
            'EMAIl'         => $user->email,
            'USER_TOKEN'    => $user->api_token,
        ]);
    }

    private function createDefaultEditorUser()
    {
        $user = new App\Models\User();

        $user->name = 'Editor User';
        $user->email = 'editor+' . rand() . '@bramonmeteor.org';
        $user->password = $user->generatePassword();
        $user->api_token = $user->generateApiToken();
        $user->role = \App\Models\User::ROLE_EDITOR;
        $user->save();

        dump([
            'EMAIl'         => $user->email,
            'USER_TOKEN'    => $user->api_token,
        ]);
    }
}
