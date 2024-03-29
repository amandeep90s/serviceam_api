<?php

namespace Database\Seeders\Service;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($company = null): void
    {
        Schema::connection("service")->disableForeignKeyConstraints();

        $service = DB::table("admin_services")
            ->where("admin_service", "SERVICE")
            ->first();

        $Electrician_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Electrician")
            ->first()->id;
        $Plumber_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Plumber")
            ->first()->id;
        $Tutors_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Tutors")
            ->first()->id;
        $Carpenter_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Carpenter")
            ->first()->id;
        $Mechanic_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Mechanic")
            ->first()->id;
        $Beautician_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Beautician")
            ->first()->id;
        $DJ_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "DJ")
            ->first()->id;
        $Massage_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Massage")
            ->first()->id;
        $Tow_Truck_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Tow Truck")
            ->first()->id;
        $Painting_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Painting")
            ->first()->id;
        $Car_Wash_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Car Wash")
            ->first()->id;
        $PhotoGraphy_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "PhotoGraphy")
            ->first()->id;
        $Doctors_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Doctors")
            ->first()->id;
        $Dog_Walking_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Dog Walking")
            ->first()->id;
        $Baby_Sitting_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Baby Sitting")
            ->first()->id;
        $Fitness_Coach_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Fitness Coach")
            ->first()->id;
        $Maids_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Maids")
            ->first()->id;
        $Pest_Control_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Pest Control")
            ->first()->id;
        $Home_Painting_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Home Painting")
            ->first()->id;
        $PhysioTheraphy_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "PhysioTheraphy")
            ->first()->id;
        $Catering_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Catering")
            ->first()->id;
        $Dog_Gromming_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Dog Grooming")
            ->first()->id;
        $Vet_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Vet")
            ->first()->id;
        $Snow_Plows_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Snow Plows")
            ->first()->id;
        $Workers_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Workers")
            ->first()->id;
        $Lock_Smith_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Lock Smith")
            ->first()->id;
        $Travel_Agent_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Travel Agent")
            ->first()->id;
        $Tour_Guide_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Tour Guide")
            ->first()->id;
        $Insurance_Agent_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Insurance Agent")
            ->first()->id;
        $Security_Guard_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Security Guard")
            ->first()->id;
        $Fuel_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Fuel")
            ->first()->id;
        $Law_Mowing_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Lawn Mowing")
            ->first()->id;
        $Barber_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Barber")
            ->first()->id;
        $Interior_Decorator_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Interior Decorator")
            ->first()->id;
        $Lawn_Care_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Lawn Care")
            ->first()->id;
        $Carpet_Repairer_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Carpet Repairer")
            ->first()->id;
        $Computer_Repairer_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Computer Repairer")
            ->first()->id;
        $Cuddling_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Cuddling")
            ->first()->id;
        $Fire_Fighters_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Fire Fighters")
            ->first()->id;
        $Helpers_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Helpers")
            ->first()->id;
        $Lawyers_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Lawyers")
            ->first()->id;
        $Mobile_Technician_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Mobile Technician")
            ->first()->id;
        $Office_Cleaning_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Office Cleaning")
            ->first()->id;
        $Party_Cleaning_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Party Cleaning")
            ->first()->id;
        $Psychologist_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Psychologist")
            ->first()->id;
        $Road_Assistance_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Road Assistance")
            ->first()->id;
        $Sofa_Repairer_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Sofa Repairer")
            ->first()->id;
        $Spa_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Spa")
            ->first()->id;
        $Translator_category = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->where("service_category_name", "Translator")
            ->first()->id;

        $Wiring = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Electrician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Wiring")
            ->first()->id;
        $Blocks_and_Leakage = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Plumber_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Blocks and Leakage")
            ->first()->id;
        $Maths = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tutors_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Maths")
            ->first()->id;
        $Science = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tutors_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Science")
            ->first()->id;
        $Bolt_Latch = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Carpenter_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Bolt Latch")
            ->first()->id;
        $Furniture_Installation = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Carpenter_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Furniture Installation")
            ->first()->id;
        $Carpentry_Work = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Carpenter_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Carpentry Work")
            ->first()->id;
        $General_Mechanic = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Mechanic_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "General Mechanic")
            ->first()->id;
        $Car_Mechanic = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Mechanic_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Car Mechanic")
            ->first()->id;
        $Bike_Mechanic = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Mechanic_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Bike Mechanic")
            ->first()->id;
        $Hair_Style = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Beautician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Hair Style")
            ->first()->id;
        $Makeup = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Beautician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Makeup")
            ->first()->id;
        $BlowOut = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Beautician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "BlowOut")
            ->first()->id;
        $Facial = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Beautician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Facial")
            ->first()->id;
        $Weddings = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $DJ_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Weddings")
            ->first()->id;
        $Parties = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $DJ_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Parties")
            ->first()->id;
        $Deep_Tissue_Massage = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Massage_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Deep Tissue Massage")
            ->first()->id;
        $Thai_Massage = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Massage_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Thai Massage")
            ->first()->id;
        $Swedish_Massage = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Massage_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Swedish Massage")
            ->first()->id;
        $Flat_Tier = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tow_Truck_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Flat Tier")
            ->first()->id;
        $Towing = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tow_Truck_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Towing")
            ->first()->id;
        $Key_Lock_Out = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tow_Truck_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Key Lock Out")
            ->first()->id;
        $Interior_Painting = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Painting_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Interior Painting")
            ->first()->id;
        $Exterior_Painting = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Painting_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Exterior Painting")
            ->first()->id;
        $Wedding = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $PhotoGraphy_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Wedding")
            ->first()->id;
        $Tap_and_wash_basin = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Plumber_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Tap and wash basin")
            ->first()->id;
        $Fans = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Electrician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Fans")
            ->first()->id;
        $Switches_and_Meters = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Electrician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Switches and Meters")
            ->first()->id;
        $Lights = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Electrician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Lights")
            ->first()->id;
        $Others = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Electrician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Others")
            ->first()->id;
        $Toilet = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Plumber_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Toilet")
            ->first()->id;
        $Bathroom_Fitting = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Plumber_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Bathroom Fitting")
            ->first()->id;
        $Water_Tank = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Plumber_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Water Tank")
            ->first()->id;
        $Walking = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Dog_Walking_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Walking")
            ->first()->id;
        $Day_Care = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Baby_Sitting_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Day Care")
            ->first()->id;
        $English = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tutors_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "English")
            ->first()->id;
        $Social_Science = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tutors_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Social Science")
            ->first()->id;
        $Computer = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tutors_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Computer")
            ->first()->id;
        $Hatchback = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Car_Wash_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Hatchback")
            ->first()->id;
        $Sedan = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Car_Wash_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Sedan")
            ->first()->id;
        $SUV = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Car_Wash_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "SUV")
            ->first()->id;
        $Photoshoot = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $PhotoGraphy_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Photoshoot")
            ->first()->id;
        $General_Physician = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Doctors_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "General Physician")
            ->first()->id;
        $Cardiologist = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Doctors_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Cardiologist")
            ->first()->id;
        $Dermatologist = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Doctors_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Dermatologist")
            ->first()->id;
        $After_School_Sitters = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Baby_Sitting_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "After School Sitters")
            ->first()->id;
        $Date_Night_sitters = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Baby_Sitting_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Date Night sitters")
            ->first()->id;
        $Tutoring_and_lessons = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Baby_Sitting_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Tutoring and lessons")
            ->first()->id;
        $Aerobic_Exercise = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Fitness_Coach_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Aerobic Exercise")
            ->first()->id;
        $Resistance_Exercise = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Fitness_Coach_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Resistance Exercise")
            ->first()->id;
        $Flexibility_Training = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Fitness_Coach_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Flexibility Training")
            ->first()->id;
        $Full_Home_Deep_Cleaning = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Maids_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Full Home Deep Cleaning")
            ->first()->id;
        $Post_Party_Cleaning = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Maids_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Party Cleaning")
            ->first()->id;
        $Office_Cleaning = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Maids_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Office Cleaning")
            ->first()->id;
        $Water_Tank_Storage_Cleaning = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Maids_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Water Tank Storage Cleaning")
            ->first()->id;
        $Termite_Cleaning = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Pest_Control_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Termite Cleaning")
            ->first()->id;
        $Cockroach_Treatment = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Pest_Control_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Cockroach Treatment")
            ->first()->id;
        $Bed_Bugs_Treatment = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Pest_Control_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Bed Bugs Treatment")
            ->first()->id;
        $Mosquito_Treatment = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Pest_Control_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Mosquito Treatment")
            ->first()->id;
        $Muscle_and_Joint_Pain = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $PhysioTheraphy_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Muscle and Joint Pain")
            ->first()->id;
        $Knee_Pain = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $PhysioTheraphy_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Knee Pain")
            ->first()->id;
        $sciatica = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $PhysioTheraphy_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "sciatica")
            ->first()->id;
        $Lunch_Meetings = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Catering_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Lunch Meetings")
            ->first()->id;
        $Wedding_Catering = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Catering_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Wedding Catering")
            ->first()->id;
        $Event_Catering = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Catering_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Event Catering")
            ->first()->id;
        $Office_Catering = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Catering_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Office Catering")
            ->first()->id;
        $Clippers_and_Scissors = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Dog_Gromming_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Clippers and Scissors")
            ->first()->id;
        $Nail_Clippers = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Dog_Gromming_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Nail Clippers")
            ->first()->id;
        $Brushes_and_Combs = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Dog_Gromming_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Brushes and Combs")
            ->first()->id;
        $Surgery = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Vet_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Surgery")
            ->first()->id;
        $Vaccine = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Vet_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Vaccine")
            ->first()->id;
        $Disease = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Vet_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Disease")
            ->first()->id;
        $Plow_Category_1 = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Snow_Plows_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Plow Category 1")
            ->first()->id;
        $Plow_Category_2 = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Snow_Plows_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Plow Category 2")
            ->first()->id;
        $Home_Work = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Workers_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Home Work")
            ->first()->id;
        $Office_Work = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Workers_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Office Work")
            ->first()->id;
        $Residential = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Lock_Smith_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Residential")
            ->first()->id;
        $Commercial = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Lock_Smith_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Commercial")
            ->first()->id;
        $Automobile = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Lock_Smith_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Automobile")
            ->first()->id;
        $Ticket_Booking = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Travel_Agent_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Ticket Booking")
            ->first()->id;
        $Hotel_Booking = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Travel_Agent_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Hotel Booking")
            ->first()->id;
        $Sight_Seeing = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tour_Guide_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Sight Seeing")
            ->first()->id;
        $Trekking = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tour_Guide_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Trekking")
            ->first()->id;
        $Walking_Tour = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Tour_Guide_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Walking Tour")
            ->first()->id;
        $Health_Insurance_Agent = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Insurance_Agent_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Health Insurance Agent")
            ->first()->id;
        $Mutual_Fund_Agent = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Insurance_Agent_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Mutual Fund Agent")
            ->first()->id;
        $Personal_Security = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Security_Guard_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Personal Security")
            ->first()->id;
        $Commercial_Security = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Security_Guard_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Commercial Security")
            ->first()->id;
        $Residential_Category = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Security_Guard_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Residential Category")
            ->first()->id;
        $Petrol = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Fuel_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Petrol")
            ->first()->id;
        $Diesel = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Fuel_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Diesel")
            ->first()->id;
        $LPG = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Fuel_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "LPG")
            ->first()->id;
        $_to_2000_Sqft_Lawn = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Law_Mowing_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "0 to 2000 Sqft Lawn")
            ->first()->id;
        $_to_4000_Sqft_Lawn = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Law_Mowing_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "2000 to 4000 Sqft Lawn")
            ->first()->id;
        $_to_6000_Sqft_Lawn = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Law_Mowing_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "4000 to 6000 Sqft Lawn")
            ->first()->id;
        $Hair_Cutting = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Barber_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Hair Cutting")
            ->first()->id;
        $Hair_Dressing = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Barber_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Hair Dressing")
            ->first()->id;
        $Shaving = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Barber_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Shaving")
            ->first()->id;
        $Home_Interior = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Interior_Decorator_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Home Interior")
            ->first()->id;
        $Office_Interior = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Interior_Decorator_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Office Interior")
            ->first()->id;
        $Pest_Control = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Lawn_Care_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Pest Control")
            ->first()->id;
        $Mosquito_Treatment = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Lawn_Care_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Mosquito Treatment")
            ->first()->id;
        $Bed_Bug_Treatment = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Lawn_Care_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Bed Bug Treatment")
            ->first()->id;
        $Sofa_Cleaning_Services = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Carpet_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Sofa Cleaning Services")
            ->first()->id;
        $Carpet_Shampooing_Services = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Carpet_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Carpet Shampooing Services")
            ->first()->id;
        $Commercial_Carpet_Cleaning = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Carpet_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Commercial Carpet Cleaning")
            ->first()->id;
        $Laptop = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Computer_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Laptop")
            ->first()->id;
        $Desktop = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Computer_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Desktop")
            ->first()->id;
        $Mac = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Computer_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Mac")
            ->first()->id;
        $Category_Cuddling_1 = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Cuddling_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Category 1")
            ->first()->id;
        $Category_Cuddling_2 = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Cuddling_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Category 2")
            ->first()->id;
        $Water_Pumps_and_hoses = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Fire_Fighters_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Water Pumps and hoses")
            ->first()->id;
        $Fire_Extinguishers = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Fire_Fighters_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Fire Extinguishers")
            ->first()->id;
        $Category_1 = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Helpers_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Category 1")
            ->first()->id;
        $Category_2 = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Helpers_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Category 2")
            ->first()->id;
        $Civil_Lawyers = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Lawyers_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Civil Lawyers")
            ->first()->id;
        $Lawyers_for_Property_Case = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Lawyers_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Lawyers for Property Case")
            ->first()->id;
        $Mobile = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Mobile_Technician_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Mobile")
            ->first()->id;
        $Vacuum_All_Floors = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Office_Cleaning_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Vacuum All Floors")
            ->first()->id;
        $Clean_and_Replace_bins = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Office_Cleaning_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Clean and Replace bins")
            ->first()->id;
        $Lobby_and_Workplace = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Office_Cleaning_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Lobby and Workplace")
            ->first()->id;
        $Dinning_Washout = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Party_Cleaning_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Dinning Washout")
            ->first()->id;
        $Table_Cleaning = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Party_Cleaning_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Table Cleaning")
            ->first()->id;
        $Counselors = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Psychologist_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Counselors")
            ->first()->id;
        $Child_Psychologist = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Psychologist_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Child Psychologist")
            ->first()->id;
        $Cognitive_Behavioral_Therapists = DB::connection("service")
            ->table("service_subcategories")
            ->where("company_id", $company)
            ->where("service_category_id", $Psychologist_category)
            ->where(
                "service_subcategory_name",
                "Cognitive Behavioral Therapists"
            )
            ->first()->id;
        $Vehicle_Breakdown = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Road_Assistance_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Vehicle Breakdown")
            ->first()->id;
        $Towing = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Road_Assistance_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Towing")
            ->first()->id;
        $Battery_Service = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Road_Assistance_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Battery Service")
            ->first()->id;
        $Furniture_Repair = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Sofa_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Furniture Repair")
            ->first()->id;
        $Chair_Repair = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Sofa_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Chair Repair")
            ->first()->id;
        $Furniture_Upholstery = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Sofa_Repairer_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Furniture Upholstery")
            ->first()->id;
        $Aromatherapy_Massage = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Spa_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Aromatherapy Massage")
            ->first()->id;
        $Balinese_Massage = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Spa_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Balinese Massage")
            ->first()->id;
        $Swedish_Massage = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Spa_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Swedish Massage")
            ->first()->id;
        $Category_1 = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Translator_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Category 1")
            ->first()->id;
        $Category_2 = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Translator_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Category 2")
            ->first()->id;
        $Fridge = DB::connection("service")
            ->table("service_subcategories")
            ->where("service_category_id", $Mechanic_category)
            ->where("company_id", $company)
            ->where("service_subcategory_name", "Fridge")
            ->first()->id;

        $services = [
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Wiring,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Blocks_and_Leakage,
                "company_id" => $company,
                "service_name" => "Fitting or Installation",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Maths,
                "company_id" => $company,
                "service_name" => "Algebra",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Maths,
                "company_id" => $company,
                "service_name" => "Calculus and Analysis",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Maths,
                "company_id" => $company,
                "service_name" => "Geometry",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Science,
                "company_id" => $company,
                "service_name" => "Physics",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Science,
                "company_id" => $company,
                "service_name" => "Chemistry",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Science,
                "company_id" => $company,
                "service_name" => "Biology",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpenter_category,
                "service_subcategory_id" => $Bolt_Latch,
                "company_id" => $company,
                "service_name" => "Door Stopper",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpenter_category,
                "service_subcategory_id" => $Bolt_Latch,
                "company_id" => $company,
                "service_name" => "Door Handle",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpenter_category,
                "service_subcategory_id" => $Furniture_Installation,
                "company_id" => $company,
                "service_name" => "Lock and Others",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpenter_category,
                "service_subcategory_id" => $Carpentry_Work,
                "company_id" => $company,
                "service_name" => "Wooden Partion",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpenter_category,
                "service_subcategory_id" => $Carpentry_Work,
                "company_id" => $company,
                "service_name" => "Wooden Partition",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mechanic_category,
                "service_subcategory_id" => $General_Mechanic,
                "company_id" => $company,
                "service_name" => "Oil change",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mechanic_category,
                "service_subcategory_id" => $General_Mechanic,
                "company_id" => $company,
                "service_name" => "General Service",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mechanic_category,
                "service_subcategory_id" => $Car_Mechanic,
                "company_id" => $company,
                "service_name" => "Oil Change",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mechanic_category,
                "service_subcategory_id" => $Car_Mechanic,
                "company_id" => $company,
                "service_name" => "General Service",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mechanic_category,
                "service_subcategory_id" => $Bike_Mechanic,
                "company_id" => $company,
                "service_name" => "Oil Change",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $Hair_Style,
                "company_id" => $company,
                "service_name" => "Long Hair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $Hair_Style,
                "company_id" => $company,
                "service_name" => "Medium Hair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $Hair_Style,
                "company_id" => $company,
                "service_name" => "Short Hair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $Makeup,
                "company_id" => $company,
                "service_name" => "Bridal Makeup",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $Makeup,
                "company_id" => $company,
                "service_name" => "Party Makeup",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $Makeup,
                "company_id" => $company,
                "service_name" => "PhotoShoot",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $BlowOut,
                "company_id" => $company,
                "service_name" => "Curly Hair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $BlowOut,
                "company_id" => $company,
                "service_name" => "Long Hair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Beautician_category,
                "service_subcategory_id" => $Facial,
                "company_id" => $company,
                "service_name" => "Back Facial",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $DJ_category,
                "service_subcategory_id" => $Weddings,
                "company_id" => $company,
                "service_name" => "Pop",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $DJ_category,
                "service_subcategory_id" => $Weddings,
                "company_id" => $company,
                "service_name" => "Jazz",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $DJ_category,
                "service_subcategory_id" => $Weddings,
                "company_id" => $company,
                "service_name" => "Classical",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $DJ_category,
                "service_subcategory_id" => $Parties,
                "company_id" => $company,
                "service_name" => "Folk",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $DJ_category,
                "service_subcategory_id" => $Parties,
                "company_id" => $company,
                "service_name" => "Jazz",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $DJ_category,
                "service_subcategory_id" => $Parties,
                "company_id" => $company,
                "service_name" => "Classical",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Massage_category,
                "service_subcategory_id" => $Deep_Tissue_Massage,
                "company_id" => $company,
                "service_name" => "30 Min",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Massage_category,
                "service_subcategory_id" => $Deep_Tissue_Massage,
                "company_id" => $company,
                "service_name" => "45 Min",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Massage_category,
                "service_subcategory_id" => $Thai_Massage,
                "company_id" => $company,
                "service_name" => "30 Min",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Massage_category,
                "service_subcategory_id" => $Thai_Massage,
                "company_id" => $company,
                "service_name" => "60 Min",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Massage_category,
                "service_subcategory_id" => $Swedish_Massage,
                "company_id" => $company,
                "service_name" => "30 Min",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Massage_category,
                "service_subcategory_id" => $Swedish_Massage,
                "company_id" => $company,
                "service_name" => "45 Min",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tow_Truck_category,
                "service_subcategory_id" => $Flat_Tier,
                "company_id" => $company,
                "service_name" => "Single Tier",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tow_Truck_category,
                "service_subcategory_id" => $Key_Lock_Out,
                "company_id" => $company,
                "service_name" => "Key Lock Out",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tow_Truck_category,
                "service_subcategory_id" => $Towing,
                "company_id" => $company,
                "service_name" => "Load Truck",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Painting_category,
                "service_subcategory_id" => $Interior_Painting,
                "company_id" => $company,
                "service_name" => "Repainting",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Painting_category,
                "service_subcategory_id" => $Interior_Painting,
                "company_id" => $company,
                "service_name" => "Fresh Painting",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Painting_category,
                "service_subcategory_id" => $Exterior_Painting,
                "company_id" => $company,
                "service_name" => "Repainting",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Painting_category,
                "service_subcategory_id" => $Exterior_Painting,
                "company_id" => $company,
                "service_name" => "Fresh Painting",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhotoGraphy_category,
                "service_subcategory_id" => $Wedding,
                "company_id" => $company,
                "service_name" => "Canon",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Maths,
                "company_id" => $company,
                "service_name" => "Probability",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Fans,
                "company_id" => $company,
                "service_name" => "Wiring",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Fans,
                "company_id" => $company,
                "service_name" => "Installation",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Wiring,
                "company_id" => $company,
                "service_name" => "Installation",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Switches_and_Meters,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Switches_and_Meters,
                "company_id" => $company,
                "service_name" => "Installation",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Lights,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Others,
                "company_id" => $company,
                "service_name" => "Fuse Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Electrician_category,
                "service_subcategory_id" => $Others,
                "company_id" => $company,
                "service_name" => "AC Installation",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Tap_and_wash_basin,
                "company_id" => $company,
                "service_name" => "Fitting or Installation",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Tap_and_wash_basin,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Toilet,
                "company_id" => $company,
                "service_name" => "Fitting or Installtion",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Toilet,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Blocks_and_Leakage,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Bathroom_Fitting,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Bathroom_Fitting,
                "company_id" => $company,
                "service_name" => "Fitting or Installtion",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Water_Tank,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Plumber_category,
                "service_subcategory_id" => $Water_Tank,
                "company_id" => $company,
                "service_name" => "Fitting or Installtion",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Maths,
                "company_id" => $company,
                "service_name" => "Trignometry",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $English,
                "company_id" => $company,
                "service_name" => "Poetry",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $English,
                "company_id" => $company,
                "service_name" => "Drama",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $English,
                "company_id" => $company,
                "service_name" => "Literature",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Social_Science,
                "company_id" => $company,
                "service_name" => "History",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Social_Science,
                "company_id" => $company,
                "service_name" => "Civics",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Social_Science,
                "company_id" => $company,
                "service_name" => "Geography",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Computer,
                "company_id" => $company,
                "service_name" => "Fundamental Programming",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Computer,
                "company_id" => $company,
                "service_name" => "Networks",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tutors_category,
                "service_subcategory_id" => $Computer,
                "company_id" => $company,
                "service_name" => "Web Development",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tow_Truck_category,
                "service_subcategory_id" => $Flat_Tier,
                "company_id" => $company,
                "service_name" => "2 tier",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tow_Truck_category,
                "service_subcategory_id" => $Towing,
                "company_id" => $company,
                "service_name" => "Tow truck for Buses",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tow_Truck_category,
                "service_subcategory_id" => $Towing,
                "company_id" => $company,
                "service_name" => "Wheel Lift",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $Hatchback,
                "company_id" => $company,
                "service_name" => "Economic Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $Hatchback,
                "company_id" => $company,
                "service_name" => "Standard Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $Hatchback,
                "company_id" => $company,
                "service_name" => "Premium Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $Sedan,
                "company_id" => $company,
                "service_name" => "Economic Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $Sedan,
                "company_id" => $company,
                "service_name" => "Standard Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $Sedan,
                "company_id" => $company,
                "service_name" => "Premium Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $SUV,
                "company_id" => $company,
                "service_name" => "Economic Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $SUV,
                "company_id" => $company,
                "service_name" => "Standard Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Car_Wash_category,
                "service_subcategory_id" => $SUV,
                "company_id" => $company,
                "service_name" => "Premium Wash",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhotoGraphy_category,
                "service_subcategory_id" => $Wedding,
                "company_id" => $company,
                "service_name" => "Pre Wedding",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhotoGraphy_category,
                "service_subcategory_id" => $Wedding,
                "company_id" => $company,
                "service_name" => "Post Wedding",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhotoGraphy_category,
                "service_subcategory_id" => $Photoshoot,
                "company_id" => $company,
                "service_name" => "Baby Photoshoot",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhotoGraphy_category,
                "service_subcategory_id" => $Photoshoot,
                "company_id" => $company,
                "service_name" => "Party Photo shoot",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhotoGraphy_category,
                "service_subcategory_id" => $Photoshoot,
                "company_id" => $company,
                "service_name" => "Outdoor Shoot",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $Dermatologist,
                "company_id" => $company,
                "service_name" => "Skin Care",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $Dermatologist,
                "company_id" => $company,
                "service_name" => "Skin Infection",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $Dermatologist,
                "company_id" => $company,
                "service_name" => "Mole removal Treatment",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $Dermatologist,
                "company_id" => $company,
                "service_name" => "Skin Biopsy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $Cardiologist,
                "company_id" => $company,
                "service_name" => "ECG",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $Cardiologist,
                "company_id" => $company,
                "service_name" => "Angioplasty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $Cardiologist,
                "company_id" => $company,
                "service_name" => "Heart catheterizations",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $General_Physician,
                "company_id" => $company,
                "service_name" => "Fever",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],

            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $General_Physician,
                "company_id" => $company,
                "service_name" => "Stomach Ache",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Doctors_category,
                "service_subcategory_id" => $General_Physician,
                "company_id" => $company,
                "service_name" => "General Checkup",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Walking_category,
                "service_subcategory_id" => $Walking,
                "company_id" => $company,
                "service_name" => "1 Hour walking",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Walking_category,
                "service_subcategory_id" => $Walking,
                "company_id" => $company,
                "service_name" => "2 Hours Walking",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Walking_category,
                "service_subcategory_id" => $Walking,
                "company_id" => $company,
                "service_name" => "30 Min walking",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Baby_Sitting_category,
                "service_subcategory_id" => $Day_Care,
                "company_id" => $company,
                "service_name" => "Part Time",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Baby_Sitting_category,
                "service_subcategory_id" => $Day_Care,
                "company_id" => $company,
                "service_name" => "Full Time",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Baby_Sitting_category,
                "service_subcategory_id" => $Date_Night_sitters,
                "company_id" => $company,
                "service_name" => "60 Mins",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Baby_Sitting_category,
                "service_subcategory_id" => $Date_Night_sitters,
                "company_id" => $company,
                "service_name" => "120 Mins",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Baby_Sitting_category,
                "service_subcategory_id" => $After_School_Sitters,
                "company_id" => $company,
                "service_name" => "1 Hour",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Baby_Sitting_category,
                "service_subcategory_id" => $After_School_Sitters,
                "company_id" => $company,
                "service_name" => "2 Hours",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Baby_Sitting_category,
                "service_subcategory_id" => $Tutoring_and_lessons,
                "company_id" => $company,
                "service_name" => "1 hour",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Baby_Sitting_category,
                "service_subcategory_id" => $Tutoring_and_lessons,
                "company_id" => $company,
                "service_name" => "2 Hours",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fitness_Coach_category,
                "service_subcategory_id" => $Aerobic_Exercise,
                "company_id" => $company,
                "service_name" => "Basic Plan",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fitness_Coach_category,
                "service_subcategory_id" => $Aerobic_Exercise,
                "company_id" => $company,
                "service_name" => "Advanced Plan",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fitness_Coach_category,
                "service_subcategory_id" => $Resistance_Exercise,
                "company_id" => $company,
                "service_name" => "Basic Plan",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fitness_Coach_category,
                "service_subcategory_id" => $Resistance_Exercise,
                "company_id" => $company,
                "service_name" => "Advanced Plan",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fitness_Coach_category,
                "service_subcategory_id" => $Flexibility_Training,
                "company_id" => $company,
                "service_name" => "Basic Plan",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fitness_Coach_category,
                "service_subcategory_id" => $Flexibility_Training,
                "company_id" => $company,
                "service_name" => "Advanced Plan",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Full_Home_Deep_Cleaning,
                "company_id" => $company,
                "service_name" => "Bathroom Deep Cleaning",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Full_Home_Deep_Cleaning,
                "company_id" => $company,
                "service_name" => "Bedroom Deep Cleaning",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Full_Home_Deep_Cleaning,
                "company_id" => $company,
                "service_name" => "Kitchen Deep Cleaning",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Post_Party_Cleaning,
                "company_id" => $company,
                "service_name" => "Apartment",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Post_Party_Cleaning,
                "company_id" => $company,
                "service_name" => "Bunglow or Villa",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Office_Cleaning,
                "company_id" => $company,
                "service_name" => "Apartment",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Office_Cleaning,
                "company_id" => $company,
                "service_name" => "Bunglow or Villa",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Water_Tank_Storage_Cleaning,
                "company_id" => $company,
                "service_name" => "1000 to 2000 ltrs",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Water_Tank_Storage_Cleaning,
                "company_id" => $company,
                "service_name" => "2000 to 5000 ltrs",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Maids_category,
                "service_subcategory_id" => $Water_Tank_Storage_Cleaning,
                "company_id" => $company,
                "service_name" => "5000 ltrs",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Pest_Control_category,
                "service_subcategory_id" => $Termite_Cleaning,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Pest_Control_category,
                "service_subcategory_id" => $Termite_Cleaning,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Pest_Control_category,
                "service_subcategory_id" => $Cockroach_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Pest_Control_category,
                "service_subcategory_id" => $Cockroach_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Pest_Control_category,
                "service_subcategory_id" => $Bed_Bugs_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Pest_Control_category,
                "service_subcategory_id" => $Bed_Bugs_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Pest_Control_category,
                "service_subcategory_id" => $Mosquito_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Pest_Control_category,
                "service_subcategory_id" => $Mosquito_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhysioTheraphy_category,
                "service_subcategory_id" => $Muscle_and_Joint_Pain,
                "company_id" => $company,
                "service_name" => "30 Min Theraphy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhysioTheraphy_category,
                "service_subcategory_id" => $Muscle_and_Joint_Pain,
                "company_id" => $company,
                "service_name" => "60 Min Therapy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhysioTheraphy_category,
                "service_subcategory_id" => $Knee_Pain,
                "company_id" => $company,
                "service_name" => "30 Min Therapy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhysioTheraphy_category,
                "service_subcategory_id" => $Knee_Pain,
                "company_id" => $company,
                "service_name" => "60 Mins Therapy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhysioTheraphy_category,
                "service_subcategory_id" => $sciatica,
                "company_id" => $company,
                "service_name" => "30 Min Therapy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $PhysioTheraphy_category,
                "service_subcategory_id" => $sciatica,
                "company_id" => $company,
                "service_name" => "60 Mins Therapy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Catering_category,
                "service_subcategory_id" => $Lunch_Meetings,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Catering_category,
                "service_subcategory_id" => $Lunch_Meetings,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Catering_category,
                "service_subcategory_id" => $Wedding_Catering,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Catering_category,
                "service_subcategory_id" => $Wedding_Catering,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Catering_category,
                "service_subcategory_id" => $Event_Catering,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Catering_category,
                "service_subcategory_id" => $Event_Catering,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Catering_category,
                "service_subcategory_id" => $Office_Catering,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Catering_category,
                "service_subcategory_id" => $Office_Catering,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Clippers_and_Scissors,
                "company_id" => $company,
                "service_name" => "Small Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Clippers_and_Scissors,
                "company_id" => $company,
                "service_name" => "Medium Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Clippers_and_Scissors,
                "company_id" => $company,
                "service_name" => "Large Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Nail_Clippers,
                "company_id" => $company,
                "service_name" => "Small Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Nail_Clippers,
                "company_id" => $company,
                "service_name" => "Medium Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Nail_Clippers,
                "company_id" => $company,
                "service_name" => "Large Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Brushes_and_Combs,
                "company_id" => $company,
                "service_name" => "Small Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Brushes_and_Combs,
                "company_id" => $company,
                "service_name" => "Medium Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Dog_Gromming_category,
                "service_subcategory_id" => $Brushes_and_Combs,
                "company_id" => $company,
                "service_name" => "Large Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Vet_category,
                "service_subcategory_id" => $Surgery,
                "company_id" => $company,
                "service_name" => "Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Vet_category,
                "service_subcategory_id" => $Surgery,
                "company_id" => $company,
                "service_name" => "Cow",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Vet_category,
                "service_subcategory_id" => $Vaccine,
                "company_id" => $company,
                "service_name" => "Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Vet_category,
                "service_subcategory_id" => $Vaccine,
                "company_id" => $company,
                "service_name" => "Cow",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Vet_category,
                "service_subcategory_id" => $Disease,
                "company_id" => $company,
                "service_name" => "Dog",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Vet_category,
                "service_subcategory_id" => $Disease,
                "company_id" => $company,
                "service_name" => "Cow",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Snow_Plows_category,
                "service_subcategory_id" => $Plow_Category_1,
                "company_id" => $company,
                "service_name" => "Plow Subcategory 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Snow_Plows_category,
                "service_subcategory_id" => $Plow_Category_1,
                "company_id" => $company,
                "service_name" => "Plow Subcategory 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Snow_Plows_category,
                "service_subcategory_id" => $Plow_Category_2,
                "company_id" => $company,
                "service_name" => "Plow Subcategory 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Snow_Plows_category,
                "service_subcategory_id" => $Plow_Category_2,
                "company_id" => $company,
                "service_name" => "Plow Subcategory 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Workers_category,
                "service_subcategory_id" => $Home_Work,
                "company_id" => $company,
                "service_name" => "Deep Street Cleaning Work",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Workers_category,
                "service_subcategory_id" => $Home_Work,
                "company_id" => $company,
                "service_name" => "Home Cleaning work",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Workers_category,
                "service_subcategory_id" => $Office_Work,
                "company_id" => $company,
                "service_name" => "Office Dinning Work",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Workers_category,
                "service_subcategory_id" => $Office_Work,
                "company_id" => $company,
                "service_name" => "Office Drainage Work",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Residential,
                "company_id" => $company,
                "service_name" => "New Locking Setup",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Residential,
                "company_id" => $company,
                "service_name" => "Repair Locks",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Residential,
                "company_id" => $company,
                "service_name" => "Key Cutting Service",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Commercial,
                "company_id" => $company,
                "service_name" => "New Locking Setup",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Commercial,
                "company_id" => $company,
                "service_name" => "Repair Locks",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Commercial,
                "company_id" => $company,
                "service_name" => "Key Cutting Service",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Automobile,
                "company_id" => $company,
                "service_name" => "New Locking Setup",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Automobile,
                "company_id" => $company,
                "service_name" => "Repair Locks",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lock_Smith_category,
                "service_subcategory_id" => $Automobile,
                "company_id" => $company,
                "service_name" => "Key Cutting Service",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Travel_Agent_category,
                "service_subcategory_id" => $Ticket_Booking,
                "company_id" => $company,
                "service_name" => "Train Ticket Booking",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Travel_Agent_category,
                "service_subcategory_id" => $Ticket_Booking,
                "company_id" => $company,
                "service_name" => "Bus Ticket Booking",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Travel_Agent_category,
                "service_subcategory_id" => $Ticket_Booking,
                "company_id" => $company,
                "service_name" => "Bus Ticket Booking",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Travel_Agent_category,
                "service_subcategory_id" => $Hotel_Booking,
                "company_id" => $company,
                "service_name" => "1 day",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Travel_Agent_category,
                "service_subcategory_id" => $Hotel_Booking,
                "company_id" => $company,
                "service_name" => "Tour Guide",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tour_Guide_category,
                "service_subcategory_id" => $Sight_Seeing,
                "company_id" => $company,
                "service_name" => "Sight Seeing subcategory 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tour_Guide_category,
                "service_subcategory_id" => $Sight_Seeing,
                "company_id" => $company,
                "service_name" => "Sight Seeing Subcategory 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tour_Guide_category,
                "service_subcategory_id" => $Walking_Tour,
                "company_id" => $company,
                "service_name" => "Walking Tour Subcategory 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tour_Guide_category,
                "service_subcategory_id" => $Walking_Tour,
                "company_id" => $company,
                "service_name" => "Walking Tour Subcategory 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tour_Guide_category,
                "service_subcategory_id" => $Trekking,
                "company_id" => $company,
                "service_name" => "Trekking Subcategory 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Tour_Guide_category,
                "service_subcategory_id" => $Trekking,
                "company_id" => $company,
                "service_name" => "Trekking Subcategory 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Insurance_Agent_category,
                "service_subcategory_id" => $Health_Insurance_Agent,
                "company_id" => $company,
                "service_name" => "Health insurance Document",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Insurance_Agent_category,
                "service_subcategory_id" => $Mutual_Fund_Agent,
                "company_id" => $company,
                "service_name" => "Mutual Fund Document",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Security_Guard_category,
                "service_subcategory_id" => $Personal_Security,
                "company_id" => $company,
                "service_name" => "Day Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Security_Guard_category,
                "service_subcategory_id" => $Personal_Security,
                "company_id" => $company,
                "service_name" => "Night Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Security_Guard_category,
                "service_subcategory_id" => $Commercial_Security,
                "company_id" => $company,
                "service_name" => "Day Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Security_Guard_category,
                "service_subcategory_id" => $Commercial_Security,
                "company_id" => $company,
                "service_name" => "Night Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Security_Guard_category,
                "service_subcategory_id" => $Residential_Category,
                "company_id" => $company,
                "service_name" => "Day Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Security_Guard_category,
                "service_subcategory_id" => $Residential_Category,
                "company_id" => $company,
                "service_name" => "Night Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fuel_category,
                "service_subcategory_id" => $Petrol,
                "company_id" => $company,
                "service_name" => "5 Gallon",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fuel_category,
                "service_subcategory_id" => $Petrol,
                "company_id" => $company,
                "service_name" => "7 Gallon",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fuel_category,
                "service_subcategory_id" => $Diesel,
                "company_id" => $company,
                "service_name" => "5 Gallon",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fuel_category,
                "service_subcategory_id" => $Diesel,
                "company_id" => $company,
                "service_name" => "7 Gallon",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fuel_category,
                "service_subcategory_id" => $LPG,
                "company_id" => $company,
                "service_name" => "LPG Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fuel_category,
                "service_subcategory_id" => $LPG,
                "company_id" => $company,
                "service_name" => "LPG Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Law_Mowing_category,
                "service_subcategory_id" => $_to_2000_Sqft_Lawn,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Law_Mowing_category,
                "service_subcategory_id" => $_to_2000_Sqft_Lawn,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Law_Mowing_category,
                "service_subcategory_id" => $_to_4000_Sqft_Lawn,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Law_Mowing_category,
                "service_subcategory_id" => $_to_4000_Sqft_Lawn,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Law_Mowing_category,
                "service_subcategory_id" => $_to_4000_Sqft_Lawn,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Law_Mowing_category,
                "service_subcategory_id" => $_to_6000_Sqft_Lawn,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Law_Mowing_category,
                "service_subcategory_id" => $_to_6000_Sqft_Lawn,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Barber_category,
                "service_subcategory_id" => $Hair_Cutting,
                "company_id" => $company,
                "service_name" => "Kids Haircut",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Barber_category,
                "service_subcategory_id" => $Hair_Cutting,
                "company_id" => $company,
                "service_name" => "Long Haircut",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Barber_category,
                "service_subcategory_id" => $Hair_Cutting,
                "company_id" => $company,
                "service_name" => "Short Hair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Barber_category,
                "service_subcategory_id" => $Hair_Dressing,
                "company_id" => $company,
                "service_name" => "Short Hair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Barber_category,
                "service_subcategory_id" => $Hair_Dressing,
                "company_id" => $company,
                "service_name" => "Long Hair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Barber_category,
                "service_subcategory_id" => $Shaving,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Barber_category,
                "service_subcategory_id" => $Shaving,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Interior_Decorator_category,
                "service_subcategory_id" => $Home_Interior,
                "company_id" => $company,
                "service_name" => "Modular Kitchen",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Interior_Decorator_category,
                "service_subcategory_id" => $Home_Interior,
                "company_id" => $company,
                "service_name" => "Wardrobe Design",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Interior_Decorator_category,
                "service_subcategory_id" => $Office_Interior,
                "company_id" => $company,
                "service_name" => "3D Design",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Interior_Decorator_category,
                "service_subcategory_id" => $Office_Interior,
                "company_id" => $company,
                "service_name" => "Furniture and Table Design",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawn_Care_category,
                "service_subcategory_id" => $Pest_Control,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawn_Care_category,
                "service_subcategory_id" => $Pest_Control,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawn_Care_category,
                "service_subcategory_id" => $Mosquito_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawn_Care_category,
                "service_subcategory_id" => $Mosquito_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawn_Care_category,
                "service_subcategory_id" => $Bed_Bug_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawn_Care_category,
                "service_subcategory_id" => $Bed_Bug_Treatment,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpet_Repairer_category,
                "service_subcategory_id" => $Sofa_Cleaning_Services,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpet_Repairer_category,
                "service_subcategory_id" => $Sofa_Cleaning_Services,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpet_Repairer_category,
                "service_subcategory_id" => $Carpet_Shampooing_Services,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpet_Repairer_category,
                "service_subcategory_id" => $Carpet_Shampooing_Services,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpet_Repairer_category,
                "service_subcategory_id" => $Commercial_Carpet_Cleaning,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Carpet_Repairer_category,
                "service_subcategory_id" => $Commercial_Carpet_Cleaning,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Laptop,
                "company_id" => $company,
                "service_name" => "OS Installation",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Laptop,
                "company_id" => $company,
                "service_name" => "Motherboard Problem",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Laptop,
                "company_id" => $company,
                "service_name" => "Monitor or Display",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Desktop,
                "company_id" => $company,
                "service_name" => "Data Recovery",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Desktop,
                "company_id" => $company,
                "service_name" => "Not Working",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Desktop,
                "company_id" => $company,
                "service_name" => "Monitor and Display",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Mac,
                "company_id" => $company,
                "service_name" => "Charger",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Mac,
                "company_id" => $company,
                "service_name" => "Chip Level Servicing",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Computer_Repairer_category,
                "service_subcategory_id" => $Mac,
                "company_id" => $company,
                "service_name" => "Not Working",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Cuddling_category,
                "service_subcategory_id" => $Category_Cuddling_1,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Cuddling_category,
                "service_subcategory_id" => $Category_Cuddling_1,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Cuddling_category,
                "service_subcategory_id" => $Category_Cuddling_2,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Cuddling_category,
                "service_subcategory_id" => $Category_Cuddling_2,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fire_Fighters_category,
                "service_subcategory_id" => $Water_Pumps_and_hoses,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fire_Fighters_category,
                "service_subcategory_id" => $Water_Pumps_and_hoses,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fire_Fighters_category,
                "service_subcategory_id" => $Fire_Extinguishers,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Fire_Fighters_category,
                "service_subcategory_id" => $Fire_Extinguishers,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Helpers_category,
                "service_subcategory_id" => $Category_1,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Helpers_category,
                "service_subcategory_id" => $Category_1,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Helpers_category,
                "service_subcategory_id" => $Category_2,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Helpers_category,
                "service_subcategory_id" => $Category_2,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawyers_category,
                "service_subcategory_id" => $Civil_Lawyers,
                "company_id" => $company,
                "service_name" => "Equality Case",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawyers_category,
                "service_subcategory_id" => $Civil_Lawyers,
                "company_id" => $company,
                "service_name" => "Human RIghts",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawyers_category,
                "service_subcategory_id" => $Civil_Lawyers,
                "company_id" => $company,
                "service_name" => "Social Freedom",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawyers_category,
                "service_subcategory_id" => $Lawyers_for_Property_Case,
                "company_id" => $company,
                "service_name" => "Insurance and Environmental Issues",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Lawyers_category,
                "service_subcategory_id" => $Lawyers_for_Property_Case,
                "company_id" => $company,
                "service_name" => "Real Estate Transaction",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mobile_Technician_category,
                "service_subcategory_id" => $Mobile,
                "company_id" => $company,
                "service_name" => "Motorola",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mobile_Technician_category,
                "service_subcategory_id" => $Mobile,
                "company_id" => $company,
                "service_name" => "LG",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mobile_Technician_category,
                "service_subcategory_id" => $Mobile,
                "company_id" => $company,
                "service_name" => "Samsung",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mobile_Technician_category,
                "service_subcategory_id" => $Mobile,
                "company_id" => $company,
                "service_name" => "Others",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Office_Cleaning_category,
                "service_subcategory_id" => $Vacuum_All_Floors,
                "company_id" => $company,
                "service_name" => "Daily Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Office_Cleaning_category,
                "service_subcategory_id" => $Vacuum_All_Floors,
                "company_id" => $company,
                "service_name" => "Weekly Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Office_Cleaning_category,
                "service_subcategory_id" => $Clean_and_Replace_bins,
                "company_id" => $company,
                "service_name" => "Daily Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Office_Cleaning_category,
                "service_subcategory_id" => $Lobby_and_Workplace,
                "company_id" => $company,
                "service_name" => "Daily Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Office_Cleaning_category,
                "service_subcategory_id" => $Lobby_and_Workplace,
                "company_id" => $company,
                "service_name" => "Weekly Duty",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Party_Cleaning_category,
                "service_subcategory_id" => $Dinning_Washout,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Party_Cleaning_category,
                "service_subcategory_id" => $Dinning_Washout,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Party_Cleaning_category,
                "service_subcategory_id" => $Table_Cleaning,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Party_Cleaning_category,
                "service_subcategory_id" => $Table_Cleaning,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Psychologist_category,
                "service_subcategory_id" => $Counselors,
                "company_id" => $company,
                "service_name" => "Treatment",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Psychologist_category,
                "service_subcategory_id" => $Counselors,
                "company_id" => $company,
                "service_name" => "Counselling",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Psychologist_category,
                "service_subcategory_id" => $Child_Psychologist,
                "company_id" => $company,
                "service_name" => "Treatment",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Psychologist_category,
                "service_subcategory_id" => $Child_Psychologist,
                "company_id" => $company,
                "service_name" => "Counselling",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Psychologist_category,
                "service_subcategory_id" => $Cognitive_Behavioral_Therapists,
                "company_id" => $company,
                "service_name" => "Treatment",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Psychologist_category,
                "service_subcategory_id" => $Cognitive_Behavioral_Therapists,
                "company_id" => $company,
                "service_name" => "Counselling",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Road_Assistance_category,
                "service_subcategory_id" => $Vehicle_Breakdown,
                "company_id" => $company,
                "service_name" => "Car",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Road_Assistance_category,
                "service_subcategory_id" => $Vehicle_Breakdown,
                "company_id" => $company,
                "service_name" => "Lorry",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Road_Assistance_category,
                "service_subcategory_id" => $Towing,
                "company_id" => $company,
                "service_name" => "Car",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Road_Assistance_category,
                "service_subcategory_id" => $Towing,
                "company_id" => $company,
                "service_name" => "2 Wheelers",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Road_Assistance_category,
                "service_subcategory_id" => $Battery_Service,
                "company_id" => $company,
                "service_name" => "Car",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Road_Assistance_category,
                "service_subcategory_id" => $Battery_Service,
                "company_id" => $company,
                "service_name" => "Bike",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Sofa_Repairer_category,
                "service_subcategory_id" => $Furniture_Repair,
                "company_id" => $company,
                "service_name" => "Furniture Repair Subcategory 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Sofa_Repairer_category,
                "service_subcategory_id" => $Furniture_Repair,
                "company_id" => $company,
                "service_name" => "Furniture Repair Subcategory 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Sofa_Repairer_category,
                "service_subcategory_id" => $Chair_Repair,
                "company_id" => $company,
                "service_name" => "Chair Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Sofa_Repairer_category,
                "service_subcategory_id" => $Chair_Repair,
                "company_id" => $company,
                "service_name" => "Chair Repair Subcategory 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Sofa_Repairer_category,
                "service_subcategory_id" => $Furniture_Upholstery,
                "company_id" => $company,
                "service_name" => "Furniture Upholstery Subcategory 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Sofa_Repairer_category,
                "service_subcategory_id" => $Furniture_Upholstery,
                "company_id" => $company,
                "service_name" => "Furniture Upholstery Subcategory 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Spa_category,
                "service_subcategory_id" => $Aromatherapy_Massage,
                "company_id" => $company,
                "service_name" => "30 Mins",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Spa_category,
                "service_subcategory_id" => $Aromatherapy_Massage,
                "company_id" => $company,
                "service_name" => "60 Mins",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Spa_category,
                "service_subcategory_id" => $Balinese_Massage,
                "company_id" => $company,
                "service_name" => "30 Min Therapy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Spa_category,
                "service_subcategory_id" => $Balinese_Massage,
                "company_id" => $company,
                "service_name" => "60 Mins",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Spa_category,
                "service_subcategory_id" => $Swedish_Massage,
                "company_id" => $company,
                "service_name" => "30 Min Therapy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Spa_category,
                "service_subcategory_id" => $Swedish_Massage,
                "company_id" => $company,
                "service_name" => "60 Mins Therapy",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Translator_category,
                "service_subcategory_id" => $Category_1,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Translator_category,
                "service_subcategory_id" => $Category_1,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Translator_category,
                "service_subcategory_id" => $Category_2,
                "company_id" => $company,
                "service_name" => "Sub Category 1",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Translator_category,
                "service_subcategory_id" => $Category_2,
                "company_id" => $company,
                "service_name" => "Sub Category 2",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
            [
                "service_category_id" => $Mechanic_category,
                "service_subcategory_id" => $Fridge,
                "company_id" => $company,
                "service_name" => "Repair",
                "picture" => "",
                "allow_desc" => 0,
                "allow_before_image" => 0,
                "allow_after_image" => 0,
                "is_professional" => 0,
                "service_status" => 1,
            ],
        ];

        foreach (array_chunk($services, 1000) as $service) {
            DB::connection("service")
                ->table("services")
                ->insert($service);
        }

        Schema::connection("service")->enableForeignKeyConstraints();
    }
}
