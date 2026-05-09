<?php

namespace App\JsonApi\V1\Roles;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use Spatie\Permission\Models\Role;

class RoleRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:125',
                Rule::unique(Role::class, 'name')
                    ->where('guard_name', 'web')
                    ->ignore($this->model())],
        ];
    }
}
