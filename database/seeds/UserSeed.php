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

        // Let's clear the users table first
//        \App\User::truncate();

        $faker = \Faker\Factory::create();

        // Let's make sure everyone has the same password and
        // let's hash it before the loop, or else our seeder
        // will be too slow.
        $password = Hash::make('mad@2019');

        \App\User::create([
            'name' => 'Phillip Madsen',
            'email' => 'wecodelaravel@gmail.com',
            'password' => $password,
        ]);

        \App\User::create([
            'name' => 'Phillip Madsen',
            'email' => 'phillip.madsen@sling.com',
            'password' => $password,
        ]);

        $mark_password = Hash::make('mark@2019');

        \App\User::create([
            'name' => 'Mark Hurst',
            'email' => 'mark.hurst@sling.com',
            'password' => $mark_password,
        ]);

        $drew_password = Hash::make('drew@2019');

        \App\User::create([
            'name' => 'Drew Major',
            'email' => 'drew.major@sling.com',
            'password' => $drew_password,
        ]);

        $adam_password = Hash::make('adam@2019');

        \App\User::create([
            'name' => 'Adam Harral',
            'email' => 'adam.harral@sling.com',
            'password' => $adam_password,
        ]);

        $jorg_password = Hash::make('jorg@2019');

        \App\User::create([
            'name' => 'Jorg Nonnenmacher',
            'email' => 'jorg.nonnenmacher@sling.com',
            'password' => $jorg_password,
        ]);

        $katie_password = Hash::make('katie@2019');

        \App\User::create([
            'name' => 'Katie Stankiewicz',
            'email' => 'Katie.Stankiewicz@sling.com',
            'password' => $katie_password,
        ]);

        // And now let's generate a few dozen users for our app:
        for ($i = 0; $i < 10; $i++) {
            // \App\User::create([
            //     'name' => $faker->name,
            //     'email' => $faker->email,
            //     'password' => $password,
            // ]);
        }
    }
}
