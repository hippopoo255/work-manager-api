<?php

namespace App\Queries;

use App\Contracts\Queries\DocumentFileQueryInterface;
use App\Models\DocumentFile;
use App\Queries\Abstracts\EloquentQuery;

class DocumentFileQuery extends EloquentQuery implements DocumentFileQueryInterface
{
  public function __construct(DocumentFile $model)
  {
    $this->setBuilder($model);
    $this->setColumns(['name']);
    $this->setRelationTargets([
      'createdBy' => [
        'given_name',
        'family_name'
      ],
      'folder' => [
        'name'
      ],
    ]);
  }
}
