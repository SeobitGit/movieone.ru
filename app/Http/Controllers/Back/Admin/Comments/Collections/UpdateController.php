<?php

namespace App\Http\Controllers\Back\Admin\Comments\Collections;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Back\Admin\Comments\Collections\UpdateRequest;
use App\Models\CollectionComment;


class UpdateController extends BaseController
{
    public function __invoke(UpdateRequest $request, CollectionComment $comment)
    {
       $data = $request->validated();
       $comment->update($data);

        $comments = CollectionComment::where('approved', 1)
            ->where('collection_id', $data['collection_id'])
            ->select('rating')
            ->get()
            ->toArray();


        $rating = 0;

        foreach ($comments as $item) {
            $rating += $item['rating'];
        }

        if(count($comments) > 0)
        {
            $rating = round(($rating / count($comments)), 1);
        }else
            {
                $rating = 0;
            }


        $comment->collection()->update([
            'rating' => $rating,
        ]);

        $this->service->resetCache();

       return to_route('collection.comments.index');
    }
}
