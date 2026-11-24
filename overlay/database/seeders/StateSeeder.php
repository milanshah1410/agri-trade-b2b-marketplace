<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Indian states and union territories with ISO 3166-2:IN codes and GST state codes.
     * Reference data: have your CA confirm the GST codes against the official list before go-live.
     */
    public function run(): void
    {
        $rows = [
            // [name, iso code, gst code, type]
            ['Andaman and Nicobar Islands', 'AN', '35', 'ut'],
            ['Andhra Pradesh', 'AP', '37', 'state'],
            ['Arunachal Pradesh', 'AR', '12', 'state'],
            ['Assam', 'AS', '18', 'state'],
            ['Bihar', 'BR', '10', 'state'],
            ['Chandigarh', 'CH', '04', 'ut'],
            ['Chhattisgarh', 'CG', '22', 'state'],
            ['Dadra and Nagar Haveli and Daman and Diu', 'DH', '26', 'ut'],
            ['Delhi', 'DL', '07', 'ut'],
            ['Goa', 'GA', '30', 'state'],
            ['Gujarat', 'GJ', '24', 'state'],
            ['Haryana', 'HR', '06', 'state'],
            ['Himachal Pradesh', 'HP', '02', 'state'],
            ['Jammu and Kashmir', 'JK', '01', 'ut'],
            ['Jharkhand', 'JH', '20', 'state'],
            ['Karnataka', 'KA', '29', 'state'],
            ['Kerala', 'KL', '32', 'state'],
            ['Ladakh', 'LA', '38', 'ut'],
            ['Lakshadweep', 'LD', '31', 'ut'],
            ['Madhya Pradesh', 'MP', '23', 'state'],
            ['Maharashtra', 'MH', '27', 'state'],
            ['Manipur', 'MN', '14', 'state'],
            ['Meghalaya', 'ML', '17', 'state'],
            ['Mizoram', 'MZ', '15', 'state'],
            ['Nagaland', 'NL', '13', 'state'],
            ['Odisha', 'OD', '21', 'state'],
            ['Puducherry', 'PY', '34', 'ut'],
            ['Punjab', 'PB', '03', 'state'],
            ['Rajasthan', 'RJ', '08', 'state'],
            ['Sikkim', 'SK', '11', 'state'],
            ['Tamil Nadu', 'TN', '33', 'state'],
            ['Telangana', 'TG', '36', 'state'],
            ['Tripura', 'TR', '16', 'state'],
            ['Uttar Pradesh', 'UP', '09', 'state'],
            ['Uttarakhand', 'UK', '05', 'state'],
            ['West Bengal', 'WB', '19', 'state'],
        ];

        foreach ($rows as [$name, $code, $gstCode, $type]) {
            State::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'gst_code' => $gstCode, 'type' => $type],
            );
        }
    }
}
