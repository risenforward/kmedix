<?php

use Illuminate\Database\Seeder;

class CreateTestDBData extends Seeder
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
            'username' => 'testadmin',
            'email' => 'test.admin@kmedix.com',
            'password' => bcrypt('123456'),
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'phone_number' => '+16418831920',
            'active' => 1,
            'api_token' => 'testadmintoken'
        ]);
        $user->save();
        $user->attachRole($role);

        $role = \App\Role::where('name', 'TECHNICAL_SUPPORT_ENGINEER')->first();
        $user = new \App\User();
        $user->fill([
            'username' => 'testteng',
            'email' => 'test.technical-eng@kmedix.com',
            'password' => bcrypt('123456'),
            'first_name' => 'Test',
            'last_name' => 'TechicalEng',
            'phone_number' => '+16418831921',
            'active' => 1,
            'api_token' => 'testtechengtoken'
        ]);
        $user->save();
        $user->attachRole($role);

        $role = \App\Role::where('name', 'CLINICAL_SUPPORT_ENGINEER')->first();
        $user = new \App\User();
        $user->fill([
            'username' => 'testceng',
            'email' => 'test.clinical-eng@kmedix.com',
            'password' => bcrypt('123456'),
            'first_name' => 'Test',
            'last_name' => 'ClinicalEng',
            'phone_number' => '+16418831922',
            'active' => 1,
            'api_token' => 'testclinengtoken'
        ]);
        $user->save();
        $user->attachRole($role);

        $role = \App\Role::where('name', 'STORE_ADMINISTRATOR')->first();
        $user = new \App\User();
        $user->fill([
            'username' => 'teststoreadmin',
            'email' => 'test.store-admin@kmedix.com',
            'password' => bcrypt('123456'),
            'first_name' => 'Test',
            'last_name' => 'StoreAdmin',
            'phone_number' => '+16418831923',
            'active' => 1,
            'api_token' => 'teststoreadmintoken'
        ]);
        $user->save();
        $user->attachRole($role);
    }
}
