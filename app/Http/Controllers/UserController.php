<?php

namespace App\Http\Controllers;

use App\Actions\Jetstream\DeleteUser;
use App\Http\Requests\ManagedUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private const ITEMS_PER_PAGE = 18;

    public function index(): Response
    {
        $users = User::query()
            ->with(['roles:id,name', 'permissions:id,name'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->orderBy('name')
            ->paginate(self::ITEMS_PER_PAGE);

        return inertia('Users/Index', [
            'users' => $users->through(fn (User $user) => $this->userPayload($user)),
            'overview' => [
                'total' => User::count(),
                'admins' => User::role('admin')->count(),
                'editors' => User::role('editor')->count(),
                'clients' => User::role('client')->count(),
                'with_phone' => User::query()->whereNotNull('phone')->where('phone', '!=', '')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return inertia('Users/Create', $this->formOptions());
    }

    public function store(ManagedUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => User::fullNameFromParts($validated['first_name'], $validated['last_name']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->syncRoles($validated['roles'] ?? []);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('users.index');
    }

    public function edit(User $user): Response
    {
        $user->load(['roles:id,name', 'permissions:id,name']);

        return inertia('Users/Edit', [
            'managedUser' => $this->userPayload($user),
            ...$this->formOptions(),
        ]);
    }

    public function update(ManagedUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $attributes = [
            'name' => User::fullNameFromParts($validated['first_name'], $validated['last_name']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
        ];

        if (filled($validated['password'] ?? null)) {
            $attributes['password'] = $validated['password'];
        }

        $user->update($attributes);
        $user->syncRoles($validated['roles'] ?? []);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('users.index');
    }

    public function destroy(User $user, DeleteUser $deleteUser): RedirectResponse
    {
        abort_if(request()->user()?->is($user), 403, 'No puedes eliminar tu propia cuenta desde este modulo.');

        $deleteUser->delete($user);

        return redirect()->route('users.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'roles' => Role::query()
                ->with('permissions:id,name')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (Role $role) => [
                    'name' => $role->name,
                    'description' => match ($role->name) {
                        'admin' => 'Control total del sistema y de los usuarios.',
                        'editor' => 'Gestion operativa de contenido y estructura.',
                        'client' => 'Acceso limitado para consultar el contenido.',
                        default => 'Perfil personalizado para necesidades especificas.',
                    },
                ])
                ->values()
                ->all(),
            'rolePermissionMap' => Role::query()
                ->with('permissions:id,name')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->mapWithKeys(fn (Role $role) => [
                    $role->name => $role->permissions->pluck('name')->values()->all(),
                ])
                ->all(),
            'permissions' => Permission::query()
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
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function userPayload(User $user): array
    {
        $user->loadMissing(['roles:id,name', 'permissions:id,name']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->values()->all(),
            'permissions' => $user->permissions->pluck('name')->values()->all(),
            'effective_permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
            'updated_at' => $user->updated_at->diffForHumans(),
        ];
    }
}
