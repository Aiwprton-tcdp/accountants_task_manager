<?php

namespace Database\Seeders;

use App\Models\LLC;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LLCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['Абсолют', 115700],
            ['Финансово-юридический центр', 115700],
            ['Юридическое право', 1922],
            ['Юрист для людей', 1922],
            ['Ваш юрист', 1922],
            ['Максимум', 1922],
            ['Нужные люди', 115700],
        ];
        $names_count = count($data);
        $llc_count = LLC::count();

        for ($i = $llc_count; $i < $names_count; $i++) {
            LLC::create([
                'name' => $data[$i][0],
                'manager_id' => $data[$i][1],
            ]);
        }
    }
}
