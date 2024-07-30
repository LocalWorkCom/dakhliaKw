<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueModelName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

     protected $model;
    protected $name;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
       
        $exists = DB::table('permissions')
            ->whereRaw("CONCAT(name) = ?", [$this->model])
            ->exists();
            // dd($exists);
        if ($exists) {

            $fail('هذه الصلاحية بالفعل موجوده');
        }
    }
}
