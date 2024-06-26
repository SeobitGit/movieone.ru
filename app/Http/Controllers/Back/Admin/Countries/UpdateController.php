<?php

namespace App\Http\Controllers\Back\Admin\Countries;

use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Requests\Back\Admin\Countries\UpdateRequest;
use App\Models\Country;

class UpdateController extends BaseController
{
    public function __invoke(UpdateRequest $request, Country $country)
    {
       $data = $request->validated();
       $country->update($data);

       $this->cacheService->resetCache();

       return to_route('countries.index');
    }
}
