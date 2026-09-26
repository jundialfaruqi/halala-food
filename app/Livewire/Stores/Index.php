<?php

namespace App\Livewire\Stores;

use App\Models\Store;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Daftar Toko Mitra')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $routeFilter = '';

    public bool $showModal = false;

    public ?int $storeId = null;

    // Form fields
    public string $name = '';

    public string $owner_name = '';

    public string $phone = '';

    public string $address = '';

    public string $route = '';

    public string $notes = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRouteFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['storeId', 'name', 'owner_name', 'phone', 'address', 'route', 'notes']);
        $this->showModal = true;
    }

    public function openEditModal(Store $store): void
    {
        $this->storeId = $store->id;
        $this->name = $store->name;
        $this->owner_name = $store->owner_name ?? '';
        $this->phone = $store->phone ?? '';
        $this->address = $store->address ?? '';
        $this->route = $store->route ?? '';
        $this->notes = $store->notes ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'route' => 'nullable|string|max:100',
        ]);

        Store::updateOrCreate(
            ['id' => $this->storeId],
            [
                'name' => $this->name,
                'owner_name' => $this->owner_name,
                'phone' => $this->phone,
                'address' => $this->address,
                'route' => $this->route,
                'notes' => $this->notes,
                'is_active' => true,
            ]
        );

        $this->showModal = false;
        session()->flash('message', 'Data toko berhasil disimpan.');
    }

    public function deleteStore(int $id): void
    {
        $store = Store::find($id);
        if ($store) {
            $name = $store->name;
            $store->delete();
            session()->flash('message', "Toko '{$name}' berhasil dihapus.");
        }
    }

    public function render(): View
    {
        $stores = Store::withCount(['consignments' => function ($q) {
            $q->where('status', 'active');
        }])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('owner_name', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            })
            ->when($this->routeFilter, function ($q) {
                $q->where('route', $this->routeFilter);
            })
            ->orderBy('name')
            ->paginate(15);

        $routes = Store::whereNotNull('route')->distinct()->pluck('route');

        return view('livewire.stores.index', [
            'stores' => $stores,
            'routes' => $routes,
        ]);
    }
}
