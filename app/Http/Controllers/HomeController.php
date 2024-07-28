<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\departements;
use App\Models\outgoings;
use App\Models\Iotelegram;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        $empCount=User::where('active',1)->count();
        $depCount=departements::count();
        $outCount=outgoings::count();
        $ioCount=Iotelegram::count();

        // Check if the previous URL matches
        // if (url()->previous() === route('reset_password')) {
        //     return redirect()->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
        // }
        
        return view('home.index',compact('empCount','depCount','outCount','ioCount'));

    }
}
