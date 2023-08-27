<?php

namespace Database\Seeders;

use App\Models\Manager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [1922, 'Гурина Анфиса Алексеевна'],
            [115700, 'Задыхина Ирина Алексеевна'],
        ];
        $count = count($data);
        $m_count = Manager::count();

        for ($i = $m_count; $i < $count; $i++) {
            Manager::create([
                'crm_id' => $data[$i][0],
                'name' => $data[$i][1],
            ]);
        }
    }
}