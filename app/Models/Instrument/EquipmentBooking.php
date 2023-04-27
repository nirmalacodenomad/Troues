<?php

namespace App\Models\Instrument;

use App\Models\Instrument\Equipment;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentBooking extends Model
{
  use HasFactory;

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  public function userInfo()
  {
    return $this->belongsTo(User::class);
  }
  
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function equipmentInfo()
  {
    return $this->belongsTo(Equipment::class, 'equipment_id', 'id');
  }
  public function vendor()
  {
    return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
  }
}
