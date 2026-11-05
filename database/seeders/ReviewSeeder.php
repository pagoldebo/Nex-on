<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::take(5)->get();
        if ($products->isEmpty()) {
            return;
        }

        $reviewsData = [
            [
                'customer_name' => 'Sunita Kulkarni',
                'city'          => 'Mumbai',
                'rating'        => 5,
                'title'         => 'Pure & authentic quality',
                'comment'       => "The freshness and quality took me right back to our traditional farm roots. You can feel the purity and meticulous care in every single harvest and stitch.",
                'is_approved'   => true,
            ],
            [
                'customer_name' => 'Dr. Rajesh Menon',
                'city'          => 'Pune',
                'rating'        => 5,
                'title'         => 'Remarkable standards',
                'comment'       => "We switched to their certified organic essentials 6 months ago. The aroma is heavenly and the authentic quality has genuinely improved our daily lifestyle.",
                'is_approved'   => true,
            ],
            [
                'customer_name' => 'Ananya Patel',
                'city'          => 'Bengaluru',
                'rating'        => 5,
                'title'         => 'Punctual & eco-friendly',
                'comment'       => "Express door delivery is always punctual and the packaging is completely eco-friendly with zero single-use plastics. Highly recommend to everyone!",
                'is_approved'   => true,
            ],
            [
                'customer_name' => 'Priya Sharma',
                'city'          => 'New Delhi',
                'rating'        => 5,
                'title'         => 'Exceptional comfort & fit',
                'comment'       => "Heirloom craftsmanship and breathable, skin-friendly fabrics. Delicate yet remarkably durable for everyday elegance.",
                'is_approved'   => true,
            ],
        ];

        foreach ($reviewsData as $index => $data) {
            $product = $products[$index % $products->count()];
            ProductReview::updateOrCreate(
                [
                    'customer_name' => $data['customer_name'],
                    'product_id'    => $product->id,
                ],
                array_merge($data, ['product_id' => $product->id])
            );
        }
    }
}

