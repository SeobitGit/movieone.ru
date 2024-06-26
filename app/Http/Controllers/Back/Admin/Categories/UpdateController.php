<?php

namespace App\Http\Controllers\Back\Admin\Categories;

use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Requests\Back\Admin\Categories\UpdateRequest;
use App\Models\Category;


class UpdateController extends BaseController
{
    public function __invoke(UpdateRequest $request, Category $category)
    {
       $data = $request->validated();
       $category->update($data);

       $this->cacheService->resetCache();

       return to_route('categories.index');
    }
}
