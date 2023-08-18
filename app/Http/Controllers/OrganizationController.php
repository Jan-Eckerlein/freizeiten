<?php

namespace App\Http\Controllers;

use App\Http\Requests\GhostUserCreateRequest;
use App\Http\Requests\OrganizationUpdateRequest;
use App\Http\Resources\OgranizationResource;
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
        $organizations = OgranizationResource::collection($user->organizations);
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
        $organization = OgranizationResource::make($user->organizations()->whereOrganizationId($id)->first());

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

    /**
     * Create a ghost user to add as a member to an organization
     */
    public function createGhostUser(GhostUserCreateRequest $request, string $id) {
        $validated = $request->validated();

        $user = User::find(auth()->user()->id);
        $organization = $user->getOwnedOrganizations()->where('id', $id)->first();

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found or you do not have permission to add a member to it',
            ], 404);
        }

        $ghostUser = User::create(collect($validated)->merge(['global_role' => 'ghost'])->toArray());
        $organization->users()->attach($ghostUser->id);

        return response()->json([
            'message' => 'Successfully created Ghost User and added them to Organization',
            'ghostUser' => $ghostUser,
        ]);
    }

    /**
     * Delete a ghost user that was added as a member to an organization
     */
    public function deleteGhostUser(string $id, string $ghostUserId) {
        $user = User::find(auth()->user()->id);

        $organization = $user->getOwnedOrganizations()->where('id', $id)->first();
        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found or you do not have permission to delete a member from it',
            ], 404);
        }

        $ghostUser = $organization->users()->whereUserId($ghostUserId)->first();
        if (!$ghostUser) {
            return response()->json([
                'message' => 'Ghost User not found or you do not have permission to delete them',
            ], 404);
        }

        $organization->users()->detach($ghostUser->id);
        $ghostUser->delete();

        return response()->json([
            'message' => 'Successfully deleted Ghost User and removed them from Organization',
        ]);
    }
}
