<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreDocumentRequest;
use App\Models\Document;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    protected DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Display listing of shared documents for authenticated client.
     */
    public function clientIndex()
    {
        return $this->documentService->clientIndex();
    }

    /**
     * Client upload new document or receipt.
     */
    public function clientUpload(StoreDocumentRequest $request)
    {
        return $this->documentService->clientUpload($request->validated());
    }

    /**
     * Display listing of office documents for lawyer.
     */
    public function index()
    {
        return $this->documentService->index();
    }

    /**
     * Store new document created by office user.
     */
    public function store(StoreDocumentRequest $request)
    {
        return $this->documentService->store($request->validated());
    }

    /**
     * Display single document details.
     */
    public function show(Document $document)
    {
        return $this->documentService->show($document);
    }

    /**
     * Delete document.
     */
    public function destroy(Document $document)
    {
        return $this->documentService->destroy($document);
    }
}
