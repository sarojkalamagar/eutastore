<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubscriptionPlan::query()->whereIn('id', [1, 2])->delete();
        SubscriptionPlan::query()->create([
            'id' => 1,
            'name' => 'Annual fee',
            'amount' => 50.0,
            'currency' => 'usd',
            'billing_interval' => 'annual',
            'description' => 'Annual fee is charged every year from the date of subscription.'
        ]);
        SubscriptionPlan::query()->create([
            'id' => 2,
            'name' => 'Monthly fee',
            'amount' => 5.0,
            'currency' => 'usd',
            'billing_interval' => 'every-30-days',
            'description' => 'Monthly fee is charged every 30 days from the date of subscription.'
        ]);
    }
}
