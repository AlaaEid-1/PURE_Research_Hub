<?php

namespace Database\Seeders;

use App\Models\ResearchCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Artificial Intelligence',
                'description' => 'Research papers on neural networks, natural language processing, computer vision, and cognitive computing.',
            ],
            [
                'name' => 'Machine Learning',
                'description' => 'Supervised, unsupervised, reinforcement learning algorithms, optimization, and predictive modeling.',
            ],
            [
                'name' => 'Computer Science',
                'description' => 'Algorithms, data structures, software engineering, systems architecture, and theory of computation.',
            ],
            [
                'name' => 'Cyber Security',
                'description' => 'Cryptography, network defense, threat intelligence, vulnerability analysis, and privacy preserving protocols.',
            ],
            [
                'name' => 'Data Science',
                'description' => 'Big data analytics, data mining, statistical learning, data visualization, and data pipeline architectures.',
            ],
            [
                'name' => 'Medicine',
                'description' => 'Clinical studies, biomedical research, pharmacology, immunology, public health, and diagnostics.',
            ],
            [
                'name' => 'Engineering',
                'description' => 'Electrical, mechanical, civil, chemical, and aerospace engineering innovations.',
            ],
            [
                'name' => 'Physics',
                'description' => 'Theoretical physics, quantum mechanics, astrophysics, condensed matter, and optics.',
            ],
            [
                'name' => 'Mathematics',
                'description' => 'Pure and applied mathematics, algebra, analysis, geometry, probability, and numerical methods.',
            ],
            [
                'name' => 'Business',
                'description' => 'Finance, economics, marketing, strategic management, organizational behavior, and entrepreneurship.',
            ],
            [
                'name' => 'Social Sciences',
                'description' => 'Psychology, sociology, political science, anthropology, education, and human geography.',
            ],
        ];

        foreach ($categories as $category) {
            ResearchCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ]
            );
        }
    }
}
