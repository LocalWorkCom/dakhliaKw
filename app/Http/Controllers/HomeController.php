<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\outgoings;
use App\Models\Iotelegram;
use App\Models\departements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        $empCount=User::where('active',1)->count();
        $depCount=departements::count();
        $outCount=outgoings::count();
        $ioCount=Iotelegram::count();


            // if (!Auth::check()) {
            //     return redirect()->route('login');
            // }

        // Check if the previous URL matches
        // if (url()->previous() === route('reset_password')) {
        //     return redirect()->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
        // }
        
        return view('home.index',compact('empCount','depCount','outCount','ioCount'));

    }
}
