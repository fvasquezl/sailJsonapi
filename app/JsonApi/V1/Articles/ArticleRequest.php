<?php

namespace App\JsonApi\V1\Articles;

use App\Models\Article;
use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class ArticleRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     */
    public function rules(): array
    {
        $slug = [
            'required',
            'string',
            Rule::unique(Article::class, 'slug')->ignore($this->model()),
        ];

        return [
            'title' => ['required', 'string'],
            'slug' => $slug,
            'content' => ['required', 'string'],
            'category' => ['required'],
            'user' => ['required'],
        ];
    }
}
