<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsignmentPrintController extends Controller
{
    /**
     * Display printable invoice / delivery note for consignment.
     */
    public function show(Request $request, Consignment $consignment): View
    {
        $consignment->load(['store', 'items.product']);

        $mode = $request->query('mode', 'dual'); // 'dual' (2-ply per page) or 'single'

        $totalQty = $consignment->items->sum('quantity_dropped');
        $totalValue = $consignment->items->sum(function ($item) {
            return (int) $item->quantity_dropped * (float) $item->price_per_item;
        });

        return view('consignments.print', [
            'consignment' => $consignment,
            'mode' => $mode,
            'totalQty' => $totalQty,
            'totalValue' => $totalValue,
        ]);
    }
}
