<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreFinancialReceiptRequest;
use App\Http\Requests\API\UpdateFinancialReceiptRequest;
use App\Models\FinancialReceipt;
use App\Services\FinancialReceiptService;

class FinancialReceiptController extends Controller
{
    protected FinancialReceiptService $financialReceiptService;

    public function __construct(FinancialReceiptService $financialReceiptService)
    {
        $this->financialReceiptService = $financialReceiptService;
    }

    /**
     * Display a listing of financial receipts.
     */
    public function index()
    {
        return $this->financialReceiptService->index();
    }

    /**
     * Store a newly created financial receipt.
     */
    public function store(StoreFinancialReceiptRequest $request)
    {
        return $this->financialReceiptService->store($request->validated());
    }

    /**
     * Display the specified financial receipt.
     */
    public function show(FinancialReceipt $financialReceipt)
    {
        return $this->financialReceiptService->show($financialReceipt);
    }

    /**
     * Update the specified financial receipt.
     */
    public function update(UpdateFinancialReceiptRequest $request, FinancialReceipt $financialReceipt)
    {
        return $this->financialReceiptService->update($request->validated(), $financialReceipt);
    }

    /**
     * Remove the specified financial receipt.
     */
    public function destroy(FinancialReceipt $financialReceipt)
    {
        return $this->financialReceiptService->destroy($financialReceipt);
    }
}
