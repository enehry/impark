<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // \App\Models\User::factory(10)->create();

    // \App\Models\User::factory()->create([
    //     'name' => 'Test User',
    //     'email' => 'test@example.com',
    // ]);

    // create user
    User::create([
      'name' => "Juan Dela Cruz",
      'email' => 'admin@mail.com',
      'email_verified_at' => now(),
      'role' => 1,
      'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    ]);

    // create 3 branches
    for ($i = 1; $i <= 3; $i++) {
      Branch::create([
        'name' => "Branch $i",
        'address' => "Address $i",
        'description' => "Description $i",
      ]);
    }

    // create 3 users

    // call product seeder for creating products and stocks for all branches
    // $this->call([ProductSeeder::class, StockSeeder::class]);
  }
}
