<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeVacation extends Model
{
    use HasFactory;
    protected $table = "employee_vacations";

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    public function created_by()
    {
        return $this->belongsTo(User::class);
    }
    public function created_department()
    {
        return $this->belongsTo(departements::class);
    }
   
    public function updated_by()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    public function vacation_type()
    {
        return $this->belongsTo(VacationType::class, 'vacation_type_id');
    }
  
}
