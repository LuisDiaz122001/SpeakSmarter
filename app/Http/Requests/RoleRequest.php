<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return match ($this->route()?->getActionMethod()) {
            'store' => $this->user()?->can('create roles') ?? false,
            'update' => $this->user()?->can('update roles') ?? false,
            default => false,
        };
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where(fn ($query) => $query->where('guard_name', 'web'))
                    ->ignore($this->route('role')),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')
                    ->where(fn ($query) => $query->where('guard_name', 'web')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The role name is required.',
            'name.unique' => 'The role name has already been taken.',
            'permissions.array' => 'The permissions selection is invalid.',
            'permissions.*.exists' => 'One of the selected permissions is invalid.',
        ];
    }
}
