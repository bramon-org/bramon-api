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
}
