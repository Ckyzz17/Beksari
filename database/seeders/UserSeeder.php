<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['username' => 'admin_user'],
            [
                'name' => 'Admin Utama',
                'email' => 'admin@smartbooks.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'avatar_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAkU8YNaCiYs5ECFJzmi8UTGfcTUemxeLHtOQBa1Jxo5KT570DOPUswQevQKLFYN8pdsPHr15nNRCRNOBVYGpI3TQi6Kz86PezUkug5DMk7iiRtUTxrsCRR3dYbauszdnbgoo0JVvGKhAzDZeXEFNypvWr9CU_651AxqtS3ed9V7nUy10SDnBMIjEIZfKSOSd-Tpv5zYb_GlhwyPUuyTp4TbTZ_x5BjoEMm2bGZx4YcbL65b4vA5_k',
            ]
        );

        // Kasir 1
        User::updateOrCreate(
            ['username' => 'kasir_user'],
            [
                'name' => 'Siti Aminah',
                'email' => 'kasir@smartbooks.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'avatar_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCer6pyLSfB2N2t7rXaLCslkDDF3CRjuAANnITU65SJmb12CVOViygt160xub6058llUBlVRpO94PV3JFU6lu3cmWL31npAd34kjswZAPbEyJMyqXiNZfhqNQU179Epq3Y3vbWRDv959KYnNPQTi1VXwEdds_NTZm6DnyR5xi35dcPp3DYYJ4sgBBpeW0ToFLBKxbQ4djwV9Ed_eIq2N1FRdOckQJlRam5ifrCmILudN0KZ6lAsGtI',
            ]
        );

        // Kasir 2
        User::updateOrCreate(
            ['username' => 'kasir2'],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@smartbooks.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'avatar_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA2Eh_vq1ns-bgOpyZM74OuiJ1MpyceBtOvkhcsGpniwdSBXpAFcxTVTWY6WaF6Bofdv5WqmjeU_-a6sben5dpD0m6jYFYgCOxb2ieEY_y9SnnhMpYUE3VOMlm7LcPxSfqckqoAs-8WSjrLrdpDnFcWS2PkXKQWjjLPCyySf3H74rZiqBnO7Pa6LYgfS7M-C7uPN1CD3iAB2CU7qN6AJXtp-2M37VtpE16EG9bjZt-MijrunaTUNRM',
            ]
        );

        // Pelanggan
        User::updateOrCreate(
            ['username' => 'pelanggan_user'],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@example.com',
                'password' => Hash::make('pelanggan123'),
                'role' => 'pelanggan',
            ]
        );

        // Kurir
        User::updateOrCreate(
            ['username' => 'kurir_smartbooks'],
            [
                'name' => 'Kurir SMART BOOKS',
                'email' => 'kurir@smartbooks.com',
                'password' => Hash::make('kurir123'),
                'role' => 'kurir',
            ]
        );
    }
}
