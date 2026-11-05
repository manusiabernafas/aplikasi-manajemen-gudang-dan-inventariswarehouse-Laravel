<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="text-xl font-bold text-slate-900">Konfirmasi Password</h2>
    <p class="mt-1 text-sm text-slate-500">
        Ini adalah area aman. Mohon konfirmasi password Anda sebelum melanjutkan.
    </p>

    <form wire:submit="confirmPassword" class="mt-6 space-y-4">
        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input wire:model="password"
                          id="password"
                          class="mt-1.5"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <x-primary-button class="w-full">
            Konfirmasi
        </x-primary-button>
    </form>
</div>
