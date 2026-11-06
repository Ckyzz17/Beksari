<?php

namespace Database\Seeders;

use App\Models\Distributor;
use Illuminate\Database\Seeder;

class DistributorSeeder extends Seeder
{
    public function run(): void
    {
        $distributors = [
            [
                'kode' => 'DST-001',
                'nama' => 'PT Gramedia Asri Media',
                'alamat' => 'Jl. Palmerah Barat No. 29-37, Jakarta Pusat',
                'telepon' => '021-53650110',
                'email' => 'distribusi@gramedia.com',
            ],
            [
                'kode' => 'DST-002',
                'nama' => 'CV Andi Offset',
                'alamat' => 'Jl. Beo 38-40, Demangan Baru, Yogyakarta',
                'telepon' => '0274-561881',
                'email' => 'order@andipublisher.com',
            ],
            [
                'kode' => 'DST-003',
                'nama' => 'Penerbit Erlangga Group',
                'alamat' => 'Jl. H. Baping Raya No. 100, Ciracas, Jakarta Timur',
                'telepon' => '021-8717006',
                'email' => 'layanan@erlangga.co.id',
            ],
            [
                'kode' => 'DST-004',
                'nama' => 'Mizan Media Utama',
                'alamat' => 'Jl. Cinambo No. 135, Cisaranten Wetan, Bandung',
                'telepon' => '022-7834310',
                'email' => 'mizanmu@mizan.com',
            ],
        ];

        foreach ($distributors as $item) {
            Distributor::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
