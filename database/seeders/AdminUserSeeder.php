<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the default administrator account.
     * Idempotent — safe to run multiple times.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@purehub.com'],
            [
                'name'     => 'PURE Administrator',
                'password' => Hash::make('Admin@123456'),
                'role'     => Role::ADMIN,
            ]
        );
    }
}
