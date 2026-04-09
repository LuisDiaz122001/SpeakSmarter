<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const ITEMS_PER_PAGE = 25;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $roles = Role::query()
            ->with('permissions:id,name')
            ->orderBy('name')
            ->paginate(self::ITEMS_PER_PAGE);

        $userCountsByRole = DB::table(config('permission.table_names.model_has_roles'))
            ->select('role_id', DB::raw('count(*) as aggregate'))
            ->where('model_type', User::class)
            ->whereIn('role_id', $roles->getCollection()->pluck('id'))
            ->groupBy('role_id')
            ->pluck('aggregate', 'role_id');

        return inertia('Roles/Index', [
            'roles' => $roles->through(fn (Role $role) => $this->rolePayload(
                $role,
                (int) ($userCountsByRole[$role->id] ?? 0),
            )),
            'overview' => [
                'total' => Role::count(),
                'permissions' => Permission::count(),
                'assigned_users' => User::query()->whereHas('roles')->count(),
                'custom_roles' => Role::query()->whereNotIn('name', ['admin', 'editor', 'client'])->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return inertia('Roles/Create', [
            'permissions' => $this->availablePermissions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): Response
    {
        $role->load('permissions:id,name');

        return inertia('Roles/Edit', [
            'role' => $this->rolePayload($role),
            'permissions' => $this->availablePermissions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('roles.index');
    }

    /**
     * @return array<string, array<int, array{name: string, label: string}>>
     */
    private function availablePermissions(): array
    {
        return Permission::query()
            ->orderBy('name')
            ->get(['name'])
            ->groupBy(fn (Permission $permission) => Str::headline(Str::after($permission->name, ' ')))
            ->map(fn ($group) => $group
                ->map(fn (Permission $permission) => [
                    'name' => $permission->name,
                    'label' => Str::headline(Str::before($permission->name, ' ')),
                ])
                ->values()
                ->all())
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function rolePayload(Role $role, int $usersCount = 0): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'users_count' => $usersCount,
            'permissions_count' => $role->permissions->count(),
            'surfaces' => $role->permissions
                ->map(fn (Permission $permission) => Str::headline(Str::after($permission->name, ' ')))
                ->unique()
                ->values()
                ->all(),
            'permissions' => $role->permissions->pluck('name')->values()->all(),
        ];
    }
}
