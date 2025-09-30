<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        \App\Models\User::truncate();
        \App\Models\Tenant::truncate();

        \App\Models\User::factory()->create([
            'name' => 'Super admin',
            'email' => 'admin@gmail.com'
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
