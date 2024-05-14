<?php

use Illuminate\Database\Seeder;

use App\Models\Permissions\PermissionCategory;
use App\Models\Permissions\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Transaction Fees',
                'description' => 'Manage Transaction Fees',
                'icon' => 'fas fa-cash-register',
                'items' => [
                    [
                        'name' => 'admin.payments.crud',
                        'description' => 'Manage Transaction Fees',
                    ],
                ],
            ],
            [
                'name' => 'Content Management',
                'description' => 'Manage Pages & Contents',
                'icon' => 'fa fa-feather',
                'items' => [
                    [
                        'name' => 'admin.pages.crud',
                        'description' => 'Manage Pages',
                    ],
                    [
                        'name' => 'admin.page-items.crud',
                        'description' => 'Manage Page Contents',
                    ],
                    [
                        'name' => 'admin.articles.crud',
                        'description' => 'Manage Articles',
                    ],
                ],
            ],
            [
                'name' => 'Carousels',
                'description' => 'Manage Page Carousels',
                'icon' => 'fa fa-feather',
                'items' => [
                    [
                        'name' => 'admin.home-banners.crud',
                        'description' => 'Manage Carousels',
                    ],
                ],
            ],
            [
                'name' => 'Tabbings',
                'description' => 'Manage Tabbings',
                'icon' => 'fa fa-feather',
                'items' => [
                    [
                        'name' => 'admin.about-infos.crud',
                        'description' => 'Manage About Tabbings',
                    ],
                ],
            ],
            [
                'name' => 'Admin Management',
                'description' => 'Manage Administrators',
                'icon' => 'fa fa-user-shield',
                'items' => [
                    [
                        'name' => 'admin.admin-users.crud',
                        'description' => 'Manage Administrator Accounts',
                    ],
                    [
                        'name' => 'admin.roles.crud',
                        'description' => 'Manage Admin Roles & Permissions',
                    ],
                ],
            ],
            [
                'name' => 'User Management',
                'description' => 'Manage User Accounts',
                'icon' => 'fa fa-users',
                'items' => [
                    [
                        'name' => 'admin.users.crud',
                        'description' => 'Manage User Accounts',
                    ],
                ],
            ],
            [
                'name' => 'Activity Logs',
                'description' => 'View Activity Logs',
                'icon' => 'fa fa-shield-alt',
                'items' => [
                    [
                        'name' => 'admin.activity-logs.crud',
                        'description' => 'View Activity Logs',
                    ],
                ],
            ],

            [
                'name' => 'Allocations',
                'description' => 'Manage Allocations',
                'icon' => 'fas fa-hiking',
                'items' => [
                    [
                        'name' => 'admin.allocations.crud',
                        'description' => 'Manage Allocations',
                    ],
                ],
            ],
            [
                'name' => 'Destinations',
                'description' => 'Manage Destinations',
                'icon' => 'fas fa-map-marked-alt',
                'items' => [
                    [
                        'name' => 'admin.destinations.crud',
                        'description' => 'Manage Destinations',
                    ],
                ],
            ],
            [
                'name' => 'Experiences',
                'description' => 'Manage Experiences',
                'icon' => 'fas fa-hiking',
                'items' => [
                    [
                        'name' => 'admin.experiences.crud',
                        'description' => 'Manage Experiences',
                    ],
                ],
            ],

            [
                'name' => 'Inquiries',
                'description' => 'View Inquiries',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.inquiries.crud',
                        'description' => 'View Inquiries',
                    ],
                ],
            ],

            [
                'name' => 'Annual Incomes',
                'description' => 'Manage Annual Income',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.annual_incomes.crud',
                        'description' => 'Manage Annual Income',
                    ],
                ],
            ],

            [
                'name' => 'Survey Experiences',
                'description' => 'Manage Survey Experience',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.survey_experiences.crud',
                        'description' => 'Manage Survey Experience',
                    ],
                ],
            ],

            [
                'name' => 'Special Fees',
                'description' => 'Manage Special Fees',
                'icon' => 'fas fa-comment-dollar',
                'items' => [
                    [
                        'name' => 'admin.special_fees.crud',
                        'description' => 'Manage Special Fees',
                    ],
                ],
            ],

            [
                'name' => 'Add Ons',
                'description' => 'Manage Add Ons',
                'icon' => 'fas fa-plus-square',
                'items' => [
                    [
                        'name' => 'admin.add_ons.crud',
                        'description' => 'Manage Add Ons',
                    ],
                ],
            ],

            [
                'name' => 'Visitor Types',
                'description' => 'Manage Visitor Type',
                'icon' => 'fas fa-user-friends',
                'items' => [
                    [
                        'name' => 'admin.visitor_types.crud',
                        'description' => 'Manage Visitor Type',
                    ],
                ],
            ],

            [
                'name' => 'Feedbacks',
                'description' => 'Manage Feedback',
                'icon' => 'fas fa-comment-dots',
                'items' => [
                    [
                        'name' => 'admin.feedbacks.crud',
                        'description' => 'Manage Feedbacks',
                    ],
                ],
            ],

            [
                'name' => 'Agencies',
                'description' => 'Manage Agencies',
                'icon' => 'fas fa-user-friends',
                'items' => [
                    [
                        'name' => 'admin.agencies.crud',
                        'description' => 'Manage Agencies',
                    ],
                ],
            ],

            [
                'name' => 'Calendar',
                'description' => 'Manage Calendar',
                'icon' => 'fas fa-user-friends',
                'items' => [
                    [
                        'name' => 'admin.calendar.crud',
                        'description' => 'Manage Calendar',
                    ],
                ],
            ],

            [
                'name' => 'Blocked Dates',
                'description' => 'Manage Blocked Dates',
                'icon' => 'fas fa-user-friends',
                'items' => [
                    [
                        'name' => 'admin.blocked-dates.crud',
                        'description' => 'Manage Blocked Dates',
                    ],
                ],
            ],

            [
                'name' => 'Training Modules',
                'description' => 'Manage Training Modules',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.training-modules.crud',
                        'description' => 'Manage Training Modules',
                    ],
                ],
            ],

            [
                'name' => 'Faqs',
                'description' => 'Manage Faqs',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.faqs.crud',
                        'description' => 'Manage Faqs',
                    ],
                ],
            ],

            [
                'name' => 'Capacities',
                'description' => 'Manage Capacities',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.capacities.crud',
                        'description' => 'Manage Capacities',
                    ],
                ],
            ],

            [
                'name' => 'Religions',
                'description' => 'Manage Religions',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.religions.crud',
                        'description' => 'Manage Religions',
                    ],
                ],
            ],

            [
                'name' => 'Announcements',
                'description' => 'Manage Announcements',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.announcements.crud',
                        'description' => 'Manage Announcements',
                    ],
                ],
            ],

            [
                'name' => 'Frontliners',
                'description' => 'Manage Frontliners',
                'icon' => 'fas fa-user-friends',
                'items' => [
                    [
                        'name' => 'admin.managements.crud',
                        'description' => 'Manage Frontliners',
                    ],
                ],
            ],

            [
                'name' => 'Violations',
                'description' => 'Manage Violations',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.violations.crud',
                        'description' => 'Manage Violations',
                    ],
                ],
            ],

            [
                'name' => 'Remarks',
                'description' => 'Manage Remarks',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.remarks.crud',
                        'description' => 'Manage Remarks',
                    ],
                ],
            ],

            [
                'name' => 'Surveys',
                'description' => 'View Surveys',
                'icon' => 'fas fa-poll-h',
                'items' => [
                    [
                        'name' => 'admin.surveys.crud',
                        'description' => 'View Surveys',
                    ],
                ],
            ],


            [
                'name' => 'Genders',
                'description' => 'Manage Gender',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.genders.crud',
                        'description' => 'Manage Genders',
                    ],
                ],
            ],


            [
                'name' => 'Civil Status',
                'description' => 'Manage Civil Status',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.civil_statuses.crud',
                        'description' => 'Manage Civil Status',
                    ],
                ],
            ],


            [
                'name' => 'About Us',
                'description' => 'Manage About Us',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.about-us.crud',
                        'description' => 'Manage About Us',
                    ],
                ],
            ],


            [
                'name' => 'Generated Emails',
                'description' => 'Manage Generated Emails',
                'icon' => 'fas fa-at',
                'items' => [
                    [
                        'name' => 'admin.generated-emails.crud',
                        'description' => 'Manage Generated Emails',
                    ],
                ],
            ],


            [
                'name' => 'Copywritings',
                'description' => 'Manage Copywriting',
                'icon' => 'fas fa-exclamation-circle',
                'items' => [
                    [
                        'name' => 'admin.copywritings.crud',
                        'description' => 'Manage Copywritings',
                    ],
                ],
            ],


            [
                'name' => 'Fees',
                'description' => 'Manage Fee',
                'icon' => 'fas fa-comment-dollar',
                'items' => [
                    [
                        'name' => 'admin.conservation-fees.crud',
                        'description' => 'Manage Fees',
                    ],
                ],
            ],


            /** Newly added permissions */
            [
                'name' => 'Age Ranges',
                'description' => 'Manage Age Ranges',
                'icon' => 'fas fa-users',
                'items' => [
                    [
                        'name' => 'admin.age-ranges.crud',
                        'description' => 'Manage Age Ranges',
                    ],
                ],
            ],

            [
                'name' => 'Nationalities',
                'description' => 'Manage Nationalities',
                'icon' => 'fas fa-flag',
                'items' => [
                    [
                        'name' => 'admin.nationalities.crud',
                        'description' => 'Manage Nationalities',
                    ],
                ],
            ],

            [
                'name' => 'Sources',
                'description' => 'Manage Sources',
                'icon' => 'fas fa-boxes',
                'items' => [
                    [
                        'name' => 'admin.sources.crud',
                        'description' => 'Manage Sources',
                    ],
                ],
            ],

        ];

    	foreach ($categories as $category) {
            $permissions = $category['items'];
            unset($category['items']);

            $item = PermissionCategory::where('name', $category['name'])->first();

            if (!$item) {
                $this->command->info('Adding permission category ' . $category['name'] . '...');
                $item = PermissionCategory::create($category);
            } else {
                $this->command->warn('Updating permission category ' . $category['name'] . '...');
                $item->update($category);
            }


            foreach ($permissions as $permission) {
                $permissionItem = Permission::where('name', $permission['name'])->first();
                
                if (!$permissionItem) {
                    $this->command->info('Adding permission ' . $permission['name'] . '...');
                    $item->permissions()->create($permission);
                } else {
                    $this->command->warn('Updating permission ' . $permission['name'] . '...');
                    unset($permission['name']);
                    $permissionItem->update($permission);
                }
            }
    	}
    }
}
