<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserApprovalNotification;
use App\Notifications\UserRejectionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
         $filters = $request->only(['search', 'status']);
        
         $users = User::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('approval_status', $status);
            })
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    /**
     * Display a listing of pending users.
     */
    public function pending(Request $request)
    {
         $filters = $request->only(['search']);
        
         $users = User::query()
            ->where('approval_status', 'pending')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => array_merge($filters, ['status' => 'pending']),
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles');
        
        return Inertia::render('Admin/Users/Show', [
            'user' => array_merge($user->toArray(), [
                'permissions' => $user->getPermissionsViaRoles()->pluck('name'),
                'roles' => $user->getRoleNames(),
            ]),
        ]);
    }

    /**
     * Approve the specified user.
     */
    public function approve(User $user)
    {
        if ($user->approval_status !== 'pending') {
            return back()->with('error', 'User is not in pending status.');
        }

        DB::beginTransaction();
        try {
            $user->approval_status = 'approved';
            $user->save();
            
            // Send approval notification
            $user->notify(new UserApprovalNotification());
            
            DB::commit();
            
            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'User has been approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve user: ' . $e->getMessage());
        }
    }

    /**
     * Reject the specified user.
     */
    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($user->approval_status !== 'pending') {
            return back()->with('error', 'User is not in pending status.');
        }

        DB::beginTransaction();
        try {
            $user->approval_status = 'rejected';
            $user->rejection_reason = $validated['rejection_reason'];
            $user->save();
            
            // Send rejection notification
            $user->notify(new UserRejectionNotification($validated['rejection_reason']));
            
            DB::commit();
            
            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'User has been rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject user: ' . $e->getMessage());
        }
    }
}
