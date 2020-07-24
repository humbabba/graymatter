<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Add default user
         */
        if (User::where('email', '=', 'admin@admin.com')->first() === null) {
            $newUser = User::create([
                'name'     => 'Default',
                'email'    => 'default@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => date('Y-m-d H:i:s'),
                'role' => 'admin',
            ]);
        }
    }
}
