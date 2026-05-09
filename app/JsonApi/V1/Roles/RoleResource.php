<?php

namespace App\JsonApi\V1\Roles;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;
use Spatie\Permission\Models\Role;

/**
 * @property Role $resource
 */
class RoleResource extends JsonApiResource
{
    /**
     * Get the resource's attributes.
     *
     * @param  Request|null  $request
     */
    public function attributes($request): iterable
    {
        return [
            'name' => $this->resource->name,
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param  Request|null  $request
     */
    public function relationships($request): iterable
    {
        return [
            $this->relation('permissions'),
        ];
    }
}
