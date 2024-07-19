<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if ($this->command->confirm('Do you wish to refresh migration before seeding, it will clear all old data ?')) {

            $this->command->call('migrate:refresh');
            $this->command->warn("Data cleared, starting from blank database.");
        }

        // create user
        $user = User::factory()->create();
        $this->command->info('Here is your test user details:');
        $this->command->warn("Id: ". $user->id);
        $this->command->warn("Email: ". $user->email);
    }
}
