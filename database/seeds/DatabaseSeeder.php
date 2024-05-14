<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PagesTableSeeder::class);
        $this->call(CountriesSeeder::class);

        $this->call(DestinationsTableSeeder::class);
        $this->call(ExperiencesTableSeeder::class);
        $this->call(AllocationsTableSeeder::class);
        $this->call(AnnualIncomesTableSeeder::class);
        $this->call(FeedbackQuestionsTableSeeder::class);
        $this->call(FeedbackAnswersTableSeeder::class);
        $this->call(SurveyExperienceQuestionsTableSeeder::class);
        $this->call(SurveyExperienceAnswersTableSeeder::class);
        $this->call(FaqsTableSeeder::class);

        $this->call(NewPageItemSeeder::class);
        $this->call(GeneratedEmailTableSeeder::class);
        $this->call(NewPageItemSeederTwo::class);

        $this->call(CopywritingsTableSeeder::class);
        $this->call(AgeRangesTableSeeder::class);
        $this->call(GendersTableSeeder::class);
        $this->call(SpecialFeesTableSeeder::class);
        $this->call(VisitorTypesTableSeeder::class);
        $this->call(SourcesTableSeeder::class);
    }
}
