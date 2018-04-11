<?php

use Illuminate\Database\Seeder;

class CreateUserRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(\App\Role::$roles as $role => $name) {
            $r = new \App\Role();
            $r->name = $role;
            $r->display_name = $name;
            $r->save();
        }
    }
}
