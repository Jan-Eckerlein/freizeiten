<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationCreateRequest;
use App\Http\Requests\OrganizationUpdateRequest;
use App\Models\Organization;
use App\Models\User;

class OrganizationAdminController extends Controller
{

    public function index()
    {
        return Organization::all();
    }

    public function store(OrganizationCreateRequest $request)
    {
        $organization = Organization::create($request->only('name'));
        $organization->save();

        if ($request->user_ids) $organization->users()->sync($request->user_ids);
        if ($request->admin_ids) $organization->syncAdmins($request->admin_ids);

        $previousOwner = $organization->setOwner(User::find($request->owner_id));

        return response()->json([
            'message'         => 'Successfully created Organization',
            'organization'    => $organization,
            'ownerId'         => $organization->getOwner()->id,
            'previousOwnerId' => $previousOwner->id ?? null,
            'adminIds'        => $organization->getAdmins()->pluck('id'),
            'userIds'         => $organization->users()->get()->pluck('id'),
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

        $organization->update($request->only('name'));

        if ($request->user_ids)  $organization->syncUsers($request->user_ids);
        if ($request->admin_ids) $organization->syncAdmins($request->admin_ids);
        if ($request->owner_id)  $previousOwner = $organization->setOwner(User::find($request->owner_id));

        return response()->json([
            'message'         => 'Successfully updated Organization',
            'organization'    => $organization,
            'ownerId'         => $organization->getOwner()->id,
            'previousOwnerId' => $previousOwner->id ?? null,
            'adminIds'        => $organization->getAdmins()->pluck('id'),
            'userIds'         => $organization->users()->get()->pluck('id'),
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
