<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\DataTables\Admin\UserDataTable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $dataTables)
    {
        // return index data with datatables services
        return $dataTables->render('layouts.pageTable', [
            'title' => 'User Lists'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view template create
        return view('admin.user.form', [
            'title'     => 'Add New User',
            'action'    => route('admin.user.store'),
            'isCreated' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // validate input
        $request->validate([
            'name'      => 'required|min:3|max:255',
            'username'  => 'required|max:255|unique:users,username',
            'email'     => 'required|max:255|email|unique:users,email',
            'password'  => 'required|min:6',
            'type'      => 'required|in:' . implode(',', config('options.user_type')),
            'status'    => 'required|in:' . implode(',', config('options.user_status')),
            'is_active' => 'required|boolean',
        ]);

        // get form data
        $dataInput = $request->only([
            'name',
            'username',
            'email',
            'password',
            'type',
            'status',
            'is_active'
        ]);

        // Hash Password
        $dataInput['password'] = Hash::make($dataInput['password']);

        // save to database
        $user = User::create($dataInput);

        // set redirect url
        $redirect_route = route('admin.user.index');

        // check if user type is assesor or not
        if ($dataInput['type'] == 'assesor') {
            $redirect_route = route('admin.assesor.create', ['user_id' => $user->id]);
        }

        // redirect to index table
        return redirect($redirect_route)
            ->with('success', trans('action.success', [
                    'name' => $dataInput['name']
            ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Find User by ID
        $query = User::findOrFail($id);

        // return data to view
        return view('admin.user.form', [
            'title'     => 'Show Detail: ' . $query->name,
            'action'    => '#',
            'isShow'    => route('admin.user.edit', $id),
            'query'     => $query,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Find User by ID
        $query = User::findOrFail($id);

        // return data to view
        return view('admin.user.form', [
            'title' => 'Edit Data: ' . $query->name,
            'action' => route('admin.user.update', $id),
            'isEdit' => true,
            'query' => $query,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // validate input
        $request->validate([
            'name'          => 'required|min:3|max:255',
            'username'      => 'required|max:255|unique:users,username,' . $id,
            'email'         => 'required|max:255|email|unique:users,email,' . $id,
            'newpassword'   => 'nullable|min:6',
            'type'          => 'required|in:' . implode(',', config('options.user_type')),
            'status'        => 'required|in:' . implode(',', config('options.user_status')),
            'is_active'     => 'required|boolean',
        ]);

        // get form data
        $dataInput = $request->only([
            'name',
            'username',
            'email',
            'newpassword',
            'type',
            'status',
            'is_active'
        ]);

        // find by id and update
        $query = User::findOrFail($id);
        $query->username    = $dataInput['username'];
        $query->name        = $dataInput['name'];
        $query->email       = $dataInput['email'];
        $query->type        = $dataInput['type'];
        $query->status      = $dataInput['status'];
        $query->is_active   = $dataInput['is_active'];

        // update password if new password field not empty
        if (isset($dataInput['newpassword']) and !empty($dataInput['newpassword'])) {
            $query->password = Hash::make($dataInput['newpassword']);
        }

        // update data
        $query->update();

        // redirect
        return redirect()
            ->route('admin.user.index')
            ->with('success', trans('action.success_update', [
                'name' => $dataInput['name']
            ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $query = User::findOrFail($id);
        $query->delete();

        // return response json if success
        return response()->json([
                'code' => 200,
                'success' => true,
        ], 200);
    }
}
