<?php

use Illuminate\Database\Seeder;

class CreateAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = \App\Role::where('name', 'ADMINISTRATOR')->first();
        $user = new \App\User();
        $user->fill([
            'email' => 'admin@kmedix.com',
            'password' => bcrypt('123456'),
            'first_name' => 'Admin',
            'last_name' => '',
            'phone_number' => '+16418831920',
            'active' => 1,
            'api_token' => 'admintoken'
        ]);
        $user->save();
        $user->attachRole($role);
    }
}
