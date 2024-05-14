<?php

namespace App\Http\Controllers\Web\Pages;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Foundation\Inspiring;

use App\Models\Pages\Page;
use App\Models\Pages\PageItem;
use App\Models\Faqs\Faq;
use App\Models\Pages\AboutUs;
use App\Models\Pages\AboutUsFrameThree;
use App\Models\Pages\Team;
use App\Models\Allocations\Allocation;
use App\Models\Books\Book;
use App\Models\Carousels\HomeBanner;
use App\Models\Tabbings\AboutInfo;


use App\Models\Destinations\Destination;
use App\Models\Types\VisitorType;
use App\Models\Genders\Gender;
use Webpatser\Countries\Countries;

use App\Models\Payments\Payment;

use App\Models\Invoices\Invoice;
use App\Models\Users\User;

use App\Models\Copywritings\Copywriting;

use Carbon\Carbon;

class PageController extends Controller
{

	/*
	* Show Stylesheet
	*/
	public function showStylesheet() {
        return view('web.pages.stylesheet', [

        ]);
	}

    public function viewMail() {
        $invoice = Invoice::latest()->first();
        return (new \App\Notifications\Web\Paypal\UserInvoicePaid($invoice))
                ->toMail(User::first());
    }

	/*
	* Show Home
	*/
	public function showHome() {

        $data = $this->getPageData('home');
    	$destination = $this->formatData();
        $twitter = PageItem::where('slug', 'twitter')->first();
        $fb = PageItem::where('slug', 'facebook')->first();
        $insta = PageItem::where('slug', 'instagram')->first();
        $youtube = PageItem::where('slug', 'youtube')->first();

        return view('web.pages.home', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'home_banners' => HomeBanner::all(),
        	'about_infos' => AboutInfo::all(),
        	'destination' => json_encode($destination),
        	'page_scripts'=> 'home',
        	'fb' => $fb,
        	'twitter' => $twitter,
        	'insta' => $insta,
        	'youtube' => $youtube,
        ]));

	}

	/*
	* Show About Us
	*/
	public function showAboutUs() {

        $data = $this->getPageData('about');

        $teams = $this->getFrameTwoContent(Team::where('type', 0)->get());
        $collaborators = $this->getFrameTwoContent(Team::where('type', 1)->get());
        $advisors = $this->getFrameTwoContent(Team::where('type', 2)->get());

        $frame_threes = $this->getFrameThreeContent(AboutUsFrameThree::all());

        return view('web.pages.about-us', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'teams' => $teams,
        	'collaborators' => $collaborators,
        	'advisors' => $advisors,
        	'frame_threes' => $frame_threes,
        	'page_scripts'=> 'about'
        ]));

	}

	public function getFrameTwoContent($datas) {
		$result = [];
		foreach ($datas as $data) {
			array_push($result, [
				'name' => $data->name,
				'role' => $data->renderTypeLabel(),
				'description' => $data->description,
				'image_path' => $data->renderImagePath()
			]);
		}

		return $result;
	}

	public function getFrameThreeContent($datas) {
		$result = [];
		foreach ($datas as $data) {
			array_push($result, [
				'title' => $data->title,
				'description' => $data->description,
				'image_path' => $data->renderImagePath()
			]);
		}

		return $result;
	}

	/*
	* Show Destinations, Destinations Info and Request A Visit
	*/
	public function showDestinations() {

        $data = $this->getPageData('destination');

		$result = [];
		/* Masungi Georeserve ID is 5; Exclude Masungi from Available List of Destinations*/
        $destinations = Destination::where('id', '!=', 5)->with('experiences')->get();
        $destination_info = Destination::orderBy('id', 'ASC')->get();

        foreach ($destinations as $key => $destination) {
        	array_push($result, [
        		'destination' => $destination,
        		'destination_info' => $destination_info,
        		'id' => $destination->id,
        		'name' => $destination->name,
        		'short_description' => str_limit($destination->overview, 70),
        		'capacity' => $destination->capacity,
        		'image' => optional($destination->pictures()->first())->renderImagePath(),
        		'is_available' => $destination->is_available,
        		'requestVisitUrl' => $destination->renderRequestVisitUrl(),
        		'viewDestinationUrl' => $destination->renderViewDestinationUrl(),
        	]);
        }

        return view('web.pages.destination.destinations', array_merge($data, [
        	'destinations' => $result,
        	'page_scripts'=> 'destinations'
        ]));
	}

	public function showDestinationsInfo($id) {
        $data = $this->getPageData('destination');
        $selected_destination = Destination::with('allocations')->find($id);
        $selected_destination['request_url'] = $selected_destination->renderRequestVisitUrl();
        $selected_destination['is_available_for_request'] = $selected_destination->is_available ? true : false;

        return view('web.pages.destination.destinations-info', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'selected_destination' => $selected_destination,
        ]));

	}

	public function showRequestToVisit($id, $name) {
		$destination = Destination::find($id);
		$getFee = $destination->allocations->map(function ($allocation) {
		 $allocation->fees = $allocation->conservationFees()->whereNotNull('visitor_type_id')->get();
		 return $allocation;
		});

        $data = $this->getPageData('destination');
        $totalReserved = Book::where('destination_id', $id)->sum('total_guest');
        $totalReservation = $destination->capacity_per_day;
        $destination->totalReserved = $totalReserved;
        $destination->totalReservation = $totalReservation;
        $destination->availableSeat = $totalReservation - $totalReserved;

       $destination->conservationFees = $this->getFees($getFee);

		if(!auth()->guard('web')->check()) {
	       	session(['destination' => $destination]);
	       	return redirect()->route('web.login');
		}

		$destination->image = optional($destination->pictures()->first())->renderImagePath();
		$destination->dateBlock = $destination->getBlockedDates();
		$result = $destination->getFormattedData();
		$visitor_types = VisitorType::all();
		$genders = Gender::all();
		$countries = Countries::orderBy('citizenship', 'asc')->get();

		$info['conservation_fee_info'] = PageItem::where('slug', 'conservation_fee_info')->first() ? PageItem::where('slug', 'conservation_fee_info')->first()->content : null;
		$info['platform_fee_info'] = PageItem::where('slug', 'platform_fee_info')->first() ? PageItem::where('slug', 'platform_fee_info')->first()->content : null;
		$info['transaction_fee_info'] = PageItem::where('slug', 'transaction_fee_info')->first() ? PageItem::where('slug', 'transaction_fee_info')->first()->content : null;

		$transaction_fees = Payment::all();

		$copywriting = Copywriting::where('slug', 'booking-disclaimer')->first();

        return view('web.pages.destination.request-to-visit', array_merge($data, [
        	'quote' => Inspiring::quote(),
           	'destination' => $destination,
        	'visitor_types' => $visitor_types,
        	'genders' => $genders,
        	'countries' => $countries,
        	'transaction_fees' => $transaction_fees,
        	'items' => json_encode($result),
        	'page_scripts'=> 'requestToVisit',
			'info' => json_encode($info),
			'copywriting' => strip_tags($copywriting),
        ]));

	}

	public function getFees($destinations) {
		$arr = [];

        foreach ($destinations as $allocation) {
			foreach ($allocation->fees as $fee) {
				array_push($arr, [
					'id' => $fee->id,
					'experience_id' => $fee->experience_id,
					'display_name' => $fee->name,
					'weekday_fee' => $fee->weekday_fee,
					'weekend_fee' => $fee->weekend_fee,
					'special_fee_id' => $fee->special_fee_id,
					'visitor_type_id' => $fee->visitor_type_id,
				]);
			}
		}

		return $arr;
	}

	/*
	* Show Faqs
	*/
	public function showFaqs() {

        $data = $this->getPageData('faqs');
		$visitors = Faq::where('type', 'VISITOR')->get();
		$managers = Faq::where('type', 'DESTINATION MANAGER')->get();
        // dd($data);
        return view('web.pages.faqs', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'visitors'=> $visitors,
        	'managers'=> $managers,
        	'page_scripts'=> 'faqs'
        ]));
	}

	/*
	* Show Contact Us
	*/
	public function showContactUs() {

        $data = $this->getPageData('contact_us');
        $twitter = PageItem::where('slug', 'twitter')->first();
        $fb = PageItem::where('slug', 'facebook')->first();
        $insta = PageItem::where('slug', 'instagram')->first();
        $youtube = PageItem::where('slug', 'youtube')->first();

        return view('web.pages.contact-us', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'page_scripts'=> 'contact-us',
        	'twitter' => $twitter,
        	'fb' => $fb,
        	'insta' => $insta,
        	'youtube' => $youtube,
        ]));

	}

	/*
	* Show Login, Sign Up, Forgot Password and Reset Password
	*/
	public function showLogin() {
        return view('web.pages.auth.login', [
        	'page_scripts'=> 'login'
        ]);
	}

	public function showSignUp() {

        $data = $this->getPageData('login');

        return view('web.pages.auth.sign-up', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'page_scripts'=> 'sign-up'
        ]));

	}

	public function showForgotPassword() {

        $data = $this->getPageData('login');

        return view('web.pages.auth.forgot-password', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'page_scripts'=> 'forgot-password'
        ]));

	}

	public function showResetPassword() {

        $data = $this->getPageData('login');

        return view('web.pages.auth.reset-password', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'page_scripts'=> 'reset-password'
        ]));

	}

	/*
	* Show Dashboard, Profile
	*/
	public function showDashboard() {

        $data = $this->getPageData('dashboard');

        return view('web.pages.user.dashboard', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'page_scripts'=> 'dashboard'
        ]));

	}

	public function showProfile() {

        $data = $this->getPageData('profile');

        return view('web.pages.user.profile', array_merge($data, [
        	'quote' => Inspiring::quote(),
        	'page_scripts'=> 'profile'
        ]));

	}

	/*
	* Show Privacy Policy
	*/
	public function showPrivacyPolicy() {

		$page = Page::where('slug', 'privacy_policy')->first();
		$data = $page->getData();

        return view('web.pages.privacy-policy', [
        	'data' => $data,
        ]);
	}

	public function formatData() {
		$result = [];

		$destinations = collect(Destination::get())->map(function($destination) {
			$destination->viewDestinationUrl = $destination->renderViewDestinationUrl();
			return $destination;
		});

		foreach ($destinations as $destination) {
			array_push($result, [
				'destination' => $destination,
				'experiences' => $destination->experiences,
				'picture' => optional($destination->pictures()->first())->renderImagePath()
			]);
		}

		return $result;
	}

	public function fetchDestination() {

        $destinations = Destination::all();
        return response()->json([
        	'destinations' => $destinations
        ]);
	}
	/* Get Page Data */
	protected function getPageData($slug) {
		$item = Page::where('slug', $slug)->firstOrFail();
		return $item->getData();
	}

	public function frontlinerSuccessPage() {
		return view('web.pages.management.password-reset-success');
	}

	public function getTimeSlot(Request $request) {
		$allocation = Allocation::find($request->allocationSelected);
		$reserveds = Book::where(['allocation_id' => $allocation->id])->get();
		$total_reservation = $allocation->capacities->first() ? $allocation->capacities->first()->online : null;
		if($total_reservation < $allocation->destination->capacity_per_day) {
			$total_reservation = $allocation->destination->capacity_per_day;
		}
		$result = [];

		foreach ($reserveds as $reserved) {
			$is_reserved = $reserveds->where('scheduled_at', $reserved->scheduled_at)->count();
			if($is_reserved >= $total_reservation) {
				array_push($result, [
					Carbon::parse($reserved->scheduled_at)->format('Y-m-d')
				]);
			}
		}

		return collect($result)->flatten();

	}

	public function showPolicies($type) {
		$page = Page::where('slug', $type)->first() ?? 'We still working on it!';
		$data = $page->getData();

		return view('web.pages.privacy-policy', [
			'data' => $data
		]);
	}
}
