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
      'name' => $this->faker->name(),
      'email' => 'admin@mail.com',
      'email_verified_at' => now(),
      'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    ]);

    // create 3 branches
    for ($i = 0; $i < 3; $i++) {
      Branch::create([
        'name' => $this->faker->name(),
        'address' => $this->faker->address(),
        'description' => $this->faker->text(),
      ]);
    }

    // call product seeder for creating products and stocks for all branches
    $this->call([ProductsSeeder::class, StockSeeder::class]);
  }
}
