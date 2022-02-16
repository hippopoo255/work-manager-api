<?php

namespace App\Http\Controllers;

use App\Models\DocumentFile;
use App\Models\DocumentFolder;
use Illuminate\Http\Request;
use App\Http\Requests\DocumentFile\StoreRequest;
use App\Http\Requests\DocumentFile\UpdateRequest;
use Illuminate\Support\Facades\DB;
use App\Services\DocumentFileService;

class DocumentFileController extends Controller
{
  /**
   * @var DocumentFileService
   */
  protected $service;

  public function __construct(DocumentFileService $service)
  {
    $this->authorizeResource(DocumentFile::class, 'document_file_id');
    $this->service = $service;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param StoreRequest $request
   * @param DocumentFolder $id
   * @return \Illuminate\Http\Response
   */
  public function store(StoreRequest $request, DocumentFolder $id)
  {
    DB::beginTransaction();
    try {
      $documentFile = $this->service->store($request->all(), $id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($documentFile, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param DocumentFolder $id
   * @param DocumentFile $document_file_id
   * @return \Illuminate\Http\Response
   */
  public function show(DocumentFolder $id, DocumentFile $document_file_id)
  {
    return response($this->service->find($document_file_id), 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param DocumentFolder $id
   * @param DocumentFile $document_file_id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, DocumentFolder $id, DocumentFile $document_file_id)
  {
    DB::beginTransaction();
    try {
      $documentFile = $this->service->update($request->all(), $id, $document_file_id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($documentFile, 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param DocumentFolder $id
   * @param DocumentFile $document_file_id
   * @return \Illuminate\Http\Response
   */
  public function destroy(DocumentFolder $id, DocumentFile $document_file_id)
  {
    return response($this->service->delete($document_file_id), 204);
  }
}
