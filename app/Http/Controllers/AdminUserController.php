<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display paginated user list.
     */
    public function index(Request $request): View
    {
        $query = User::withCount('researches')->latest();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user profile detail.
     */
    public function show(User $user): View
    {
        $user->loadCount(['researches', 'sentAccessRequests']);
        $user->load(['researches' => fn ($q) => $q->latest()->take(5)]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Update user role (researcher ↔ admin).
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'in:' . implode(',', array_column(Role::cases(), 'value'))],
        ]);

        // Prevent admin from downgrading their own role
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role updated to {$user->fresh()->role->label()}.");
    }
}
