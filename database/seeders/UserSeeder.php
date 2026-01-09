<?php

namespace Database\Seeders;
use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com'
        ])->assignRole(RolesEnum::User->value);

        User::factory()->create([
            'name' => 'vendor',
            'email' => 'vendor@example.com'
        ])->assignRole(RolesEnum::Vendor->value);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com'
        ])->assignRole(RolesEnum::Administerator->value);
    }
}
