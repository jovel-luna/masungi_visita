<?php

use Illuminate\Database\Seeder;

use App\Models\Genders\Gender;

class GendersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row = 0;
        if(($handle = fopen('database/csv/Genders.csv', "r")) !== FALSE){ // Check if CSV file exists
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Parse data inside the file
                if ($row > 0) { // row check

                    $this->command->info('Seeding Gender #' . $row . ' ' . $data[0]); // Traces seeded rows in terminal

                    // Seed model with csv data
                    $item = new Gender(); 
                    $item->name = $data[0]; 
                    $item->color = $data[1]; 
                    $item->save();
                }
                $row++;
            }
            fclose($handle);
        }
    }
}
