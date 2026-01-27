<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get all businesses
        $businesses = \App\Models\Business::all();

        foreach ($businesses as $business) {
            // Create default categories
            $categories = [
                ['name' => 'Food & Beverage', 'name_bn' => 'খাদ্য ও পানীয়', 'icon' => '🍔', 'sort_order' => 1],
                ['name' => 'Electronics', 'name_bn' => 'ইলেকট্রনিক্স', 'icon' => '📱', 'sort_order' => 2],
                ['name' => 'Clothing', 'name_bn' => 'পোশাক', 'icon' => '👕', 'sort_order' => 3],
                ['name' => 'Cosmetics', 'name_bn' => 'প্রসাধনী', 'icon' => '💄', 'sort_order' => 4],
                ['name' => 'Stationery', 'name_bn' => 'স্টেশনারি', 'icon' => '📝', 'sort_order' => 5],
                ['name' => 'Grocery', 'name_bn' => 'মুদি', 'icon' => '🛒', 'sort_order' => 6],
                ['name' => 'Medicine', 'name_bn' => 'ওষুধ', 'icon' => '💊', 'sort_order' => 7],
                ['name' => 'Others', 'name_bn' => 'অন্যান্য', 'icon' => '📦', 'sort_order' => 99],
            ];

            foreach ($categories as $category) {
                Category::create(array_merge(['business_id' => $business->id], $category));
            }

            // Create default payment methods
            $paymentMethods = [
                ['name' => 'Cash', 'name_bn' => 'নগদ', 'type' => 'cash', 'icon' => '💵', 'sort_order' => 1],
                ['name' => 'Card', 'name_bn' => 'কার্ড', 'type' => 'card', 'icon' => '💳', 'sort_order' => 2],
                ['name' => 'bKash', 'name_bn' => 'বিকাশ', 'type' => 'mobile_banking', 'icon' => '📱', 'sort_order' => 3],
                ['name' => 'Nagad', 'name_bn' => 'নগদ', 'type' => 'mobile_banking', 'icon' => '📱', 'sort_order' => 4],
                ['name' => 'Rocket', 'name_bn' => 'রকেট', 'type' => 'mobile_banking', 'icon' => '🚀', 'sort_order' => 5],
                ['name' => 'Bank Transfer', 'name_bn' => 'ব্যাংক ট্রান্সফার', 'type' => 'bank_transfer', 'icon' => '🏦', 'sort_order' => 6],
            ];

            foreach ($paymentMethods as $method) {
                PaymentMethod::create(array_merge(['business_id' => $business->id], $method));
            }
        }
    }
}
