<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        Message::factory()->count(100)->create([
            'sender_id' => fn() => $users->random()->id,
            'receiver_id' => function () use ($users) {
                return $users->where('id', '!=', request('sender_id'))->random()->id;
            }
        ]);
    }
}
