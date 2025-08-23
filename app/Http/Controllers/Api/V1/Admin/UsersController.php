<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function __construct()
    {
        // Sanctum auth + Admin gate; ImpersonationGuard is not on API by default,
        // so we enforce read-only in write actions below.
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     * GET /api/v1/admin/users
     * Query params:
     *  - search: string (matches name, email, username)
     *  - role: admin|user (optional)
     *  - banned: 1|0 (optional)
     *  - sort: id|name|email|username|created_at (default: created_at)
     *  - dir: asc|desc (default: desc)
     *  - per_page: 5..100 (default: 20)
     */
    public function index(Request $request)
    {
        $request->validate([
            'search'   => ['nullable', 'string', 'max:255'],
            'role'     => ['nullable', Rule::in(['admin', 'user'])],
            'banned'   => ['nullable', Rule::in(['0', '1', 0, 1])],
            'sort'     => ['nullable', Rule::in(['id', 'name', 'email', 'username', 'created_at'])],
            'dir'      => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $q = User::query();

        if ($s = $request->get('search')) {
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('username', 'like', "%{$s}%");
            });
        }

        if ($role = $request->get('role')) {
            if ($role === 'admin') {
                $q->admins();
            } else {
                $q->where(function ($qq) {
                    $qq->where('is_admin', false)
                       ->orWhereNull('is_admin');
                })->where(function ($qq) {
                    $qq->whereNull('role')->orWhere('role', '!=', 'admin');
                });
            }
        }

        if (!is_null($request->get('banned'))) {
            (int)$request->get('banned') === 1 ? $q->banned() : $q->notBanned();
        }

        $sort = $request->get('sort', 'created_at');
        $dir  = $request->get('dir', 'desc');
        $per  = (int)($request->get('per_page', 20));
        if ($per < 5)  $per = 5;
        if ($per > 100) $per = 100;

        $q->orderBy($sort, $dir);

        return UserResource::collection($q->paginate($per)->appends($request->query()));
    }

    /**
     * POST /api/v1/admin/users
     */
    public function store(Request $request)
    {
        $this->ensureWriteAllowed();

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8'],
            'is_admin'  => ['sometimes', 'boolean'],
            'banned_at' => ['sometimes', 'date', 'nullable'],
        ]);

        $u = new User();
        $u->name     = $data['name'];
        $u->username = $data['username'] ?? null;
        $u->email    = $data['email'];
        // Model has 'hashed' cast, so plain assignment is fine:
        $u->password = $data['password'];

        if (array_key_exists('is_admin', $data)) {
            $u->is_admin = (bool)$data['is_admin'];
        }
        if (array_key_exists('banned_at', $data)) {
            $u->banned_at = $data['banned_at'];
        }

        $u->save();

        return (new UserResource($u))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/v1/admin/users/{user}
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * PATCH /api/v1/admin/users/{user}
     */
    public function update(Request $request, User $user)
    {
        $this->ensureWriteAllowed();

        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'username'  => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email'     => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'  => ['sometimes', 'string', 'min:8'],
            'is_admin'  => ['sometimes', 'boolean'],
            'banned_at' => ['sometimes', 'date', 'nullable'],
        ]);

        // Prevent demoting the last admin
        if (array_key_exists('is_admin', $data) && $user->isAdmin() && !$data['is_admin']) {
            $otherAdmins = User::admins()->where('id', '!=', $user->id)->count();
            if ($otherAdmins === 0) {
                return response()->json([
                    'message' => 'Cannot demote the last remaining admin.',
                ], 422);
            }
        }

        if (isset($data['password'])) {
            // hashed cast will take care of hashing
            $user->password = $data['password'];
            unset($data['password']);
        }

        $user->fill($data)->save();

        return new UserResource($user);
    }

    /**
     * DELETE /api/v1/admin/users/{user}
     */
    public function destroy(User $user)
    {
        $this->ensureWriteAllowed();

        if ((int)$user->id === (int)Auth::id()) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        // Prevent deleting the last admin
        if ($user->isAdmin()) {
            $otherAdmins = User::admins()->where('id', '!=', $user->id)->count();
            if ($otherAdmins === 0) {
                return response()->json([
                    'message' => 'Cannot delete the last remaining admin.',
                ], 422);
            }
        }

        $user->delete();

        return response()->json([], 204);
    }

    /**
     * Guard: API write actions are read-only while impersonating (Sanctum uses session for SPA).
     */
    private function ensureWriteAllowed(): void
    {
        if (request()->session()->has('impersonated_by')) {
            abort(403, 'Read-only while impersonating.');
        }
    }
}
