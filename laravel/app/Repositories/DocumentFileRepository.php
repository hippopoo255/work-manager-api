<?php

namespace App\Repositories;

use App\Contracts\Repositories\DocumentFileRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentFile;
use App\Models\DocumentFolder;
use App\Repositories\Abstracts\EloquentRepository;
use Illuminate\Support\Facades\Auth;

class DocumentFileRepository extends EloquentRepository implements DocumentFileRepositoryInterface
{
  public function __construct(DocumentFile $model)
  {
    $this->setModel($model);
  }

  /**
   * @return Model
   */
  public function attachWithMembers(array $params, DocumentFolder $documentFolder, $id = null): Model
  {
    if (is_null($id)) {
      $params['created_by'] = Auth::user()->id;
    }
    $documentFile = parent::attach($params, $documentFolder, 'files', $id);
    if (isset($params['sharedMembers'])) {
      $documentFile->sharedMembers()->sync($params['sharedMembers']);
    }
    $documentFile->load(['createdBy', 'folder', 'sharedMembers']);
    return $documentFile;
  }

  /**
   * @param array $params
   * @param $id
   * @return Model
   */
  public function update(array $params, $id): Model
  {
    $model = parent::update($params, $id);
    if (isset($params['sharedMembers'])) {
      $model->sharedMembers()->sync($params['sharedMembers']);
    }
    return $model->load(['createdBy', 'folder', 'sharedMembers']);;
  }

  /**
   * @param $id
   * @return string
   */
  public function findPublicName($id): string
  {
    if ($model = $this->find($id)) {
      return $model->public_name;
    }
    return '';
  }
}
