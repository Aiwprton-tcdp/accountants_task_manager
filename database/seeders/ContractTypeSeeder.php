<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Договор аренды помещений',
            'Договор на интернет-связь',
        ];
        $count = count($data);
        $ct_count = ContractType::count();

        for ($i = $ct_count; $i < $count; $i++) {
            ContractType::create([
                'value' => $data[$i],
            ]);
        }
    }
}