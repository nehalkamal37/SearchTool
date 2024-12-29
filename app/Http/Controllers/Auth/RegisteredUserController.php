<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        
        
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string'], // Validate the role input
            ]);
        
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role, // Store the role
            ]);
        
            event(new Registered($user));
        
            Auth::login($user);
           // return redirect()->route('pharmacist.dashboard');
              /*Redirect based on role   saveee  for lateer
    if ($user->role === 'pharmacist') {
        return redirect()->route('pharmacist.dashboard');
    } elseif ($user->role === 'administrator') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'technician') {
        return redirect()->route('technician.dashboard');
    }
        */
        switch ($user->role) {
            case 'pharmacist':
                return redirect(RouteServiceProvider::HOME); // Fallback route

               // return redirect()->route('pharmacist.dashboard');
            case 'administrator':
                return redirect(RouteServiceProvider::HOME); // Fallback route
              //  return redirect()->route('admin.dashboard');

            case 'technician':
                return redirect(RouteServiceProvider::HOME); // Fallback route
                //   return redirect()->route('technician.dashboard');
            case 'inventory_manager':
                return redirect(RouteServiceProvider::HOME); // Fallback route
                //   return redirect()->route('inventory.dashboard');
            default:
                return redirect(RouteServiceProvider::HOME); // Fallback route
        }
        }
        
    
}
