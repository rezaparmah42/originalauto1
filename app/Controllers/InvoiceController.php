<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Vehicle;

class InvoiceController extends Controller
{
    private $invoiceModel;
    private $orderModel;
    private $vehicleModel;

    public function __construct()
    {
        $this->invoiceModel = new Invoice();
        $this->orderModel = new Order();
        $this->vehicleModel = new Vehicle();
    }

    public function show($id)
    {
        requireCustomer();

        $invoice = $this->invoiceModel->findById((int) $id);
        if (!$invoice) {
            redirect(SITE_URL . '/orders/history');
        }

        $order = $this->orderModel->find((int) ($invoice['order_id'] ?? 0));
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/orders/history');
        }

        $items = $this->orderModel->getItems((int) ($invoice['order_id'] ?? 0));
        $this->view('invoices/show', [
            'invoice' => $invoice,
            'order' => $order,
            'items' => $items,
        ]);
    }

    public function downloadPdf($id)
    {
        requireCustomer();

        $invoice = $this->invoiceModel->findById((int) $id);
        if (!$invoice) {
            redirect(SITE_URL . '/orders/history');
        }

        $order = $this->orderModel->find((int) ($invoice['order_id'] ?? 0));
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/orders/history');
        }

        $items = $this->orderModel->getItems((int) ($invoice['order_id'] ?? 0));
        $html = '<h1>Invoice</h1><p>Invoice #: ' . e($invoice['invoice_number'] ?? '-') . '</p>';
        foreach ($items as $item) {
            $html .= '<p>' . e($item['title_fa'] ?? $item['title_en'] ?? '-') . ' - ' . e((int) ($item['quantity'] ?? 0)) . ' x ' . e($item['price'] ?? 0) . '</p>';
        }
        $html .= '<p>Total: ' . e($invoice['amount'] ?? ($order['total_amount'] ?? 0)) . '</p>';

        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="invoice-' . (int) $id . '.pdf"');
            echo $dompdf->output();
            exit;
        }

        $pdf = $this->buildMinimalPdf($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="invoice-' . (int) $id . '.pdf"');
        echo $pdf;
        exit;
    }

    private function buildMinimalPdf($html)
    {
        $safeText = preg_replace('/\s+/', ' ', strip_tags((string) $html));
        $safeText = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $safeText);
        $lines = preg_split('/(?<=\.)\s+|\n/', $safeText, -1, PREG_SPLIT_NO_EMPTY);

        $content = "BT\n/F1 12 Tf\n50 750 Td\n";
        foreach ($lines as $index => $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            $content .= '50 ' . (720 - ($index * 18)) . ' Td ('.$line.') Tj\n';
        }
        $content .= "ET\n";

        $objects = "<< /Type /Catalog /Pages 2 0 R >>\n";
        $objects .= "<< /Type /Pages /Kids [3 0 R] /Count 1 >>\n";
        $objects .= "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\n";
        $objects .= "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream\n";
        $objects .= "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach (explode("\n", $objects) as $idx => $chunk) {
            $offsets[] = strlen($pdf);
            $pdf .= ($idx + 1) . " 0 obj\n" . $chunk . "\nendobj\n";
        }
        $xrefStart = strlen($pdf);
        $pdf .= "xref\n0 " . (count($offsets)) . "\n0000000000 65535 f \n";
        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer\n<< /Size " . (count($offsets)) . " /Root 1 0 R >>\nstartxref\n" . $xrefStart . "\n%%EOF";

        return $pdf;
    }

    public function adminIndex()
    {
        requireLogin();
        $this->view('admin/invoices/index', [
            'invoices' => $this->invoiceModel->getAll(),
        ]);
    }

    public function adminShow($id)
    {
        requireLogin();
        $invoice = $this->invoiceModel->findById((int) $id);
        if (!$invoice) {
            redirect(SITE_URL . '/admin/invoices');
        }

        $order = $this->orderModel->find((int) ($invoice['order_id'] ?? 0));
        $items = $this->orderModel->getItems((int) ($invoice['order_id'] ?? 0));
        $this->view('admin/invoices/show', [
            'invoice' => $invoice,
            'order' => $order,
            'items' => $items,
        ]);
    }
}
