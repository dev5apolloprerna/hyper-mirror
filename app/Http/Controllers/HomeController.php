<?php
 
 namespace App\Http\Controllers;
 
 use App\Models\Customer;
 use App\Models\Lead;
 use App\Models\Product;
 use App\Models\ProductCategory;
 use App\Models\Showroom;
 use App\Models\User;
 use App\Models\UserShowroom;

 use App\Support\LeadWorkflow;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Facades\Hash;
 use App\Models\Role;
 
 class HomeController extends Controller
 {
     public function __construct()
     {
         $this->middleware('auth');
     }
 
     public function index(Request $request)
     {
        try {
            $roleSlug = optional(auth()->user()->crmRole)->slug;
             $customerCount = Customer::count();
             $categoryCount = ProductCategory::count();
             $productCount = Product::count();
             $showroomCount = Showroom::count();
             $userShowroomCount = UserShowroom::count();
 
            $leadBaseQuery = Lead::query();
            if ($roleSlug !== 'storemanager' && $roleSlug) {
                $leadBaseQuery->whereIn('iCurrentLeadStatus', LeadWorkflow::roleQueueStatuses($roleSlug));
            }
 
            $today = now()->toDateString();
            $todayFollowupCount = (clone $leadBaseQuery)->whereDate('NetFollowupdate', $today)->count();
            $overdueFollowupCount = (clone $leadBaseQuery)->whereDate('NetFollowupdate', '<', $today)->count();

            $statusCards = collect(LeadWorkflow::dashboardStatuses($roleSlug))->map(function ($status) use ($leadBaseQuery) {
                return [
                    'status' => $status,
                    'count' => (clone $leadBaseQuery)->where('iCurrentLeadStatus', $status)->count(),
                ];
            })->all();

            $fittingCollectionThisMonth = 0;
            if ($roleSlug === 'fitting') {
                $fittingCollectionThisMonth = Lead::where('isFittingChargeIncluded', 1)
                    ->where('iCurrentLeadStatus', LeadWorkflow::STATUS_FITTING_DONE)
                    ->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)
                    ->sum('iFittingCharges');
            }
 
            return view('home', compact(
                'customerCount',
                'categoryCount',
                'productCount',
                'showroomCount',
                'userShowroomCount',
                'todayFollowupCount',
                'overdueFollowupCount',
                'statusCards',
                'roleSlug',
                'fittingCollectionThisMonth'
            ));
         } catch (\Exception $e) {
            report($e);
            return false;
         }
     }
 
     public function getProfile()
     {
        try {
            $session = Auth::user()->id;
            $users = User::where('users.id', $session)->first();
 
            return view('profile', compact('users'));
        } catch (\Exception $e) {
            report($e);
            return false;
        }
     }
     public function EditProfile()
     {
        try {
            $roles = Role::where('id', '!=', '1')->get();
 
            return view('Editprofile', compact('roles'));
         } catch (\Exception $e) {
            report($e);
            return false;
        }
     }
    public function updateProfile(Request $request)
     {
         $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
             'mobile_number' => 'required|numeric|digits:10',
         ]);
 
         try {
             DB::beginTransaction();
 
             User::whereId(auth()->user()->id)->update([
                 'first_name' => $request->first_name,
                 'last_name' => $request->last_name,
                 'mobile_number' => $request->mobile_number,
             ]);
 
             DB::commit();
 
             return back()->with('success', 'Profile Updated Successfully.');
         } catch (\Throwable $th) {
             DB::rollBack();
             return back()->with('error', $th->getMessage());
         }
     }
 
     public function changePassword(Request $request)
     {
        try {
            $session = Auth::user()->id;
            $user = User::where('id', '=', $session)->where(['status' => 1])->first();

            if (Hash::check($request->current_password, $user->password)) {
                $newpassword = $request->new_password;
                $confirmpassword = $request->new_confirm_password;

                if ($newpassword == $confirmpassword) {
                    DB::table('users')
                        ->where(['status' => 1, 'id' => $session])
                        ->update([
                            'password' => Hash::make($confirmpassword),
                        ]);
                    Auth::logout();
                    return redirect()->route('login')->with('success', 'User Password Updated Successfully.');
                }

                 return back()->with('error', 'password and confirm password does not match');
             }

             return back()->with('error', 'Current Password does not match');
         } catch (\Exception $e) {
            report($e);
            return false;
        }
     }
 }
