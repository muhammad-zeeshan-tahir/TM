<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $employee = Role::create(["name"=>'employee']);
        $admin = Role::create(["name"=>'admin']);
        Permission::create(["name"=>'create users']);
        Permission::create(["name"=>'edit users']);
        Permission::create(["name"=>'delete users']);
        Permission::create(["name"=>'create tasks']);
        Permission::create(["name"=>'edit tasks']);
        Permission::create(["name"=>'delete tasks']);

        Role::where('name','admin')->first()->syncPermissions([
            'create users',
            'edit users',
            'delete users',
            'create tasks',
            'edit tasks',
            'delete tasks'
        ]);

        $this->call(UsersTableSeeder::class);

    }
}
