<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class DefaultCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Categorías Masculinas
            [
                'name' => 'Sub-12 Masculino',
                'slug' => 'sub-12-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 12 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 12,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Sub-14 Masculino',
                'slug' => 'sub-14-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 14 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 14,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sub-16 Masculino',
                'slug' => 'sub-16-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 16 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 16,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Sub-18 Masculino',
                'slug' => 'sub-18-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 18 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 18,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Sub-21 Masculino',
                'slug' => 'sub-21-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 21 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 21,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Adulto Masculino',
                'slug' => 'adulto-masculino',
                'description' => 'Categoría para jugadores masculinos adultos sin límite de edad',
                'gender' => 'male',
                'min_age' => 18,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Veterano Masculino',
                'slug' => 'veterano-masculino',
                'description' => 'Categoría para jugadores masculinos veteranos (más de 35 años)',
                'gender' => 'male',
                'min_age' => 35,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 7,
            ],

            // Categorías Femeninas
            [
                'name' => 'Sub-12 Femenino',
                'slug' => 'sub-12-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 12 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 12,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Sub-14 Femenino',
                'slug' => 'sub-14-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 14 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 14,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Sub-16 Femenino',
                'slug' => 'sub-16-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 16 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 16,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Sub-18 Femenino',
                'slug' => 'sub-18-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 18 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 18,
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Sub-21 Femenino',
                'slug' => 'sub-21-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 21 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 21,
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Adulto Femenino',
                'slug' => 'adulto-femenino',
                'description' => 'Categoría para jugadoras femeninas adultas sin límite de edad',
                'gender' => 'female',
                'min_age' => 18,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Veterano Femenino',
                'slug' => 'veterano-femenino',
                'description' => 'Categoría para jugadoras femeninas veteranas (más de 35 años)',
                'gender' => 'female',
                'min_age' => 35,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 14,
            ],

            // Categorías Mixtas
            [
                'name' => 'Sub-12 Mixto',
                'slug' => 'sub-12-mixto',
                'description' => 'Categoría mixta para jugadores hasta 12 años de edad',
                'gender' => 'mixed',
                'min_age' => null,
                'max_age' => 12,
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'name' => 'Sub-14 Mixto',
                'slug' => 'sub-14-mixto',
                'description' => 'Categoría mixta para jugadores hasta 14 años de edad',
                'gender' => 'mixed',
                'min_age' => null,
                'max_age' => 14,
                'is_active' => true,
                'sort_order' => 16,
            ],
            [
                'name' => 'Sub-16 Mixto',
                'slug' => 'sub-16-mixto',
                'description' => 'Categoría mixta para jugadores hasta 16 años de edad',
                'gender' => 'mixed',
                'min_age' => null,
                'max_age' => 16,
                'is_active' => true,
                'sort_order' => 17,
            ],
            [
                'name' => 'Sub-18 Mixto',
                'slug' => 'sub-18-mixto',
                'description' => 'Categoría mixta para jugadores hasta 18 años de edad',
                'gender' => 'mixed',
                'min_age' => null,
                'max_age' => 18,
                'is_active' => true,
                'sort_order' => 18,
            ],
            [
                'name' => 'Adulto Mixto',
                'slug' => 'adulto-mixto',
                'description' => 'Categoría mixta para jugadores adultos sin límite de edad',
                'gender' => 'mixed',
                'min_age' => 18,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 19,
            ],
            [
                'name' => 'Veterano Mixto',
                'slug' => 'veterano-mixto',
                'description' => 'Categoría mixta para jugadores veteranos (más de 35 años)',
                'gender' => 'mixed',
                'min_age' => 35,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 20,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Default volleyball categories have been seeded successfully.');
    }

    /**
     * Get categories by selected keys from the setup wizard
     */
    public static function getCategoriesByKeys(array $selectedKeys): array
    {
        $categoryMap = [
            'sub_12_masculino' => 'sub-12-masculino',
            'sub_14_masculino' => 'sub-14-masculino',
            'sub_16_masculino' => 'sub-16-masculino',
            'sub_18_masculino' => 'sub-18-masculino',
            'sub_21_masculino' => 'sub-21-masculino',
            'adulto_masculino' => 'adulto-masculino',
            'veterano_masculino' => 'veterano-masculino',
            'sub_12_femenino' => 'sub-12-femenino',
            'sub_14_femenino' => 'sub-14-femenino',
            'sub_16_femenino' => 'sub-16-femenino',
            'sub_18_femenino' => 'sub-18-femenino',
            'sub_21_femenino' => 'sub-21-femenino',
            'adulto_femenino' => 'adulto-femenino',
            'veterano_femenino' => 'veterano-femenino',
            'sub_12_mixto' => 'sub-12-mixto',
            'sub_14_mixto' => 'sub-14-mixto',
            'sub_16_mixto' => 'sub-16-mixto',
            'sub_18_mixto' => 'sub-18-mixto',
            'adulto_mixto' => 'adulto-mixto',
            'veterano_mixto' => 'veterano-mixto',
        ];

        $selectedSlugs = [];
        foreach ($selectedKeys as $key) {
            if (isset($categoryMap[$key])) {
                $selectedSlugs[] = $categoryMap[$key];
            }
        }

        return $selectedSlugs;
    }

    /**
     * Seed only selected categories
     */
    public static function seedSelectedCategories(array $selectedKeys): void
    {
        $selectedSlugs = self::getCategoriesByKeys($selectedKeys);
        
        $allCategories = [
            'sub-12-masculino' => [
                'name' => 'Sub-12 Masculino',
                'slug' => 'sub-12-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 12 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 12,
                'is_active' => true,
                'sort_order' => 1,
            ],
            'sub-14-masculino' => [
                'name' => 'Sub-14 Masculino',
                'slug' => 'sub-14-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 14 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 14,
                'is_active' => true,
                'sort_order' => 2,
            ],
            'sub-16-masculino' => [
                'name' => 'Sub-16 Masculino',
                'slug' => 'sub-16-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 16 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 16,
                'is_active' => true,
                'sort_order' => 3,
            ],
            'sub-18-masculino' => [
                'name' => 'Sub-18 Masculino',
                'slug' => 'sub-18-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 18 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 18,
                'is_active' => true,
                'sort_order' => 4,
            ],
            'sub-21-masculino' => [
                'name' => 'Sub-21 Masculino',
                'slug' => 'sub-21-masculino',
                'description' => 'Categoría para jugadores masculinos hasta 21 años de edad',
                'gender' => 'male',
                'min_age' => null,
                'max_age' => 21,
                'is_active' => true,
                'sort_order' => 5,
            ],
            'adulto-masculino' => [
                'name' => 'Adulto Masculino',
                'slug' => 'adulto-masculino',
                'description' => 'Categoría para jugadores masculinos adultos sin límite de edad',
                'gender' => 'male',
                'min_age' => 18,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 6,
            ],
            'veterano-masculino' => [
                'name' => 'Veterano Masculino',
                'slug' => 'veterano-masculino',
                'description' => 'Categoría para jugadores masculinos veteranos (más de 35 años)',
                'gender' => 'male',
                'min_age' => 35,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 7,
            ],
            'sub-12-femenino' => [
                'name' => 'Sub-12 Femenino',
                'slug' => 'sub-12-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 12 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 12,
                'is_active' => true,
                'sort_order' => 8,
            ],
            'sub-14-femenino' => [
                'name' => 'Sub-14 Femenino',
                'slug' => 'sub-14-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 14 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 14,
                'is_active' => true,
                'sort_order' => 9,
            ],
            'sub-16-femenino' => [
                'name' => 'Sub-16 Femenino',
                'slug' => 'sub-16-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 16 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 16,
                'is_active' => true,
                'sort_order' => 10,
            ],
            'sub-18-femenino' => [
                'name' => 'Sub-18 Femenino',
                'slug' => 'sub-18-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 18 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 18,
                'is_active' => true,
                'sort_order' => 11,
            ],
            'sub-21-femenino' => [
                'name' => 'Sub-21 Femenino',
                'slug' => 'sub-21-femenino',
                'description' => 'Categoría para jugadoras femeninas hasta 21 años de edad',
                'gender' => 'female',
                'min_age' => null,
                'max_age' => 21,
                'is_active' => true,
                'sort_order' => 12,
            ],
            'adulto-femenino' => [
                'name' => 'Adulto Femenino',
                'slug' => 'adulto-femenino',
                'description' => 'Categoría para jugadoras femeninas adultas sin límite de edad',
                'gender' => 'female',
                'min_age' => 18,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 13,
            ],
            'veterano-femenino' => [
                'name' => 'Veterano Femenino',
                'slug' => 'veterano-femenino',
                'description' => 'Categoría para jugadoras femeninas veteranas (más de 35 años)',
                'gender' => 'female',
                'min_age' => 35,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 14,
            ],
            'sub-12-mixto' => [
                'name' => 'Sub-12 Mixto',
                'slug' => 'sub-12-mixto',
                'description' => 'Categoría mixta para jugadores hasta 12 años de edad',
                'gender' => 'mixed',
                'min_age' => null,
                'max_age' => 12,
                'is_active' => true,
                'sort_order' => 15,
            ],
            'sub-14-mixto' => [
                'name' => 'Sub-14 Mixto',
                'slug' => 'sub-14-mixto',
                'description' => 'Categoría mixta para jugadores hasta 14 años de edad',
                'gender' => 'mixed',
                'min_age' => null,
                'max_age' => 14,
                'is_active' => true,
                'sort_order' => 16,
            ],
            'sub-16-mixto' => [
                'name' => 'Sub-16 Mixto',
                'slug' => 'sub-16-mixto',
                'description' => 'Categoría mixta para jugadores hasta 16 años de edad',
                'gender' => 'mixed',
                'min_age' => null,
                'max_age' => 16,
                'is_active' => true,
                'sort_order' => 17,
            ],
            'sub-18-mixto' => [
                'name' => 'Sub-18 Mixto',
                'slug' => 'sub-18-mixto',
                'description' => 'Categoría mixta para jugadores hasta 18 años de edad',
                'gender' => 'mixed',
                'min_age' => null,
                'max_age' => 18,
                'is_active' => true,
                'sort_order' => 18,
            ],
            'adulto-mixto' => [
                'name' => 'Adulto Mixto',
                'slug' => 'adulto-mixto',
                'description' => 'Categoría mixta para jugadores adultos sin límite de edad',
                'gender' => 'mixed',
                'min_age' => 18,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 19,
            ],
            'veterano-mixto' => [
                'name' => 'Veterano Mixto',
                'slug' => 'veterano-mixto',
                'description' => 'Categoría mixta para jugadores veteranos (más de 35 años)',
                'gender' => 'mixed',
                'min_age' => 35,
                'max_age' => null,
                'is_active' => true,
                'sort_order' => 20,
            ],
        ];

        foreach ($selectedSlugs as $slug) {
            if (isset($allCategories[$slug])) {
                Category::firstOrCreate(
                    ['slug' => $slug],
                    $allCategories[$slug]
                );
            }
        }
    }
}