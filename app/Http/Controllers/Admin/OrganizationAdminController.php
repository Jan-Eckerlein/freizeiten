<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationCreateRequest;
use App\Http\Requests\OrganizationUpdateRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationAdminController extends Controller
{

    public function index()
    {
        return Organization::all();
    }

    public function store(OrganizationCreateRequest $request)
    {
        try {
            $organization = Organization::create($request->only('name'));
            $organization->save();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to create Organization',
                'error' => $th->getMessage(),
            ], 500);
        }

        try {
            $organization->setOwner(User::find($request->owner_id));
            $owner = $organization->getOwner();

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to associate owner to Organization',
                'error' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message'      => 'Successfully created Organization',
            'organization' => $organization,
            'owner'        => $owner,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Organization::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganizationUpdateRequest $request, string $id)
    {
        $organization = Organization::find($id);

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found',
            ], 404);
        }

        try {
            $organization->update($request->name);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update Organization',
                'error'   => $th->getMessage(),
            ], 500);
        }

        try {
            $organization->owner()->associate($request->owner_id);
            $owner = $organization->owner()->get();

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to associate owner to Organization',
                'error'   => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message'      => 'Successfully updated Organization',
            'organization' => $organization,
            'owner'        => $owner,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!Organization::find($id)) {
            return response()->json([
                'message' => 'Organization not found',
            ], 404);
        }

        if (Organization::destroy($id)) {
            return response()->json([
                'message' => 'Successfully deleted Organization',
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to delete Organization',
            ], 500);
        }
    }
}
