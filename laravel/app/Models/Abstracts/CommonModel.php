<?php

namespace App\Models\Abstracts;

use App\Contracts\Models\RelationalDeleteInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class CommonModel extends Model
{
  use HasFactory;
  /**
   * @return $this
   */
  public function customDelete()
  {
    if ($this->isSoftDeletes()) {
      $this->fetchDeleteColumns();
      $this->save();
    } else {
      $this->delete();
    }

    if ($this instanceof RelationalDeleteInterface) {
      foreach ($this->getDeleteRelations() as $relation) {
        if ($relation instanceof Collection) {
          $relation->each(function (Model $child) {
            $child->customDelete();
          });
        } elseif ($relation instanceof RelationalDeleteInterface) {
          $relation->customDelete();
        }
      }
    }
    return $this;
  }

  protected function fetchDeleteColumns(): void
  {
    $this->deleted_at = Carbon::now()->format('Y-m-d H:i:s');
    if (array_key_exists('deleted_by', $this->getAttributes())) {
      $this->deleted_by = Auth::check() ? Auth::user()->id : null;
    }
  }

  /**
   * @return bool
   */
  protected function isSoftDeletes(): bool
  {
    return array_key_exists('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this));
  }
}
