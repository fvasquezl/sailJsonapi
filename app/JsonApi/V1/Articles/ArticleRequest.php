<?php

namespace App\JsonApi\V1\Articles;

use App\Models\Article;
use App\Rules\Slug;
use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rules\HasOne;

class ArticleRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     */
    public function rules(): array
    {
        $slug = [
            'required',
            'alpha_dash',
            new Slug,
            Rule::unique(Article::class, 'slug')->ignore($this->model()),
        ];

        return [
            'title' => ['required', 'string'],
            'slug' => $slug,
            'content' => ['required', 'string'],
            'categories' => [
                'required',
                new HasOne($this->schema()),
            ],
            'authors' => [$this->isCreating() ? 'required' : 'sometimes'],
        ];
    }
}
