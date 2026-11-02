<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['Maria Santos', 'maria@example.com', '0917-000-0001', '12 Mabini St.'],
            ['Juan Dela Cruz', 'juan@example.com', '0917-000-0002', '45 Rizal Ave.'],
            ['Ana Reyes', 'ana@example.com', '0917-000-0003', '78 Bonifacio Rd.'],
        ];

        foreach ($customers as [$name, $email, $phone, $address]) {
            Customer::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'phone' => $phone, 'address' => $address],
            );
        }
    }
}
