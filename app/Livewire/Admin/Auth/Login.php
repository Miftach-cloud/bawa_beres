<?php

namespace App\Livewire\Admin\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Login Admin — Bawa Beres')]
class Login extends Component
{
    #[Validate('required|string')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        $input = trim($this->email);
        $resolvedEmail = str_contains($input, '@') ? $input : "{$input}@bawaberes.id";

        $throttleKey = Str::lower($resolvedEmail).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Try with resolved email or exact input
        $credentials = ['email' => $resolvedEmail, 'password' => $this->password];

        if (! Auth::attempt($credentials, $this->remember)) {
            // Also fallback attempt with raw input if different
            if ($resolvedEmail !== $input && Auth::attempt(['email' => $input, 'password' => $this->password], $this->remember)) {
                // Succeeded with raw email
            } else {
                RateLimiter::hit($throttleKey);

                throw ValidationException::withMessages([
                    'email' => 'Kombinasi akun dan password tidak sesuai.',
                ]);
            }
        }

        $user = Auth::user();
        if (! $user || Gate::forUser($user)->denies('access-admin')) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Akun tidak memiliki izin akses ke sistem internal.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function render()
    {
        return view('livewire.admin.auth.login');
    }
}
