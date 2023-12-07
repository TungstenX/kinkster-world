<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
  use HasFactory;

  public function friends()
  {
    return $this->hasMany('App\Models\Friend');
  }

  public function user()
  {
      return $this->belongsTo('App\Models\User');
  }

  public function friend()
  {
      return $this->belongsTo('App\Models\User');
  }
}
