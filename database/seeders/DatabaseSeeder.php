<?php

namespace Database\Seeders;

use App\Models\Message;
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
        $users = User::factory()->count(15)->create();

        Message::factory()
            ->count(15)
            ->make()
            ->each(function ($message) use ($users) {
                $message->user_id = $users->random()->id;
                $message->save();
            });
    }
}
