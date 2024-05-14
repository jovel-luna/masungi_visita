<?php

use Illuminate\Database\Seeder;

use App\Models\Emails\GeneratedEmail;

class GeneratedEmailTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row = 0;
        
        if(($handle = fopen('database/csv/GeneratedEmails.csv', "r")) !== FALSE){
        	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $this->command->info('Seeding Generated Email #' . $row . ' ' . $data[1]);

                $item = new GeneratedEmail();
                $item->notification_type = $data[0];
                $item->title = $data[1];
                $item->message = $data[2];
                $item->email_to = $data[3];

                $item->save();

                $row++;

            }
            fclose($handle);
        }
    }
}
