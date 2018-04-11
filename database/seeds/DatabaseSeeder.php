<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(CreateUserRoles::class);
        if (env('APP_ENV') != 'production') {
            $this->call(CreateTestDBData::class);
        } else {
            $this->call(CreateAdminUser::class);
        }
    }
}
