<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApproveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        return [
            'legajo' => ['required', 'string', 'max:50', 'unique:users,legajo,' . $userId],
            'role'   => ['nullable', 'in:buyer,vendor,admin'],
        ];
    }

    public function messages(): array
    {
        return [
            'legajo.required' => 'El legajo es obligatorio para aprobar.',
            'legajo.unique'   => 'Ese legajo ya existe.',
        ];
    }
}
