<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::all();
        return DataTables::of($data)->make(true);
        
    }
    public function login(Request $request)
    {
        $credentials = $request->only('military_number', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return view('welcome');
        }

        return back()->withErrors([
            'military_number' => 'The provided credentials do not match our records.',
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd("dd");
        // validation
        // $validatedData = $request->validate([
        //     'military_number' => 'required|string|unique:users|max:255',
        //     'phone' => 'required|unique:users|max:255',
        //     'password' => 'required|string|min:8|confirmed',
        //     'country_code' =>'required',
        // ]);

        $newUser = new User();
        $newUser->military_number = "123";
        $newUser->phone = "01114057863";
        $newUser->country_code = "+20";
        // $newUser->password = Hash::make($validatedData['password']);
        $newUser->password = Hash::make("123");
        $newUser->save();

        return response()->json($newUser);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
