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
            'Manteaux',
            'Croquettes',
            'Snacks',
            'Plaids',
            'San Bernard',
            'Thermes Argos'
        ];

        $colors = ['bg-red-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500', 'bg-indigo-500', 'bg-purple-500', 'bg-pink-500'];

        foreach ($categories as $key => $category) {
            Category::create([
                'name' => $category,
                'color' => $colors[$key % count($colors)], // Attribue une couleur à chaque catégorie
            ]);
        }
    }
}
