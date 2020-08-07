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
         * Add default users
         */
          if (User::where('email', '=', 'default@example.com')->first() === null) {
              $newUser = User::create([
                  'name'     => 'Default',
                  'email'    => 'default@example.com',
                  'password' => bcrypt('password'),
                  'email_verified_at' => date('Y-m-d H:i:s'),
                  'role' => 'admin',
              ]);
          }
           if (User::where('email', '=', 'joe@example.com')->first() === null) {
               $newUser = User::create([
                   'name'     => 'Joe Bloe',
                   'email'    => 'joe@example.com',
                   'password' => bcrypt('password'),
                   'email_verified_at' => date('Y-m-d H:i:s'),
                   'role' => 'user',
               ]);
          }
          if (User::where('email', '=', 'jane@example.com')->first() === null) {
              $newUser = User::create([
                  'name'     => 'Jane Doe',
                  'email'    => 'jane@example.com',
                  'password' => bcrypt('password'),
                  'email_verified_at' => date('Y-m-d H:i:s'),
                  'role' => 'contributor',
              ]);
          }
    }
}
