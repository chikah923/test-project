<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
    $tags = ['Daily Life', 'Relationships', 'Fashion', 'Issue', 'Study'];
    foreach ($tags as $tag) App\Model\Tag::create(['name' => $tag]);
    }
}
