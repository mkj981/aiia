<?php

namespace Database\Seeders;

use App\Models\BankCsvFormat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'superadmin',
            'email' => 'superadmin@aiia.com',
            'password' => bcrypt('superadmin'),
        ]);

        $bankCsvFormat = [
            [
                'name' => 'BACS Multi',
                'description' => 'A file formated to basc standard 18 for multi payment dates',
                'requires_bacs_sun' => true,
            ],
            [
                'name' => 'Bank of America BASC',
                'description' => 'A CSV file formatted to the Bank of America BASC file format',
                'requires_bacs_sun' => true,
            ],
            [
                'name' => 'Barclays (SIF/Pegasus)',
                'description' => 'A SIF/Pegasus file formatted. An alternative format for barclays',
                'requires_bacs_sun' => false,
            ],
        ];

        BankCsvFormat::insert($bankCsvFormat);
    }
}
