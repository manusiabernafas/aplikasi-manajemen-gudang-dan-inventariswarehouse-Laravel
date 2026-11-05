<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <h2 class="text-xl font-bold text-slate-900">Lupa password?</h2>
    <p class="mt-1 text-sm text-slate-500">
        Masukkan email Anda dan kami akan mengirimkan tautan untuk membuat password baru.
    </p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="mt-6 space-y-4">
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="email" id="email" class="mt-1.5" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <x-primary-button class="w-full">
            Kirim Tautan Reset Password
        </x-primary-button>

        <p class="text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-700" wire:navigate>&larr; Kembali ke halaman masuk</a>
        </p>
    </form>
</div>
