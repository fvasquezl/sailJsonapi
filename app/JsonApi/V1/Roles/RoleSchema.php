<?php

namespace App\JsonApi\V1\Roles;

use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsToMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;
use Spatie\Permission\Models\Role;

class RoleSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = Role::class;

    /**
     * Get the resource fields.
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('name'),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
            BelongsToMany::make('permissions'),
        ];
    }

    /**
     * Get the resource filters.
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
        ];
    }

    /**
     * Get the resource paginator.
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }
}
