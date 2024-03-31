<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Toilettage',
            'Colliers',
            'Laisses',
            'Paniers',
            'Jouets',
            'Sacs',
            'Accessoires',
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
