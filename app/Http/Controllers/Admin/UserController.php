<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q     = $request->string('q')->toString();
        $role  = $request->string('role')->toString();  // admin|user|''
        $state = $request->string('state')->toString(); // banned|active|''

        $users = User::query()
            ->when($q, fn($qq) => $qq->where(function($x) use ($q) {
                $x->where('name','like',"%$q%")
                    ->orWhere('username','like',"%$q%")
                    ->orWhere('email','like',"%$q%");
            }))
            ->when($role === 'admin', fn($qq) => $qq->admins())
            ->when($role === 'user', fn($qq) => $qq->where(function($x){
                $x->where('is_admin', false)->orWhereNull('is_admin');
            }))
            ->when($state === 'banned', fn($qq) => $qq->whereNotNull('banned_at'))
            ->when($state === 'active', fn($qq) => $qq->whereNull('banned_at'))
            ->orderByDesc('id')
            ->paginate(12)->withQueryString();

        return view('admin.users.index', compact('users','q','role','state'));
        // View code in step 5
    }

    public function create()
    {
        // No managedUser passed in create mode; Blade will treat as "new"
        return view('admin.users.form');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        AuditLog::write($user, 'user.create', 'Admin created a user', ['fields'=>array_keys($data)]);
        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        // Pass the model as $managedUser to avoid colliding with any globally shared $user
        return view('admin.users.form', ['managedUser' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
        else unset($data['password']);

        $user->update($data);
        AuditLog::write($user, 'user.update', 'Admin updated user', ['fields'=>array_keys($data)]);

        return back()->with('status', 'User updated.');
    }

    public function destroy(User $user)
    {
        AuditLog::write($user, 'user.delete', 'Admin deleted user', ['email'=>$user->email]);
        $user->delete();

        return redirect()->route('admin.users.index')->with('status','User deleted.');
    }

    public function ban(User $user)
    {
        $user->update(['banned_at' => now()]);
        AuditLog::write($user, 'user.ban', 'Admin banned user');
        return back()->with('status', 'User banned.');
    }

    public function unban(User $user)
    {
        $user->update(['banned_at' => null]);
        AuditLog::write($user, 'user.unban', 'Admin unbanned user');
        return back()->with('status', 'User unbanned.');
    }

    public function promote(User $user)
    {
        $user->update(['is_admin' => true]);
        AuditLog::write($user, 'user.promote', 'Admin promoted user to admin');
        return back()->with('status', 'User promoted to admin.');
    }

    public function demote(User $user)
    {
        $user->update(['is_admin' => false]);
        AuditLog::write($user, 'user.demote', 'Admin demoted admin to user');
        return back()->with('status', 'Admin demoted to user.');
    }

    public function bulk(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->string('action')->toString();

        $users = User::whereIn('id', $ids)->get();

        foreach ($users as $u) {
            match ($action) {
                'ban'     => $u->update(['banned_at' => now()]),
                'unban'   => $u->update(['banned_at' => null]),
                'promote' => $u->update(['is_admin' => true]),
                'demote'  => $u->update(['is_admin' => false]),
                'delete'  => $u->delete(),
                default   => null,
            };

            AuditLog::write($u, "user.bulk.$action", 'Bulk action on user');
        }

        return back()->with('status', 'Bulk action completed.');
    }

    public function exportCsv(): StreamedResponse
    {
        $file = 'users-'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$file\"",
        ];

        return response()->stream(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','name','username','email','is_admin','banned_at','email_verified_at','created_at']);
            User::orderBy('id')->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $u) {
                    fputcsv($out, [
                        $u->id, $u->name, $u->username, $u->email,
                        $u->isAdmin() ? 1 : 0,
                        optional($u->banned_at)->toDateTimeString(),
                        optional($u->email_verified_at)->toDateTimeString(),
                        optional($u->created_at)->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }
}
