<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $users = User::all();
        return $this->sendResponse(UserResource::collection($users), 'All Users Sent');
    }

    public function show($id)
    {
        $user = User::query()->find($id);
        if (!$user) {
            return $this->sendError('User Not exist');
        }
        $this->authorize('view', $user);
        return $this->sendResponse(new UserResource($user), 'User Sent');
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::query()->find($id);
        if (!$user) {
            return $this->sendError('User Not exist');
        }
        $this->authorize('update', $user);
        $validated = $request->safe();
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        return $this->sendResponse(new UserResource($user), 'User Updated Successfully');
    }

    public function destroy($id)
    {
        $user = User::query()->find($id);
        if (!$user) {
            return $this->sendError('User Not exist');
        }
        $this->authorize('delete', $user);
        $user->delete();
        return $this->sendResponse(new UserResource($user), 'User Deleted Successfully');
    }
}
