<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationUpdateRequest;
use App\Models\User;

class OrganizationController extends Controller
{
    /**
     * Return all organizations the user owns or is part of
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = User::find(auth()->user()->id);
        $organizations = $user->organizations()->get();
        return $organizations
            ? response()->json([
                'message' => 'Successfully retrieved Organizations',
                'organizations' => $organizations,
            ])
            : response()->json([
                'message' => 'No Organizations found for this user',
            ], 404);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find(auth()->user()->id);
        $organization = $user->organizations()->whereOrganizationId($id)->first();

        return $organization
            ? response()->json([
                'message' => 'Successfully retrieved Organization',
                'organization' => $organization,
            ])
            : response()->json([
                'message' => 'Organization not found or you do not have permission to view it',
            ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganizationUpdateRequest $request, string $id)
    {
        $user = User::find(auth()->user()->id);
        $organization = $user->getOwnedOrganizations()->where('id', $id)->first();

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found or you do not have permission to update it',
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
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find(auth()->user()->id);
        $organization = $user->getOwnedOrganizations()->where('id', $id)->first();

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found or you do not have permission to delete it',
            ], 404);
        }

        $organization->delete();
        return response()->json([
            'message' => 'Successfully deleted Organization',
        ]);
    }
}
