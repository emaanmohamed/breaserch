<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TagService;

class TagsController extends ApiController
{
    private $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(Request $request)
    {
        $tags = $this->tagService->getTags($request);

        return $this->ApiResponseData($tags, 200, (string)$tags->links());
    }

    public function getAllTags()
    {
        $tags = $this->tagService->getAllTags();
        return $this->ApiResponseData($tags, 200);
    }

}
