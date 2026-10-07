<?php

namespace App\Services;

use App\Http\Resources\DocumentResource;
use App\Models\Client;
use App\Models\Document;
use App\Models\LegalCase;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    use ApiResponse;

    /**
     * Format byte size to human readable string (e.g. 2.4 MB).
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * List documents for authenticated client.
     */
    public function clientIndex(): JsonResponse
    {
        $client = auth()->user();

        if (! $client || ! ($client instanceof Client)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $caseIds = LegalCase::where('client_id', $client->id)->pluck('id');
        $perPage = request()->get('per_page', 10);

        $documents = Document::where('client_id', $client->id)
            ->orWhereIn('legal_case_id', $caseIds)
            ->with(['case', 'client'])
            ->latest()
            ->paginate($perPage);

        return $this->paginated(DocumentResource::class, $documents, __('messages.success'));
    }

    /**
     * Allow authenticated client to upload a document or receipt.
     */
    public function clientUpload(array $data): JsonResponse
    {
        $client = auth()->user();

        if (! $client || ! ($client instanceof Client)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $file = $data['file'];
        $filePath = $file->store('documents', 'public');
        $fileExtension = strtoupper($file->getClientOriginalExtension() ?: 'PDF');
        $fileSize = $this->formatFileSize($file->getSize());
        $title = $data['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $document = Document::create([
            'office_id'     => $client->office_id,
            'client_id'     => $client->id,
            'legal_case_id' => $data['legal_case_id'] ?? null,
            'title'         => $title,
            'file_path'     => $filePath,
            'file_type'     => $fileExtension,
            'file_size'     => $fileSize,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new DocumentResource($document),
        ], 201);
    }

    /**
     * List all documents for authenticated lawyer's office.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $perPage = request()->get('per_page', 10);

        $documents = Document::where('office_id', $user->office_id)
            ->with(['case', 'client'])
            ->latest()
            ->paginate($perPage);

        return $this->paginated(DocumentResource::class, $documents, __('messages.success'));
    }

    /**
     * Store new document created by office user.
     */
    public function store(array $data): JsonResponse
    {
        $user = auth()->user();
        $file = $data['file'];
        $filePath = $file->store('documents', 'public');
        $fileExtension = strtoupper($file->getClientOriginalExtension() ?: 'PDF');
        $fileSize = $this->formatFileSize($file->getSize());
        $title = $data['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $clientId = $data['client_id'] ?? null;
        if (! $clientId && ! empty($data['legal_case_id'])) {
            $legalCase = LegalCase::find($data['legal_case_id']);
            $clientId  = $legalCase?->client_id;
        }

        $document = Document::create([
            'office_id'     => $user->office_id,
            'client_id'     => $clientId,
            'legal_case_id' => $data['legal_case_id'] ?? null,
            'title'         => $title,
            'file_path'     => $filePath,
            'file_type'     => $fileExtension,
            'file_size'     => $fileSize,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new DocumentResource($document),
        ], 201);
    }

    /**
     * Show single document.
     */
    public function show(Document $document): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new DocumentResource($document),
        ], 200);
    }

    /**
     * Delete document.
     */
    public function destroy(Document $document): JsonResponse
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
        ], 200);
    }
}
