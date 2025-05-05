<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\voucher;
use Carbon\Carbon;
use Illuminate\Support\Str;



class AuthController extends Controller
{
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

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

        try {

            $voucher = voucher::where('voucher_code',md5($new_data['voucher_code']))->first();
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
                
            return redirect()->route('login')->with('success', 'Registration successful. Please login.');
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed. Please try again.'. $e->getMessage());
        }
    }

    public function showloginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

}
