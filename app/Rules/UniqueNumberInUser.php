<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueNumberInUser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $Number;
    // protected $name;

    public function __construct($Number)
    {
        $this->Number = $Number;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        // dd($this->Number->id);email
        if($attribute == "email")
        {
            $exists = DB::table('users')->where('id', '!=', $this->Number->id)
            ->whereRaw("email = ?", [$value])
            ->exists();
            if ($exists) {
                $fail('هذا الايميل مستخدم من قبل');
            }
        }
        if($attribute == "military_number")
        {
            $exists = DB::table('users')->where('id', '!=', $this->Number->id)
            ->whereRaw("military_number = ?", [$value])
            ->exists();
            if ($exists) {
                $fail(' رقم العسكرى بالفعل موجوده');
            }
        }
        if($attribute == "file_number")
        {
            $exists = DB::table('users')->where('id', '!=', $this->Number->id)
            ->whereRaw("file_number = ?", [$value])
            ->exists();
            if ($exists) {
                $fail(' رقم الملف بالفعل موجوده');
            }
        }
        if($attribute == "Civil_number")
        {
            $exists = DB::table('users')->where('id', '!=', $this->Number->id)
            ->whereRaw("Civil_number = ?", [$value])
            ->exists();
            if ($exists) {
                $fail('رقم المدنى  بالفعل موجوده');
            }
        }
        
            // dd($exists);
        
    }
}
