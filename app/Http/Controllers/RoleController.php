<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $roles = Role::all();
        return response()->json( $roles, 201);
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
        //
        $data = $request->validate([
            'TenQuyen' => 'required|string|max:255',
            'MoTa' => 'nullable|string|max:255',
        ]);
        $role = Role::create($data);
        return response()->json(['message' => 'Quyền được tạo thành công', 'data' => $role], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //
        $data = $request->validate([
            'TenQuyen' => 'required|string|max:255',
            'MoTa' => 'nullable|string|max:255',
        ]);
        $role->update($data);
        return response()->json(['message' => 'Role updated successfully', 'data' => $role], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}
