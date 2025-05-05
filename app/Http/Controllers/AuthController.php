<?php

namespace App\Http\Controllers; // Namespace declaration- basically tells where this file is located
// This is the AuthController class, which handles user authentication and registration.
use Illuminate\Support\Facades\Auth;
// Hash is a facade for hashing passwords
use Illuminate\Support\Facades\Hash;
use App\Models\User;// User Model
use Illuminate\Http\Request; // Request is a class that handles HTTP requests
use App\Models\voucher; // Voucher Model
use Carbon\Carbon;// Carbon is a library for date and time manipulation
use Illuminate\Support\Str; // Str is a class that provides string manipulation functions
use App\Notifications\NewUserCredentials;


// This is the AuthController class, which handles user authentication and registration.
class AuthController extends Controller
{   

    // This method shows the registration form
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    // This method handles the registration of a new user
    public function register(Request $request)
    {   
        // Check if the user is already authenticated
        // If the user is already logged in, redirect them to the dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        // Validate the incoming request data
        // The validate method checks the request data against the specified rules
        $new_data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:400',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:100',
            'voucher_code'=>'required|string|max:100',
        ]);
        // dd($new_data);
        // Check if the user is already authenticated
        // If the user is already logged in, redirect them to the dashboard
        // Validate the incoming request data
        try {

            $voucher = voucher::where('voucher_code',md5($new_data['voucher_code']))->first(); // Check if the voucher code exists in the database
            // dd($voucher);
            
            if(!$voucher){
                return back()->with('error','Invalid voucher code');
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'voucher_id' => $voucher->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ]);

            // Send email notification with credentials
            $user->notify(new NewUserCredentials($request->password));

            while (true) {
                $datePart = Carbon::now()->format('Ymd');       // YYYYMMDD
                $timePart = Carbon::now()->format('Hi');        // HHMM
                $randomLetters = Str::upper(Str::random(3));    // 3 uppercase letters
    
                $generatedCode = $datePart . $randomLetters . $timePart;
    
                if (!Voucher::where('voucher_code', $generatedCode)->exists()) {
                    break;
                }
            }
    
            // Create a new voucher associated with the newly registered user
            $newVoucher = new Voucher();
            $newVoucher->voucher_code = md5($generatedCode);
            $newVoucher->voucher_type = 'BANKEMP00ASD';
            $newVoucher->employee_id = $user->id;
            $newVoucher->created_at = Carbon::now();
            $newVoucher->updated_at = Carbon::now();
            $newVoucher->save();
                
            return redirect()->route('login')->with('success', 'Registration successful. Please check your email for login credentials.');
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed. Please try again.'. $e->getMessage());
        }
    }

    // This method shows the login form
    public function showloginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // This method handles the login of a user
    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user with the provided credentials
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard')->with('success', 'Welcome back!');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
    }


    // This method handles the logout of a user
    // It invalidates the session and regenerates the CSRF token
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

}
