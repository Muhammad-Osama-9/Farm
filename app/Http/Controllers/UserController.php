<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    public function register(Request $request)
    {
        $response = $request->validate([
            'email' => ['required'],
            'gender' => 'required',
            'name' => ['required'],
            'password' => ['required']
        ]);
        $response['password'] = bcrypt($response['password']);
        User::create($response);

        return redirect('/')->with('success', 'Registration successful! Please login.');

    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->intended('/');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            return redirect('/dashboard'); // Redirect to dashboard after login
        }

        return back()->with('error', 'Invalid email or password.');
    }

}
