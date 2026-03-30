<?php
 
 namespace App\Http\Controllers\Auth;
 
 use App\Http\Controllers\Controller;
use App\Models\User;
 use App\Providers\RouteServiceProvider;
 use Illuminate\Foundation\Auth\AuthenticatesUsers;
 use Session;
 use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
 use Illuminate\Http\Request;
 
 
 class LoginController extends Controller
 {
     /*
     |--------------------------------------------------------------------------
     | Login Controller
     |--------------------------------------------------------------------------
     |
     | This controller handles authenticating users for the application and
     | redirecting them to your home screen. The controller uses a trait
     | to conveniently provide its functionality to your applications.
     |
     */
 
     use AuthenticatesUsers;
 
     /**
      * Where to redirect users after login.
      *
      * @var string
      */
     protected $redirectTo = RouteServiceProvider::HOME;
 
     /**
      * Create a new controller instance.
      *
      * @return void
      */
     public function __construct()
     {
         $this->middleware('guest')->except('logout');
     }
 
    public function username()
    {
        return 'login';
    }

    protected function credentials(Request $request)
    {
        $login = trim((string) $request->input('login'));

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Admin users login via email (admin users do not have CRM role)
            return [
                'email' => $login,
                'password' => $request->input('password'),
                'iRoalId' => null,
            ];
        }

        // CRM users login via mobile number
        return [
            'mobile_number' => preg_replace('/\D+/', '', $login),
            'password' => $request->input('password'),
        ];
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        $login = trim((string) $request->input('login'));

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return $this->guard()->attempt([
                'email' => $login,
                'password' => $request->input('password'),
                'iRoalId' => null,
            ], $request->boolean('remember'));
        }

        $mobile = preg_replace('/\D+/', '', $login);

        $user = User::where('mobile_number', $mobile)
            ->whereNotNull('iRoalId')
            ->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return false;
        }

        $this->guard()->login($user, $request->boolean('remember'));

        return true;
    }

     public function logout(Request $request)
     {
         Auth::logout();
         return view('logout');
     }
 }
