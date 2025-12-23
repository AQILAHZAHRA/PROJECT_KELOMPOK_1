<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create admin user';

    public function handle()
    {
        $admin = User::create([
            'name' => 'Pengelola BSU Mekar Swadaya',
            'email' => 'pengelola@gmail.com',
            'password' => Hash::make('pengelola123'),
            'role' => 'pengelola',
            'no_hp' => '081234567890',
            'alamat' => 'Kantor BSU Mekar Swadaya',
            'saldo' => 0,
        ]);

        $this->info('Admin user created successfully!');
        $this->info('Email: pengelola@gmail.com');
        $this->info('Password: pengelola123');
        $this->info('Role: pengelola');
        
        return 0;
    }
}
