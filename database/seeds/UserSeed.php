<?php

use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            
            ['id' => 1, 'name' => 'Phillip Madsen', 'email' => 'wecodelaravel@gmail.com', 'password' => '$2y$10$m6FBVQs.2yH0xLnacOCsW.TubyVPk6NXVp7UKqq6D0lxRkcMt.Gpi', 'remember_token' => '',],

        ];

        foreach ($items as $item) {
            \App\User::create($item);
        }
    }
}
