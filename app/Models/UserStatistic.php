<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatistic extends Model
{
    use HasFactory;
    protected $table = "user_statistics";

    protected $fillable = [
        'user_id',
        'statistic_id',
        'checked',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function statistic()
    {
        return $this->belongsTo(Statistic::class, 'statistic_id'); // Assuming 'grade_id' is the foreign key
    }

}
