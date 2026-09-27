<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreProductBarcode;
use App\Services\BarcodePdfService;
use App\Services\BarcodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BarcodePrintController extends Controller
{
    /**
     * Render printable A4 barcode sheet page.
     */
    public function show(Request $request): View
    {
        $data = $this->prepareBarcodeSheetData($request);

        return view('barcodes.print', $data);
    }

    /**
     * Download crisp vector A4 PDF file directly.
     */
    public function downloadPdf(Request $request): Response
    {
        $data = $this->prepareBarcodeSheetData($request);
        $pdfContent = BarcodePdfService::generate($data);

        $filename = 'Barcode-Cetak-'.date('Ymd-His').'.pdf';
        if (! empty($data['storeName'])) {
            $safeStore = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['storeName']);
            $filename = "Barcode-{$safeStore}-".date('Ymd').'.pdf';
        }

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download standalone offline-ready HTML file for Flashdisk.
     */
    public function downloadHtml(Request $request): Response
    {
        $data = $this->prepareBarcodeSheetData($request);

        $htmlContent = view('barcodes.standalone-export', $data)->render();

        $filename = 'Barcode-Cetak-'.date('Ymd-His').'.html';
        if (! empty($data['storeName'])) {
            $safeStore = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['storeName']);
            $filename = "Barcode-{$safeStore}-".date('Ymd').'.html';
        }

        return response($htmlContent, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Parse and build printable label items and grid configuration.
     *
     * @return array<string, mixed>
     */
    protected function prepareBarcodeSheetData(Request $request): array
    {
        $mode = $request->query('mode', 'single'); // 'single', 'store_all', 'batch'
        $barcodeId = $request->query('barcode_id');
        $storeId = $request->query('store_id');
        $copies = max(1, (int) $request->query('copies', 24));
        $batch = $request->query('batch', []); // array of ['id' => ..., 'qty' => ...]

        $template = $request->query('template', 'a4_3x8');
        $columns = (int) $request->query('cols', 3);
        $rows = (int) $request->query('rows', 8);
        $labelWidth = (float) $request->query('w', 65.0);
        $labelHeight = (float) $request->query('h', 35.0);
        $marginTop = (float) $request->query('mt', 8.0);
        $marginLeft = (float) $request->query('ml', 8.0);
        $gapX = (float) $request->query('gx', 3.0);
        $gapY = (float) $request->query('gy', 2.0);

        $showProductName = $request->boolean('show_prod', true);
        $showStoreName = $request->boolean('show_store', true);
        $showPrice = $request->boolean('show_price', true);
        $showBarcodeText = $request->boolean('show_text', true);
        $showCutBorders = $request->boolean('show_border', true);
        $barcodeHeight = (int) $request->query('bh', 36);

        $labels = [];
        $storeName = '';

        if ($mode === 'single' && $barcodeId) {
            $item = StoreProductBarcode::with(['store', 'product'])->find($barcodeId);
            if ($item) {
                $storeName = $item->store->name;
                $svg = BarcodeService::getSvg($item->barcode, $item->barcode_type, $barcodeHeight, 1.4, $showBarcodeText);
                for ($i = 0; $i < $copies; $i++) {
                    $labels[] = [
                        'item' => $item,
                        'svg' => $svg,
                    ];
                }
            }
        } elseif ($mode === 'store_all' && $storeId) {
            $store = Store::find($storeId);
            if ($store) {
                $storeName = $store->name;
            }
            $items = StoreProductBarcode::with(['store', 'product'])
                ->where('store_id', $storeId)
                ->get();

            $perItemCount = max(1, (int) floor(($columns * $rows) / max(1, $items->count())));
            foreach ($items as $item) {
                $svg = BarcodeService::getSvg($item->barcode, $item->barcode_type, $barcodeHeight, 1.4, $showBarcodeText);
                for ($i = 0; $i < $perItemCount; $i++) {
                    $labels[] = [
                        'item' => $item,
                        'svg' => $svg,
                    ];
                }
            }
        } elseif ($mode === 'batch' && ! empty($batch)) {
            $all = StoreProductBarcode::with(['store', 'product'])->get()->keyBy('id');
            foreach ($batch as $b) {
                $bId = $b['id'] ?? null;
                $qty = (int) ($b['qty'] ?? 0);
                if ($bId && isset($all[$bId]) && $qty > 0) {
                    $item = $all[$bId];
                    if (empty($storeName)) {
                        $storeName = $item->store->name;
                    }
                    $svg = BarcodeService::getSvg($item->barcode, $item->barcode_type, $barcodeHeight, 1.4, $showBarcodeText);
                    for ($i = 0; $i < $qty; $i++) {
                        $labels[] = [
                            'item' => $item,
                            'svg' => $svg,
                        ];
                    }
                }
            }
        }

        return [
            'labels' => $labels,
            'storeName' => $storeName,
            'template' => $template,
            'columns' => $columns,
            'rows' => $rows,
            'labelWidth' => $labelWidth,
            'labelHeight' => $labelHeight,
            'marginTop' => $marginTop,
            'marginLeft' => $marginLeft,
            'gapX' => $gapX,
            'gapY' => $gapY,
            'showProductName' => $showProductName,
            'showStoreName' => $showStoreName,
            'showPrice' => $showPrice,
            'showBarcodeText' => $showBarcodeText,
            'showCutBorders' => $showCutBorders,
            'barcodeHeight' => $barcodeHeight,
            'mode' => $mode,
        ];
    }
}
