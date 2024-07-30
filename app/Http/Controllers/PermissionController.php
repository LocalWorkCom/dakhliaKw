<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Rules\UniqueModelName;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\DataTables\PermissionRoleDataTable;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Validation\Rule as ValidationRule;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getAllModels()
    {
        // Directory where your models are located
        $modelsDirectory = app_path('Models');

        // Get all PHP files in the models directory
        $modelFiles = File::files($modelsDirectory);

        $models = [];

        // Iterate through each file to get model class names
        foreach ($modelFiles as $file) {
            // Get the file name
            $fileName = $file->getFilename();

            // Remove the .php extension
            $modelName = Str::replaceLast('.php', '', $fileName);

            // Fully qualify the model class name
            $modelClass = 'App\\Models\\' . $modelName;

            // Check if the class exists and is an instance of Eloquent Model
            if (class_exists($modelClass) && is_subclass_of($modelClass, 'Illuminate\Database\Eloquent\Model')) {
                // $translatedName = __('models.' . $modelName);

                $models[] = $modelName;
            }
        }

        // dd($models);
        return $models;
    }

    public function index()
    {
        //
        // return $dataTable->render('permission.view');
        return view('permission.view');
    }
    public function getPermision()
    {
        $data = Permission::all();

        return DataTables::of($data)->addColumn('action', function ($row) {

            return '<button class="btn btn-primary btn-sm">Edit</button>';
        })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PermissionRoleDataTable $dataTable)
    {
        $models = $this->getAllModels();

        // dd($models);
        return $dataTable->render('permission.create', compact('models'));
        // return view('permission.create', compact('models'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PermissionRoleDataTable $dataTable)
    {
        // dd($request);
        $messages = [
            'model.required' => 'القسم  مطلوب ولا يمكن تركه فارغاً.',
            'name.unique' => 'القسم  الذي أدخلته موجود بالفعل.',

            'name.required' => 'الصلاحية  مطلوب ولا يمكن تركه فارغاً.',
            // 'name.unique' => 'رقم الهاتف يجب أن يكون نصاً.',

            // Add more custom messages here
        ];
       $nameModel = $request->name . " " . $request->model;
    //    dd($nameModel);
        $validatedData = Validator::make($request->all(), [
            'model' => [
                'required',
                'string',
                'max:255',
                // ValidationRule::unique('permissions', 'name'),
                new UniqueModelName($nameModel),
            ],
            'name' => 'required|unique:permissions,name',

        ], $messages);



        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }


        $nameModel = $request->name . " " . $request->model;
        

        try {

            $existornot = Permission::where('name', $nameModel)->withTrashed()->first();
           

            if ($existornot) {
                $existornot->restore();
                // dd($existornot);
            } else {
                // dd();
                // $modelClass = 'App\\Models\\' . $request->model;
                $modelClass = $request->model;
                // Create the permission
                $permission = new Permission();
                $permission->name = $nameModel;
                $permission->guard_name = $modelClass;
                $permission->save();
                // dd($permission);
                DB::insert('INSERT INTO model_has_permissions (permission_id , model_type ,model_id ) VALUES (?, ?, ?)', [
                    $permission->id,
                    $request->name,
                    $request->model, // or specify your guard name if different

                ]);
            }
            return view('permission.view');
            // return response()->json("ok");
            // dd("sara");
            // return redirect()->back()->with('alert', 'Permission created successfully.');
            // return $dataTable->render('permission.view')->with('alert', 'Permission created successfully.');
            // return $dataTable->render('permission.view');
        } catch (\Exception $e) {
            // dd("yy");
            return response()->json($e->getMessage());
            // return redirect()->back()->with('error', 'Failed to create permission. ' . );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $permission = Permission::find($id);
        $models = $this->getAllModels();
        // dd($models);

        // Split the name into action and model parts
        $nameParts = explode(' ', $permission->name);
        $permissionAction = $nameParts[0] ?? '';
        $permissionModel = $nameParts[1] ?? '';
        // dd($permissionAction);

        return view('permission.show', compact('permission', 'models', 'permissionAction', 'permissionModel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // dd($id);
        $permission = Permission::find($id);
        $models = $this->getAllModels();
        // dd($models);

        // Split the name into action and model parts
        $nameParts = explode(' ', $permission->name);
        $permissionAction = $nameParts[0] ?? '';
        $permissionModel = $nameParts[1] ?? '';
        // dd($permissionAction);

        return view('permission.edit', compact('permission', 'models', 'permissionAction', 'permissionModel'));
    }


    public function update(Request $request, Permission $permission)
    {
        $messages = [
            'model.required' => 'القسم  مطلوب ولا يمكن تركه فارغاً.',
            // 'model.unique' => 'القسم  الذي أدخلته موجود بالفعل.',

            'name.required' => 'الصلاحية  مطلوب ولا يمكن تركه فارغاً.',
            // 'name.unique' => 'رقم الهاتف يجب أن يكون نصاً.',

            // Add more custom messages here
        ];

        $validatedData = Validator::make($request->all(), [
            'model' => [
                'required',
                'string',
                'max:255',
                // ValidationRule::unique('permissions', 'guard_name'),
            ],
            'name' => 'required|unique:permissions,name',

        ], $messages);



        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $nameModel = $request->name . " " . $request->model;

        try {
            $modelClass = $request->model;
            $permission->name = $nameModel;
            $permission->guard_name = $modelClass;
            $permission->save();

            DB::table('model_has_permissions')
                ->where('permission_id', $permission->id)
                ->update([
                    'model_type' => $modelClass,
                    'model_id' => $request->name,
                ]);

            return redirect()->back()->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update permission: ' . $e->getMessage());
        }
    }


    // private function getAllModels()
    // {
    //     // Implement this method to return the list of all models
    //     return ['User', 'Role', 'Post']; // Example
    // }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return view('permission.view');
    }
}
