<?php

namespace App\Http\Controllers\Admin\Analytics;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Models\Users\Admin;
use App\Models\Users\User;
use App\Models\Books\Book;
use App\Models\ActivityLogs\ActivityLog;

use App\Models\Guests\Guest;
use App\Models\Invoices\Invoice;
use App\Models\Genders\Gender;
use App\Models\Nationalities\Nationality;
use App\Models\Sources\Source;
use App\Models\AgeRanges\AgeRange;
use DB;

class DashboardAnalyticsController extends Controller
{
    protected $startDate;
    protected $endDate;

    public function fetch(Request $request) {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $this->startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d') . " 00:00:00";
            $this->endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d') . " 23:59:59";
        }

        if ($request->filled('admin')) {
            $users = new Admin;
            $subject = 'App\Models\Users\Admin';
        } else {
            $users = new User;
            $subject = 'App\Models\Users\User';
        }

        $usage = $this->getSystemUsageAnalytics($users, $subject, $request);

        return response()->json($usage);
    }

    protected function getSystemUsageAnalytics($items, $subject, $request) {
        $today = Carbon::now();
        $capacity['groups'] = 0;
        $capacity['visitors'] = 0;


        $admin = auth()->guard('admin')->user();
        $admin_user = Admin::where('id', $admin->id)->with("roles")->first();

        if($request->date) {
            $today = $request->date;
        }

        // get all booking based on current destination assigned for logged-in user
        $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);

        if($admin->destination_id) {
            $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);
        }

        if($request->date) {

            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);

            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);
            }
        }

        if($request->destination && $request->destination != null) {
            $bookings = $bookings->where('destination_id', $request->destination);
        } 

        if($request->experience) {
            $bookings = $bookings->where('destination_id', $request->destination)->where('allocation_id', $request->experience);
        }
        // total of guest

        if($request->date) {
            $guest_walk_in = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', true)->sum('total_guest');
            $guest_online = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', false)->sum('total_guest');
            $total['guest'] = $guest_walk_in + $guest_online;
        } else {
            $guest_walk_in = $bookings->where('is_walkin', true)->sum('total_guest');
            $guest_online = $bookings->where('is_walkin', false)->sum('total_guest');
            $total['guest'] = $guest_walk_in + $guest_online;
        }
        // total of groups
        if($request->date) {
            $group_walk_in = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', true)->get()->count();
            $group_online = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', false)->get()->count();
            $total['groups'] = $group_walk_in + $group_online; //$bookings->whereDate('scheduled_at', $request->date)->get()->count();
        } else {
            $group_walk_in = $bookings->where('is_walkin', true)->get()->count();
            $group_online = $bookings->where('is_walkin', false)->get()->count();
            $total['groups'] = $group_walk_in + $group_online;
        }
        
        $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);

        if($admin->destination_id) {
            $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);
        }

        if($request->date) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);

            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);
            }
        }

        if($request->destination && $request->destination != null) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination);
        } 

        if($request->experience) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience);
        }

        if($request->destination) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination);
        } 

        if($request->experience) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience);
        }
        // get total checked in for walk in guest
        if($request->date) {
            $checked_in_walkin['visitors'] = $bookings->where('is_walkin', true)->get()->sum('total_guest'); 
        } else {
            $checked_in_walkin['visitors'] = $bookings->where('is_walkin', true)->get()->sum('total_guest'); 
        }
        if($request->date) {
            $checked_in_walkin['groups'] = $bookings->where('is_walkin', true)->get()->count(); 
        } else {
            $checked_in_walkin['groups'] = $bookings->where('is_walkin', true)->get()->count(); 
        }
        $today = Carbon::now();
        // get total checked in for online in guest
        $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);

        if($admin->destination_id) {
            $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);
        }

        if($request->date) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);

            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);
            }
        }
        $total_checked_in['online_visitor'] = $bookings->where('is_walkin', false)->whereNotNull('started_at')->get()->sum('total_guest'); 
        $total_checked_in['online_group'] = $bookings->where('is_walkin', false)->whereNotNull('started_at')->get()->count();

        if($request->destination) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->whereDate('scheduled_at', $today);
            $total_checked_in['online_visitor'] = $bookings->where('is_walkin', false)->whereNotNull('started_at')->get()->sum('total_guest'); 
            $total_checked_in['online_group'] = $bookings->where('is_walkin', false)->whereNotNull('started_at')->get()->count();
            if($request->date) {
                $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->whereDate('scheduled_at', $request->date);
                $total_checked_in['online_visitor'] = $bookings->whereDate('scheduled_at', $request->date)->whereNotNull('started_at')->where('is_walkin', false)->get()->sum('total_guest');
                $total_checked_in['online_group'] = $bookings->whereDate('scheduled_at', $request->date)->whereNotNull('started_at')->where('is_walkin', false)->get()->count();
            } 
        } 

        if($request->experience) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->whereDate('scheduled_at', $today);
            $total_checked_in['online_visitor'] = $bookings->where('is_walkin', false)->whereNotNull('started_at')->get()->sum('total_guest'); 
            $total_checked_in['online_group'] = $bookings->where('is_walkin', false)->whereNotNull('started_at')->get()->count();
            if($request->date) {
                $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->whereDate('scheduled_at', $request->date);
                $total_checked_in['online_visitor'] = $bookings->whereNotNull('started_at')->where('is_walkin', false)->get()->sum('total_guest');
                $total_checked_in['online_group'] = $bookings->whereNotNull('started_at')->where('is_walkin', false)->get()->count();
            } 
        }

        if($request->date) {
            $total_checked_in['online_visitor'] = $bookings->whereDate('scheduled_at', $request->date)->whereNotNull('started_at')->where('is_walkin', false)->get()->sum('total_guest');
        }

        $today = Carbon::now();
        $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);

        if($admin->destination_id) {
            $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);
        }

        $total_checked_in['walk_in'] = $bookings->where('is_walkin', true)->get()->sum('total_guest'); 
        $total_checked_in['walk_in_group'] = $bookings->where('is_walkin', true)->get()->count();  

        if($request->date) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);

            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);
            }

            $total_checked_in['walk_in'] = $bookings->where('is_walkin', true)->get()->sum('total_guest'); 
            $total_checked_in['walk_in_group'] = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', true)->get()->count();  

        } 

        if($request->destination) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination);
            if($request->date) {
                $total_checked_in['walk_in_group'] = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', true)->get()->count();  
                $total_checked_in['walk_in'] = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', true)->get()->sum('total_guest'); 
            } else {
                $total_checked_in['walk_in'] = $bookings->whereDate('scheduled_at', $today)->where('is_walkin', true)->get()->sum('total_guest'); 
                $total_checked_in['walk_in_group'] = $bookings->whereDate('scheduled_at', $today)->where('is_walkin', true)->get()->count();  
            }
        } 

        if($request->experience) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience); 
            if($request->date) {
                $total_checked_in['walk_in_group'] = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', true)->get()->count();  
                $total_checked_in['walk_in'] = $bookings->whereDate('scheduled_at', $request->date)->where('is_walkin', true)->get()->sum('total_guest'); 
            } else {

                $total_checked_in['walk_in'] = $bookings->whereDate('scheduled_at', $today)->where('is_walkin', true)->get()->sum('total_guest'); 
                $total_checked_in['walk_in_group'] = $bookings->whereDate('scheduled_at', $today)->where('is_walkin', true)->get()->count(); 
            }
        }

        $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->get()->pluck('id')->toArray();

        if($admin->destination_id) {
            $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->get()->pluck('id')->toArray();
        }

        $collection = Guest::with('visitorType', 'specialFee')->whereIn('book_id', $bookings)->where('main', false)->get();

        if($request->date) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date)->get()->pluck('id')->toArray();

            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date)->get()->pluck('id')->toArray();
            }

            $collection = Guest::with('visitorType', 'specialFee')->where('main', false)->whereIn('book_id', $bookings)->get();
        }

        if($request->destination) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->where('destination_id', $request->destination)->get()->pluck('id')->toArray();
            if($request->date) {
                $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->whereDate('scheduled_at', $request->date)->get()->pluck('id')->toArray();
            }
            $collection = Guest::with('visitorType', 'specialFee')->where('main', false)->whereIn('book_id', $bookings)->get();
        } 

        if($request->experience) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->get()->pluck('id')->toArray();
            if($request->date) {
                $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->whereDate('scheduled_at', $request->date)->get()->pluck('id')->toArray();
            }
            $collection = Guest::with('visitorType', 'specialFee')->where('main', false)->whereIn('book_id', $bookings)->get();
        }
        // get all the type of visitor of the guests

        $grouped = $collection->groupBy(function($item, $key) {
            return $item->conservationFee ? $item->conservationFee->visitorType->name  : [];
        });

        $visitor_types = $this->renderGraphDigits($grouped, false, 'visitorType');

        // get all the nationality of all guests

        $grouped = $collection->groupBy(function($item, $key) {
            return $item['nationality'];
        });
        $nationalities = $this->renderGraphDigits($grouped, false, 'nationality');

       

        // get all the gender of the guests
        $grouped = $collection->groupBy(function($item, $key) {
            return $item['gender'];
        });

        $gender = $this->renderGraphDigits($grouped, false, 'gender');

        // get all the $source of the book/reservation
        $book = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->get();
        
        if($admin->destination_id) {
            $book = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->get();
        }

        if($request->date) {
            $book = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date)->get();

            if($admin->destination_id) {
                $book = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date)->get();
            }
        }

        if($request->destination) {
            $book = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->where('destination_id', $request->destination)->get();
            if($request->date) {
                $book = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->whereDate('scheduled_at', $request->date)->get();
            }
        } 

        if($request->experience) {
            $book = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->get();
            if($request->date) {
                $book = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->whereDate('scheduled_at', $request->date)->get();
            }
        }

        $grouped = $book->groupBy(function($item, $key) {
            return $item['is_walkin'] == true ? 'Walk-In' : 'Online';
        });

        $source = $this->renderGraphDigits($grouped, true, 'source');
        
        // get all the special fee of the guests
        // $grouped = $collection->groupBy(function($item, $key) {
        //     return $item->special_fee_id === 0 || null ? 0 : ($item->specialFee ? $item->specialFee->name : 0);
        // });
        
        // $special_fees = $this->renderGraphDigits($grouped);
        $grouped = $collection->groupBy(function($item, $key) {
            return $item->conservation_fee_id === 0 || null ? 0 : ($item->conservationFee->specialFee ? $item->conservationFee->specialFee->name : 0);
        });
        
        $special_fees = $this->renderGraphDigits($grouped, false, 'specialFee');
        $revenue = [
            [
                "backgroundColor" => "#007bff",
                "data" => $this->getRevenueTotal($request, 1),
                "label" => "January"
            ],
            [
                "backgroundColor" => "red",
                "data" => $this->getRevenueTotal($request, 2),
                "label" => "February"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 3),
                "label" => "March"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 4),
                "label" => "April"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 5),
                "label" => "May"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 6),
                "label" => "June"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 7),
                "label" => "July"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 8),
                "label" => "August"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 9),
                "label" => "September"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 10),
                "label" => "October"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 11),
                "label" => "November"
            ],
            [
                "backgroundColor" => "green",
                "data" => $this->getRevenueTotal($request, 12),
                "label" => "December"
            ]
        ];

        $ages = [
            [
                "backgroundColor" => !empty(AgeRange::where('id',1)) ? AgeRange::where('id',1)->value('color') : "#5892fc",
                "data" => $this->getGuestAge($request, [0, 9]),
                "label" => "0-9"
            ],
            [
                "backgroundColor" => !empty(AgeRange::where('id',2)) ? AgeRange::where('id',2)->value('color') : "#673ab7",
                "data" => $this->getGuestAge($request, [10, 17]),
                "label" => "10-17"
            ],
            [
                "backgroundColor" => !empty(AgeRange::where('id',3)) ? AgeRange::where('id',3)->value('color') : "#007bff",
                "data" => $this->getGuestAge($request, [18, 25]),
                "label" => "18-25"
            ],
            [
                "backgroundColor" => !empty(AgeRange::where('id',4)) ? AgeRange::where('id',4)->value('color') : "red",
                "data" => $this->getGuestAge($request, [26, 34]),
                "label" => "26-34"
            ],
            [
                "backgroundColor" => !empty(AgeRange::where('id',5)) ? AgeRange::where('id',5)->value('color') : "green",
                "data" => $this->getGuestAge($request, [35, 40]),
                "label" => "35-40"
            ],
            [
                "backgroundColor" => !empty(AgeRange::where('id',6)) ? AgeRange::where('id',6)->value('color') : "yellow",
                "data" => $this->getGuestAge($request, [41, 300]),
                "label" => "41 +"
            ],
        ];

        $today = Carbon::now();

        $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);
        if($admin->destination_id) {
            $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today);
        }
        $capacity['groups'] = $bookings->where('is_walkin', false)->get()->count();
        $capacity['visitors'] = $bookings->where('is_walkin', false)->sum('total_guest');

        if($request->date) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);

            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date);
            }
            $capacity['groups'] = $bookings->where('is_walkin', false)->get()->count();
            $capacity['visitors'] = $bookings->where('is_walkin', false)->sum('total_guest');
        }

        if($request->destination && $request->destination != null) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->whereDate('scheduled_at', $today);
            $capacity['groups'] = $bookings->where('is_walkin', false)->get()->count();
            $capacity['visitors'] = $bookings->where('is_walkin', false)->sum('total_guest');
            if($request->date) {
                $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->whereDate('scheduled_at', $request->date);
                $capacity['groups'] = $bookings->where('is_walkin', false)->get()->count();
                $capacity['visitors'] = $bookings->where('is_walkin', false)->sum('total_guest');
            }
        } 

        if($request->experience) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->whereDate('scheduled_at', $today);
            $capacity['groups'] = $bookings->where('is_walkin', false)->get()->count();
            $capacity['visitors'] = $bookings->where('is_walkin', false)->sum('total_guest');
            if($request->date) {
                $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->whereDate('scheduled_at', $request->date);
                $capacity['groups'] = $bookings->where('is_walkin', false)->get()->count();
                $capacity['visitors'] = $bookings->where('is_walkin', false)->sum('total_guest');
            }
        }

        return [
            'revenue' => $revenue,
            'visitor_types' => $visitor_types,
            'ages' => $ages,
            'nationalities' => $nationalities,
            'gender' => $gender,
            'source' => $source,
            'special_fees' => $special_fees,
            'total' => $total,
            'checked_in_walkin' => $checked_in_walkin,
            'total_checked_in' => $total_checked_in,
            'capacity' => $capacity,
            'admin_user' => $admin_user,
        ];
    }

    public function getRevenueTotal($request, $month) {
        $invoice = null;
        $admin = auth()->guard('admin')->user();
        $admin_user = Admin::where('id', $admin->id)->first();

        if($admin_user->roles[0]['id'] == 4) {
            $invoice = Invoice::whereHas('book', function($book) use ($admin_user) {
                $book->where('destination_id', $admin_user->destination_id);
            })
            ->whereMonth('created_at', $month)
            ->where('is_paid', true)
            ->sum('conservation_fee');
        } else {
            $invoice = Invoice::whereMonth('created_at', $month)->where('is_paid', true)->sum('conservation_fee');
        }
        


        if($request->destination) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->get()->pluck('id')->toArray();

            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->get()->pluck('id')->toArray();
            }
            $invoice = Invoice::whereIn('book_id', $bookings)->whereMonth('created_at', $month)->where('is_paid', true)->sum('conservation_fee');
        } 

        if($request->experience) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->get()->pluck('id')->toArray();
            
            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->where('allocation_id', $request->experience)->get()->pluck('id')->toArray();
            }

            $invoice = Invoice::whereIn('book_id', $bookings)->whereMonth('created_at', $month)->where('is_paid', true)->sum('conservation_fee');
        }

        return $invoice;

    }

    public function getGuestAge($request, $arr) {
        $today = Carbon::now();
        $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->get()->pluck('id')->toArray();
        $admin = auth()->guard('admin')->user();

        if($admin->destination_id) {
            $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->get()->pluck('id')->toArray();
        }

        $guest = Guest::where('main', false)->whereIn('book_id', $bookings)->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR,birthdate,CURDATE())'), $arr)->count();


        if($request->date) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date)->get()->pluck('id')->toArray();

            if($admin->destination_id) {
                $bookings = Book::where('destination_id', $admin->destination_id)->whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date)->get()->pluck('id')->toArray();
            }

            $guest = Guest::where('main', false)->whereIn('book_id', $bookings)->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR,birthdate,CURDATE())'), $arr)->count();
        }

        if($request->destination) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->where('destination_id', $request->destination)->get()->pluck('id')->toArray();
            if($request->date) {
                $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date)->where('destination_id', $request->destination)->get()->pluck('id')->toArray();
            }
            $guest = Guest::where('main', false)->whereIn('book_id', $bookings)->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR,birthdate,CURDATE())'), $arr)->count();
        } 

        if($request->experience) {
            $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $today)->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->get()->pluck('id')->toArray();
            if($request->date) {
                $bookings = Book::whereHas('invoice', function ($query) { $query->where('is_paid', true); })->whereDate('scheduled_at', $request->date)->where('destination_id', $request->destination)->where('allocation_id', $request->experience)->get()->pluck('id')->toArray();
            }
            $guest = Guest::where('main', false)->whereIn('book_id', $bookings)->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR,birthdate,CURDATE())'), $arr)->count();
        }

        return $guest;
    }

    public function renderGraphDigits($grouped, $is_source = false, $type=null) 
    {
        $data = [];
        $groupCount = $grouped->map(function ($item, $key) {
            return collect($item)->count();
        });

        if($is_source) {
            $groupCount = $grouped->map(function ($item, $key) {
                return $item->sum('total_guest');
            });
        }

        foreach($grouped as $key => $group) {
            switch ($type) {
                case 'visitorType':
                    $bgColor = $group[0]->conservationFee ? $group[0]->conservationFee->visitorType->color : '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                    break;

                case 'specialFee':
                    $bgColor = $group[0]->conservationFee->specialFee ? $group[0]->conservationFee->specialFee->color : '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                    break;

                case 'gender':
                    $bgColor = !empty(Gender::where('name', $key)->first()->color) ? Gender::where('name', $key)->first()->color : '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                    break;

                case 'nationality':
                    $bgColor = !empty(Nationality::where('name', $key)->first()->color) ? Nationality::where('name', $key)->first()->color : '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                    break;

                case 'source':
                    $bgColor = !empty(Source::where('name', $key)->first()->color) ? Source::where('name', $key)->first()->color : '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                    break;
                
                default:
                    $bgColor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                    break;
            }

            array_push($data, [
                    "backgroundColor" => $bgColor,
                    "data" => count($group),
                    "label" => $key === 0 || null ? 'None' : $key,
                ]);
        }

        // foreach ($groupCount as $key => $value) {
        //     array_push($data, [
        //             "backgroundColor" => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
        //             "data" => $value,
        //             "label" => $key === 0 || null ? 'None' : $key,
        //         ]);
        // }

        return $data;
    }
}
