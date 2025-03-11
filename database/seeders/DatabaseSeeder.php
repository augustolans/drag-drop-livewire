<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        foreach (['group1', 'group2', 'group3'] as $group) {
            foreach (['Item 1', 'Item 2', 'Item 3'] as $index => $name) {
                Item::create([
                    'name' => "$name - $group",
                    'position' => $index,
                    'group' => $group
                ]);
            }
        }
    }
}
