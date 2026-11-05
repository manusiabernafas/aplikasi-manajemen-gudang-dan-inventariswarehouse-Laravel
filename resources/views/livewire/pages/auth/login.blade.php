<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="text-xl font-bold text-slate-900">Masuk ke akun Anda</h2>
    <p class="mt-1 text-sm text-slate-500">Kelola gudang dan inventaris Anda di WMS Nexus.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form wire:submit="login" class="mt-6 space-y-4">
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="form.email" id="email" class="mt-1.5" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input wire:model="form.password" id="password" class="mt-1.5"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center gap-2">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="text-sm text-slate-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-700" href="{{ route('password.request') }}" wire:navigate>
                    Lupa password?
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            Masuk
        </x-primary-button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-700" wire:navigate>Daftar</a>
            </p>
        @endif
    </form>
</div>
