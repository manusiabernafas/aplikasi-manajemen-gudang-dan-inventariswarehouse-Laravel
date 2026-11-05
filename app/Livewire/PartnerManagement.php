<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PartnerManagement extends Component
{
    public string $activeTab = 'suppliers';

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $code = '';

    public string $name = '';

    public string $contactPerson = '';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $errorMessage = '';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function customers()
    {
        return Customer::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->errorMessage = '';
        $this->editingId = $id;

        if ($id) {
            $model = $this->activeTab === 'suppliers' ? Supplier::findOrFail($id) : Customer::findOrFail($id);
            $this->code = $model->code;
            $this->name = $model->name;
            $this->contactPerson = (string) $model->contact_person;
            $this->phone = (string) $model->phone;
            $this->email = (string) $model->email;
            $this->address = (string) $model->address;
        } else {
            $this->code = '';
            $this->name = '';
            $this->contactPerson = '';
            $this->phone = '';
            $this->email = '';
            $this->address = '';
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $table = $this->activeTab === 'suppliers' ? 'suppliers' : 'customers';

        $this->validate([
            'code' => ['required', 'string', 'max:20', "unique:{$table},code,{$this->editingId}"],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $model = $this->activeTab === 'suppliers' ? Supplier::class : Customer::class;

        $model::updateOrCreate(['id' => $this->editingId], [
            'code' => $this->code,
            'name' => $this->name,
            'contact_person' => $this->contactPerson,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'is_active' => true,
        ]);

        $this->showModal = false;
        unset($this->suppliers, $this->customers);
    }

    public function delete(int $id): void
    {
        try {
            $model = $this->activeTab === 'suppliers' ? Supplier::class : Customer::class;
            $model::findOrFail($id)->delete();
            unset($this->suppliers, $this->customers);
        } catch (QueryException) {
            $this->errorMessage = 'Data tidak dapat dihapus karena masih memiliki transaksi terkait.';
        }
    }

    public function render()
    {
        return view('livewire.partner-management')->layout('layouts.app', ['title' => 'Supplier & Customer']);
    }
}
