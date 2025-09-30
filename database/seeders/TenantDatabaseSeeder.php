<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

use Spatie\Permission\Models\Role;

use App\Models\Tenant\User;

use App\Models\Tenant\FamilyHistory;
use App\Models\Tenant\MedicalConditions;
use App\Models\Tenant\Medicine;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant's database.
     *
     * @return void
     */
    public function run()
    {

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::create(['guard_name' => 'api', 'name' => 'Administrator']);

        $roles = [
            ['name' => 'Provider'],
            ['name' => 'Wellness coach'],
            ['name' => 'Office staff'],
            ['name' => 'Nutrition'],
        ];
        
        foreach($roles as $role) {
            Role::create(['guard_name' => 'api', 'name' => $role['name']]);
        }

        $user = User::first();
        $user->assignRole($adminRole);

        $familyHistoryData = [
            ['name'=> 'High Blood Pressure', 'codes'=> ["hpb"]],
            ['name'=> 'High cholesterol', 'codes'=> ["hc"]],
            ['name'=> 'Diabetes', 'codes'=> ["diabetes"]],
            ['name'=> 'Thyroid conditions', 'codes'=> []],
            ['name'=> 'Stroke', 'codes'=> []],
            ['name'=> 'Depression', 'codes'=> ["mental"]],
            ['name'=> 'Pregnancy Complications', 'codes'=> []],
            ['name'=> 'Alcoholism', 'codes'=> []],
            ['name'=> 'Drug Addition', 'codes'=> []],
            ['name'=> 'Smoking', 'codes'=> []],
            ['name'=> 'Heart attack', 'codes'=> []],
            ['name'=> 'Heart disease', 'codes'=> []],
            ['name'=> 'Autoimmune Condition', 'codes'=> ["ai"]],
            ['name'=> 'Breast Cancer', 'codes'=> []],
            ['name'=> 'Colorectal (colon) Cancer', 'codes'=> []],
            ['name'=> 'Prostate Cancer', 'codes'=> []],
        ];

        $conditionsData = [
            ['name'=>'High Blood Pressure', 'codes'=> ["hpb", "weight"]],
            ['name'=>'High Cholesterol', 'codes'=> ["hc", "glucose", "cardio"]],
            ['name'=>'Acid Reflux', 'codes'=> ["gi"]],
            ['name'=>'Diabetes', 'codes'=> ["diabetes", "weight", "glucose"]],
            ['name'=>'Cancer', 'codes'=> []],
            ['name'=>'Autoimmune Condition', 'codes'=> ["mental", "fatigue", "cardio", "hormones", "gi", "ai"]],
            ['name'=>'Chronic Pain', 'codes'=> ["chronicpain", "mental", "fatigue"]],
            ['name'=>'Constipation', 'codes'=> ["gi"]],
            ['name'=>'Irritable Bowel Syndrome', 'codes'=> ["hormones", "gi"]],
            ['name'=>'Indigestion', 'codes'=> ["gi"]],
            ['name'=>'Depression', 'codes'=> ["mental", "fatigue", "hormones", "gi"]],
            ['name'=>'Anxiety', 'codes'=> ["mental", "hormones", "gi"]],
            ['name'=>'Hypothyoid', 'codes'=> ["fatigue", "weight", "hormones"]],
            ['name'=>'Insomnia', 'codes'=> ["fatigue", "hormones", "gi"]],
            ['name'=>'Obesity', 'codes'=> ["obesity", "weight", "glucose"]],
            ['name'=>'Recent weight gain', 'codes'=> ["rwg", "mental", "weight", "glucose", "cardio", "hormones"]],
            ['name'=>'Allergies', 'codes'=> ["fatigue", "gi"]],
            ['name'=>'Hypertension', 'codes'=> ["cardio"]],
            
        ];


        $medicineData = [
            ['name' => 'Pain', 'codes' => []],
            ['name' => 'Antibiotic', 'codes' => []],
            ['name' => 'PPI', 'codes' => ["gi"]],
            ['name' => 'Anxiety', 'codes' => ["mental"]],
            ['name' => 'Depression', 'codes' => ["mental", "fatigue"]],
            ['name' => 'Insomnia', 'codes' => ["mental", "fatigue"]],
            ['name' => 'Antihistamine', 'codes' => []],
            ['name' => 'Blood Pressure', 'codes' => ["cardio"]],
            ['name' => 'Corticosteroid', 'codes' => ["fatigue"]],
            ['name' => 'Diuretics', 'codes' => ["cardio"]],
            ['name' => 'Thyroid', 'codes' => ["hormones"]],
            ['name' => 'Hormones', 'codes' => ["hormones"]],
            ['name' => 'Glucose', 'codes' => ["glucose"]],
            ['name' => 'Street drugs', 'codes' => ["fatigue"]],
            ['name' => 'Bronchodilator', 'codes' => []],
            ['name' => 'Cholesterol', 'codes' => ["cardio"]],
            ['name' => 'Other', 'codes' => []],
        ];

        // Not using mass insert because it doesn't call events
        foreach ($familyHistoryData as $value) {
            FamilyHistory::create($value);
        }

        foreach ($conditionsData as $value) {
            MedicalConditions::create($value);
        }

        foreach ($medicineData as $value) {
            Medicine::create($value);
        }

    }
}
