<?php

namespace Database\Seeders;

use App\Models\Island;
use App\Models\Listing;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $maafushi = Island::firstOrCreate(
            ['slug' => 'maafushi'],
            ['name' => 'Maafushi', 'atoll' => 'Kaafu', 'is_pilot' => true,
             'description' => 'Maldives\' most popular guesthouse island. White-sand beaches, easy day trips, and a vibrant local community.'],
        );

        $thulusdhoo = Island::firstOrCreate(
            ['slug' => 'thulusdhoo'],
            ['name' => 'Thulusdhoo', 'atoll' => 'Kaafu', 'is_pilot' => true,
             'description' => 'Famous for surfing and home of Coca-Cola Bottling Plant Maldives. Laid-back guesthouse scene.'],
        );

        $tourist = User::firstOrCreate(
            ['email' => 'tourist@afterarrival.test'],
            [
                'name' => 'Sarah Visitor',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TOURIST,
                'phone' => '+960 7000000',
                'currency_preference' => 'USD',
                'current_island_id' => $maafushi->id,
                'trip_start' => now()->subDays(2),
                'trip_end' => now()->addDays(5),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@afterarrival.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        $sampleProviders = [
            // Eat
            ['name' => 'Reef Cafe', 'category' => 'eat', 'business_name' => 'Reef Cafe Maafushi',
             'listings' => [
                ['title' => 'Maldivian Tuna Curry Set', 'price' => 145, 'type' => 'instant', 'desc' => 'House special: short-eats and a tuna-coconut curry with garudhiya broth, served with rice and roshi.'],
                ['title' => 'Fresh Lobster (per 100g)', 'price' => 280, 'type' => 'scheduled', 'desc' => 'Caught same day. Choose grilled or pan-seared with garlic butter. 30-min prep.'],
                ['title' => 'Sunset Smoothie Bowl', 'price' => 95, 'type' => 'instant', 'desc' => 'Mango, banana, papaya, with toasted coconut and chia. Perfect for a beach morning.'],
             ]],
            ['name' => 'Aishath\'s Home Kitchen', 'category' => 'eat', 'business_name' => 'Aishath\'s Home Kitchen',
             'listings' => [
                ['title' => 'Traditional Maldivian Dinner', 'price' => 220, 'type' => 'scheduled', 'desc' => 'Five-course home-cooked dinner: mas huni, garudhiya, fish curry, rice, and bondibaiy dessert. Order 4 hours ahead.'],
                ['title' => 'Bajiya & Hedhikaa Box', 'price' => 75, 'type' => 'instant', 'desc' => 'Traditional Maldivian short eats — bajiya, gulha, kulhi boakibaa. Box of 12 pieces.'],
             ]],
            // Wash
            ['name' => 'Atoll Laundry', 'category' => 'wash', 'business_name' => 'Atoll Laundry Maafushi',
             'listings' => [
                ['title' => 'Standard Laundry (per kg)', 'price' => 60, 'type' => 'scheduled', 'desc' => 'Wash + dry + fold. Drop before 10am, pick up by 5pm same day. Minimum 1kg.'],
                ['title' => 'Express Laundry (per kg)', 'price' => 95, 'type' => 'scheduled', 'desc' => 'Same-day in 4 hours. Wash + dry + fold + ironing included.'],
                ['title' => 'Delicate Hand Wash (per item)', 'price' => 40, 'type' => 'scheduled', 'desc' => 'For silk, swimwear, or items needing extra care. 24-hour turnaround.'],
             ]],
            // Buy
            ['name' => 'Coral Crafts', 'category' => 'buy', 'business_name' => 'Coral Crafts',
             'listings' => [
                ['title' => 'Handwoven Coconut-Leaf Hat', 'price' => 180, 'type' => 'instant', 'desc' => 'Made by local artisans from young coconut leaves. Traditional Maldivian craft. Pickup at shop.'],
                ['title' => 'Lacquered Wooden Bowl', 'price' => 450, 'type' => 'instant', 'desc' => 'Hand-turned and lacquered using the traditional liyelaajehun technique. Signature Maldivian craft.'],
                ['title' => 'Coconut-Shell Earrings (pair)', 'price' => 120, 'type' => 'instant', 'desc' => 'Hand-carved from local coconut shells. Each pair unique.'],
             ]],
            // Experience
            ['name' => 'Blue Reef Adventures', 'category' => 'experience', 'business_name' => 'Blue Reef Adventures',
             'listings' => [
                ['title' => 'Snorkel with Sea Turtles', 'price' => 600, 'type' => 'experience', 'desc' => 'Guided 3-hour snorkel trip to a turtle hotspot. Gear included. Group of up to 6.'],
                ['title' => 'Sunset Dolphin Cruise', 'price' => 750, 'type' => 'experience', 'desc' => 'Two-hour dhoni cruise to find spinner-dolphin pods. Refreshments included.'],
                ['title' => 'Sandbank Picnic', 'price' => 1200, 'type' => 'experience', 'desc' => 'Half-day trip to an uninhabited sandbank with packed lunch, snorkel gear, and beach setup.'],
             ]],
            ['name' => 'Maldivian Stories', 'category' => 'experience', 'business_name' => 'Maldivian Stories Cultural Workshops',
             'listings' => [
                ['title' => 'Cooking Class: Traditional Maldivian Curry', 'price' => 850, 'type' => 'experience', 'desc' => '3-hour hands-on class with a local chef. Prepare and eat a full Maldivian meal. Recipe card included.'],
                ['title' => 'Dhivehi Language Taster', 'price' => 350, 'type' => 'experience', 'desc' => '90-minute conversational Dhivehi session — learn 30 useful phrases for your trip.'],
                ['title' => 'Island Walking Tour', 'price' => 400, 'type' => 'experience', 'desc' => 'Two-hour walk with a local guide. History, mosque visit (respectful dress required), fishing village stops.'],
             ]],
        ];

        foreach ($sampleProviders as $i => $data) {
            $providerUser = User::firstOrCreate(
                ['email' => 'provider'.($i+1).'@afterarrival.test'],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_PROVIDER,
                    'currency_preference' => 'MVR',
                ]
            );

            $provider = Provider::firstOrCreate(
                ['slug' => Str::slug($data['business_name'])],
                [
                    'user_id' => $providerUser->id,
                    'island_id' => $maafushi->id,
                    'business_name' => $data['business_name'],
                    'category' => $data['category'],
                    'description' => 'Local provider on '.$maafushi->name.'.',
                    'national_id' => 'A'.rand(100000, 999999),
                    'business_registration' => 'BR'.rand(10000, 99999),
                    'bml_account_number' => '7730'.rand(10000000, 99999999),
                    'verification_status' => 'verified',
                    'is_active' => true,
                    'rating' => round(4 + (rand(0, 90) / 100), 2),
                    'review_count' => rand(8, 60),
                ]
            );

            foreach ($data['listings'] as $listing) {
                $slug = Str::slug($listing['title']);
                Listing::updateOrCreate(
                    ['provider_id' => $provider->id, 'slug' => $slug],
                    [
                        'island_id' => $maafushi->id,
                        'title' => $listing['title'],
                        'category' => $data['category'],
                        'type' => $listing['type'],
                        'description' => $listing['desc'],
                        'price_mvr' => $listing['price'],
                        'price_usd' => round($listing['price'] / 15.42, 2),
                        'image_url' => '/images/listings/'.$slug.'.webp',
                        'is_active' => true,
                        'lead_time_minutes' => $listing['type'] === 'instant' ? 0 : ($listing['type'] === 'experience' ? 240 : 60),
                        'rating' => round(4 + (rand(0, 90) / 100), 2),
                        'review_count' => rand(3, 35),
                    ]
                );
            }
        }
    }
}
