<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <h2 class="text-xl font-bold text-slate-900">Verifikasi Email</h2>
    <p class="mt-2 text-sm text-slate-500">
        Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi email Anda dengan mengklik tautan yang baru saja kami kirimkan. Jika belum menerima email, kami akan mengirimkan lagi.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <x-primary-button wire:click="sendVerification">
            Kirim Ulang Email Verifikasi
        </x-primary-button>

        <button wire:click="logout" type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-700">
            Keluar
        </button>
    </div>
</div>
