<?php

namespace App\Http\Requests;

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ManagedUserRequest extends FormRequest
{
    use PasswordValidationRules;

    public function authorize(): bool
    {
        return match ($this->route()?->getActionMethod()) {
            'store' => $this->user()?->can('create users') ?? false,
            'update' => $this->user()?->can('update users') ?? false,
            default => false,
        };
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'password' => $this->passwordRulesForCurrentAction(),
            'roles' => ['nullable', 'array'],
            'roles.*' => [
                'string',
                Rule::exists('roles', 'name')
                    ->where(fn ($query) => $query->where('guard_name', 'web')),
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
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'phone.max' => 'El telefono no puede superar los 30 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Debes ingresar un correo valido.',
            'email.unique' => 'Ese correo ya esta registrado.',
            'password.required' => 'La contrasena es obligatoria.',
            'password.confirmed' => 'La confirmacion de la contrasena no coincide.',
            'roles.array' => 'La seleccion de roles no es valida.',
            'roles.*.exists' => 'Uno de los roles seleccionados no es valido.',
            'permissions.array' => 'La seleccion de permisos no es valida.',
            'permissions.*.exists' => 'Uno de los permisos seleccionados no es valido.',
        ];
    }

    protected function passwordRulesForCurrentAction(): array
    {
        if ($this->route()?->getActionMethod() === 'store') {
            return $this->passwordRules();
        }

        return ['nullable', 'string', Password::default(), 'confirmed'];
    }
}
