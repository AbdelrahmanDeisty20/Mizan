<?php

namespace App\Services;

use App\Http\Resources\FinancialReceiptResource;
use App\Models\FinancialReceipt;
use App\Models\LegalCase;
use App\Traits\ApiResponse;

class FinancialReceiptService
{
    use ApiResponse;

    /**
     * Check if the authenticated user is a lawyer.
     */
    private function authorizeAsLawyer(): ?\Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'lawyer') {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        return null;
    }

    /**
     * Generate unique receipt number like REC-2026-001
     */
    private function generateReceiptNumber(): string
    {
        $year = date('Y');
        $latest = FinancialReceipt::whereYear('created_at', $year)->latest('id')->first();
        $sequence = $latest ? ((int) substr($latest->receipt_number, -4)) + 1 : 1;

        return sprintf('REC-%s-%04d', $year, $sequence);
    }

    /**
     * Recalculate legal case paid and remaining fees.
     */
    private function syncLegalCaseFees(?int $legalCaseId): void
    {
        if (! $legalCaseId) return;

        $legalCase = LegalCase::find($legalCaseId);
        if (! $legalCase) return;

        $totalPaid = FinancialReceipt::where('legal_case_id', $legalCaseId)->sum('amount');
        $remaining = max(0, $legalCase->total_fees - $totalPaid);

        $legalCase->update([
            'paid_fees'      => $totalPaid,
            'remaining_fees' => $remaining,
        ]);
    }

    /**
     * List all financial receipts for the authenticated office.
     */
    public function index()
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $query = FinancialReceipt::where('office_id', auth()->user()->office_id)
            ->with(['client', 'legalCase', 'user']);

        if (request()->filled('client_id')) {
            $query->where('client_id', request()->get('client_id'));
        }

        if (request()->filled('legal_case_id')) {
            $query->where('legal_case_id', request()->get('legal_case_id'));
        }

        if (request()->filled('user_id')) {
            $query->where('user_id', request()->get('user_id'));
        }

        $perPage = request()->get('per_page', 10);
        $receipts = $query->latest('date')->latest('id')->paginate($perPage);

        return $this->paginated(FinancialReceiptResource::class, $receipts, __('messages.success'));
    }

    /**
     * Show a single financial receipt.
     */
    public function show(FinancialReceipt $financialReceipt)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($financialReceipt->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new FinancialReceiptResource($financialReceipt->load(['client', 'legalCase', 'user'])),
        ], 200);
    }

    /**
     * Store a newly created financial receipt.
     */
    public function store(array $data)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        $receipt = FinancialReceipt::create([
            'office_id'      => $user->office_id,
            'client_id'      => $data['client_id'] ?? null,
            'legal_case_id'  => $data['legal_case_id'] ?? null,
            'user_id'        => $data['user_id'] ?? $user->id,
            'receipt_number' => $data['receipt_number'] ?? $this->generateReceiptNumber(),
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'] ?? 'نقدي',
            'date'           => $data['date'],
            'notes'          => $data['notes'] ?? null,
        ]);

        $this->syncLegalCaseFees($receipt->legal_case_id);

        return response()->json([
            'status'  => true,
            'message' => __('messages.financial_receipt_created_successfully'),
            'data'    => new FinancialReceiptResource($receipt->load(['client', 'legalCase', 'user'])),
        ], 201);
    }

    /**
     * Update the specified financial receipt.
     */
    public function update(array $data, FinancialReceipt $financialReceipt)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($financialReceipt->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $oldCaseId = $financialReceipt->legal_case_id;

        $updateData = array_filter($data, fn($value) => !is_null($value));
        $financialReceipt->update($updateData);

        $this->syncLegalCaseFees($oldCaseId);
        if ($financialReceipt->legal_case_id !== $oldCaseId) {
            $this->syncLegalCaseFees($financialReceipt->legal_case_id);
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.financial_receipt_updated_successfully'),
            'data'    => new FinancialReceiptResource($financialReceipt->fresh()->load(['client', 'legalCase', 'user'])),
        ], 200);
    }

    /**
     * Remove the specified financial receipt.
     */
    public function destroy(FinancialReceipt $financialReceipt)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($financialReceipt->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $caseId = $financialReceipt->legal_case_id;

        $financialReceipt->delete();

        $this->syncLegalCaseFees($caseId);

        return response()->json([
            'status'  => true,
            'message' => __('messages.financial_receipt_deleted_successfully'),
        ], 200);
    }
}
