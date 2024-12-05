<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    protected $faker;
    protected $sourceOptions = ['referral', 'website', 'agent', 'advertisement', 'other'];
    protected $occupations = [
        'Software Developer', 'Teacher', 'Doctor', 'Business Owner', 'Lawyer',
        'Accountant', 'Engineer', 'Sales Manager', 'Consultant', 'Architect',
        'Contractor', 'Real Estate Agent', 'Investor', 'Shop Owner', 'Farmer'
    ];
    
    protected $companies = [
        'Tech Solutions Ltd', 'Global Traders', 'First Capital Bank', 'Zambia Sugar',
        'Madison Insurance', 'Shoprite Zambia', 'Game Stores', 'Pick n Pay',
        'MTN Zambia', 'Airtel Zambia', 'Zambeef Products', 'Trade Kings',
        'Premium Pensions', 'Atlas Mara Bank', 'Stanbic Bank'
    ];

    protected $cities = [
        'Lusaka', 'Kitwe', 'Ndola', 'Kabwe', 'Chingola', 
        'Mufulira', 'Livingstone', 'Kasama', 'Chipata', 'Solwezi'
    ];

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have some agents
        $agentIds = User::where('role', 'agent')->pluck('id')->toArray();
        
        // If no agents exist, create some
        if (empty($agentIds)) {
            $agentIds = $this->createDefaultAgents();
        }

        // Create individual customers
        $this->createIndividualCustomers($agentIds, 50);

        // Create corporate customers
        $this->createCorporateCustomers($agentIds, 20);

        // Create some customers with specific statuses
        $this->createCustomersWithSpecificStatus($agentIds, 'inactive', 5);
        $this->createCustomersWithSpecificStatus($agentIds, 'blocked', 3);

        // Update some customers with purchase history
        $this->updateCustomersWithPurchaseHistory();
    }

    /**
     * Create default agents if none exist
     */
    protected function createDefaultAgents(): array
    {
        $agents = [];
        for ($i = 1; $i <= 5; $i++) {
            $agent = User::create([
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'role' => 'agent',
                'is_active' => true,
            ]);
            $agents[] = $agent->id;
        }
        return $agents;
    }

    /**
     * Create individual customers
     */
    protected function createIndividualCustomers(array $agentIds, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->createCustomer($agentIds, 'individual');
        }
    }

    /**
     * Create corporate customers
     */
    protected function createCorporateCustomers(array $agentIds, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->createCustomer($agentIds, 'corporate');
        }
    }

    /**
     * Create customers with specific status
     */
    protected function createCustomersWithSpecificStatus(array $agentIds, string $status, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $type = $this->faker->randomElement(['individual', 'corporate']);
            $this->createCustomer($agentIds, $type, $status);
        }
    }

    /**
     * Create a single customer
     */
    protected function createCustomer(array $agentIds, string $type, string $status = 'active'): void
    {
        $isCorporate = $type === 'corporate';
        $title = $isCorporate ? null : $this->faker->randomElement(['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.']);

        $customer = Customer::create([
            'title' => $title,
            'first_name' => $isCorporate ? $this->faker->company : $this->faker->firstName,
            'last_name' => $isCorporate ? '' : $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'alternate_phone' => $this->faker->boolean(30) ? $this->faker->phoneNumber : null,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->randomElement($this->cities),
            'state' => 'Central Province',
            'postal_code' => $this->faker->numberBetween(10000, 99999),
            'country' => 'ZM',
            'date_of_birth' => $isCorporate ? null : $this->faker->dateTimeBetween('-60 years', '-18 years'),
            'nationality' => $isCorporate ? null : 'Zambian',
            'id_type' => $isCorporate ? null : $this->faker->randomElement(['national_id', 'passport', 'drivers_license']),
            'id_number' => $isCorporate ? null : $this->faker->unique()->numerify('#########'),
            'occupation' => $isCorporate ? null : $this->faker->randomElement($this->occupations),
            'company_name' => $isCorporate ? $this->faker->randomElement($this->companies) : null,
            'type' => $type,
            'status' => $status,
            'source' => $this->faker->randomElement($this->sourceOptions),
            'notes' => $this->faker->boolean(70) ? $this->faker->text : null,
            'preferences' => $this->generatePreferences(),
            'tags' => $this->generateTags($type),
            'credit_limit' => $this->faker->randomFloat(2, 1000, 50000),
            'assigned_agent_id' => $this->faker->randomElement($agentIds),
        ]);
    }

    /**
     * Generate random preferences
     */
    protected function generatePreferences(): array
    {
        $preferences = [];
        
        if ($this->faker->boolean(70)) {
            $preferences['preferred_contact'] = $this->faker->randomElement(['email', 'phone', 'whatsapp']);
        }
        
        if ($this->faker->boolean(60)) {
            $preferences['newsletter'] = $this->faker->boolean;
        }
        
        if ($this->faker->boolean(50)) {
            $preferences['payment_terms'] = $this->faker->randomElement(['30_days', '60_days', '90_days']);
        }

        return $preferences;
    }

    /**
     * Generate random tags based on customer type
     */
    protected function generateTags(string $type): array
    {
        $tags = [];
        $possibleTags = $type === 'corporate' 
            ? ['enterprise', 'premium', 'wholesale', 'bulk-buyer', 'regular', 'credit']
            : ['retail', 'premium', 'first-time', 'regular', 'cash', 'credit'];

        $numTags = $this->faker->numberBetween(1, 3);
        shuffle($possibleTags);
        
        return array_slice($possibleTags, 0, $numTags);
    }

    /**
     * Update some customers with purchase history
     */
    protected function updateCustomersWithPurchaseHistory(): void
    {
        $customers = Customer::inRandomOrder()->limit(40)->get();

        foreach ($customers as $customer) {
            $totalPurchases = $this->faker->randomFloat(2, 5000, 500000);
            $lastPurchaseDate = $this->faker->dateTimeBetween('-6 months', 'now');

            $customer->update([
                'total_purchases' => $totalPurchases,
                'last_purchase_date' => $lastPurchaseDate,
            ]);
        }
    }
}