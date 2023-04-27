<?php

namespace App\Models\Instrument;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
  use HasFactory;

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'language_id',
    'vendor_id',
    'name',
    'charge',
    'serial_number',
  ];

  public function language()
  {
    return $this->belongsTo(Language::class, 'language_id', 'id');
  }
}
