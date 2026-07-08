<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $query = Invoice::with('company')->latest();

        if (Auth::user()->role === 'partner') {
            $companyIds = Company::where('partner_id', Auth::id())->pluck('id');
            $query->whereIn('company_id', $companyIds);
        }

        $invoices = $query->get();

        $totalRevenue = $invoices->where('status', 'paid')->sum('total_amount');
        $pendingRevenue = $invoices->where('status', 'unpaid')->sum('total_amount');

        return view('superadmin.invoices.index', compact('invoices', 'totalRevenue', 'pendingRevenue'));
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::with('company')->findOrFail($id);

        if (Auth::user()->role === 'partner') {
            abort_unless(
                $invoice->company && $invoice->company->partner_id === Auth::id(),
                403
            );
        }

        $invoice->update(['status' => 'paid']);

        if ($invoice->company) {
            $invoice->company->update([
                'payment_status' => 'paid',
                'status'         => 'active',
            ]);
        }

        return back()->with('success', 'Invoice #' . $invoice->invoice_number . ' marked as PAID.');
    }
}
