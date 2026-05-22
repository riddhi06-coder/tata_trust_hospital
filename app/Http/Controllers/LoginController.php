<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as PasswordRule;

use App\Models\User;

class LoginController extends Controller
{
    // ----------------------------------------------------------------
    // Dashboard
    // ----------------------------------------------------------------
    public function dashboard()
    {
        return view('backend.dashboard');
    }

    // ----------------------------------------------------------------
    // Login
    // ----------------------------------------------------------------
    public function login()
    {
        return view('backend.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email Id is required',
            'email.email'    => 'Please provide a valid email address',
            'password.required' => 'Password is required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember_token');

        if (Auth::attempt($credentials, $remember)) {
            if (! Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email'))
                    ->with('message', 'Your account has been deactivated. Please contact an administrator.')
                    ->withErrors(['email' => 'Your account has been deactivated.']);
            }

            $request->session()->regenerate();
            return redirect()
                ->intended(route('admin.dashboard'))
                ->with('message', 'You are logged-in successfully.');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('message', 'Credentials do not match our records!')
            ->withErrors(['email' => 'Credentials do not match our records!']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('message', 'You are logged out successfully.');
    }

    // ----------------------------------------------------------------
    // Register
    // ----------------------------------------------------------------
    public function register()
    {
        return view('backend.register');
    }

    public function authenticate_register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ], [
            'name.required'     => 'The name field is required.',
            'email.required'    => 'The email field is required.',
            'email.email'       => 'The email must be a valid email address.',
            'email.unique'      => 'This email is already registered.',
            'password.required' => 'The password field is required.',
            'password.min'      => 'The password must be at least 8 characters.',
            'password.confirmed'=> 'Password confirmation does not match.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('admin.dashboard')
            ->with('message', 'Account created successfully! Welcome '.$user->name.'.');
    }

    // ----------------------------------------------------------------
    // Forgot password — step 1: request a reset link
    // ----------------------------------------------------------------
    public function showForgotPasswordForm()
    {
        return view('backend.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email is required',
            'email.email'    => 'Please provide a valid email address',
            'email.exists'   => 'No account found with that email address',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('message', 'A password reset link has been sent to your email.')
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    // ----------------------------------------------------------------
    // Forgot password — step 2: show the reset form (from email link)
    // ----------------------------------------------------------------
    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('backend.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ], [
            'email.exists'      => 'No account found with that email address',
            'password.confirmed'=> 'Confirm Password must match the New Password',
            'password.min'      => 'Password must be at least 8 characters.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('message', 'Password reset successfully. Please log in.')
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    // ----------------------------------------------------------------
    // Change password — for already-authenticated users
    // ----------------------------------------------------------------
    public function showChangePasswordForm()
    {
        return view('backend.forgot_password');
    }

    public function updatePassword(Request $request)
    {
        if (Auth::check()) {
            $request->validate([
                'current_password' => 'required|string|current_password',
                'password'         => ['required', 'string', 'confirmed', PasswordRule::min(8)],
            ], [
                'current_password.required'      => 'Current password is required',
                'current_password.current_password' => 'Current password is incorrect',
                'password.required'              => 'New Password is required',
                'password.confirmed'             => 'Confirm Password must match the New Password',
                'password.min'                   => 'Password must be at least 8 characters.',
            ]);

            $user = $request->user();
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('admin.dashboard')->with('message', 'Password changed successfully!');
        }

        return $this->sendResetLink($request);
    }
}
