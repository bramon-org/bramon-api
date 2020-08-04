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

        $user->name = 'BRAMON admin';
        $user->email = 'admin+' . rand() . '@bramonmeteor.org';
        $user->password = '123123123';
        $user->api_token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9';
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

        $user->name = 'BRAMON admin';
        $user->email = 'admin+' . rand() . '@bramonmeteor.org';
        $user->password = '123123123';
        $user->api_token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ8';
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

        $user->name = 'BRAMON admin';
        $user->email = 'admin+' . rand() . '@bramonmeteor.org';
        $user->password = '123123123';
        $user->api_token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ7';
        $user->role = \App\Models\User::ROLE_EDITOR;
        $user->save();

        dump([
            'EMAIl'         => $user->email,
            'USER_TOKEN'    => $user->api_token,
        ]);
    }
}
