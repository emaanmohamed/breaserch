<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Facades\Request;

class TagService
{
    public function getTags($request)
    {
        $params = $request->tag_name;
        $tag = Tag::filter($params)->orderBy('id', 'DESC')->paginate(50);
        return $tag;
    }

    public function getAllTags()
    {
        $tags = Tag::all();
        return $tags;
    }

    public function insertTag(Request $request)
    {
        $insert = Tag::create([
            'tag_name' => $request->tag_name
        ]);
        return $insert;
    }

}
