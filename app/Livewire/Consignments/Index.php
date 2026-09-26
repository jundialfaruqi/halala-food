<?php

namespace App\Livewire\Consignments;

use App\Models\Consignment;
use App\Models\Store;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Titip Jual / Konsinyasi Toko')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $routeFilter = '';

    #[Url]
    public string $statusFilter = 'active'; // 'all', 'active', 'completed'

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRouteFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Consignment::with(['store', 'items.product'])
            ->when($this->search, function ($q) {
                $q->whereHas('store', function ($sq) {
                    $sq->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('owner_name', 'like', '%'.$this->search.'%');
                })->orWhere('consignment_number', 'like', '%'.$this->search.'%');
            })
            ->when($this->routeFilter, function ($q) {
                $q->whereHas('store', function ($sq) {
                    $sq->where('route', $this->routeFilter);
                });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('drop_date', 'desc');

        $consignments = $query->paginate(15);
        $routes = Store::whereNotNull('route')->distinct()->pluck('route');

        return view('livewire.consignments.index', [
            'consignments' => $consignments,
            'routes' => $routes,
        ]);
    }
}
