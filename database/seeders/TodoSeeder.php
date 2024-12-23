<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersIds = User::pluck('id');

        foreach ($usersIds as $userId) {
            Todo::factory(30)->create([
                'user_id' => $userId
            ]);
        }
    }
}
