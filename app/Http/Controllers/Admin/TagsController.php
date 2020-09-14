<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class tagsController extends Controller
{

    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfTagExist($name, $columnName)
    {
        return Tag::where($columnName, $name)->count();
    }

    public function index(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $tags = $this->guzzleService->get('tags', $params);
        if (optional($tags)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',optional($tags)->pagination);
            $tags = optional($tags)->data->data;
        } else {
            $tags = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.tag.display', compact('tags', 'pagination'));
        }

        return view('admin.tag.index', compact('tags', 'pagination'));
    }

    public function edit($id)
    {
        $tag = Tag::findOrFail($id);
        return view('admin.ajax_includes.tag.edit_form', compact('tag'));

    }

    public function add()
    {
        return view('admin.ajax_includes.tag.add_form');
    }

    public function getCreate()
    {

    }

    public function store(Request $request)
    {
        if (! empty($request->tag_name)) {
            if ($this->checkIfTagExist($request->tag_name, 'tag_name') < 1) {
                Tag::create([
                    'tag_name' => $request->tag_name
                ]);
                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This tag {$request->tag_name} has been added successfully!",
                    'type'    => "success"
                ]);
            }
        } else {
            return json_encode([
                'title'   => 'Exist!',
                'message' => "This tag {$request->tag_name} already exist!",
                'type'    => "warning"
            ]);
        }
    }


    public function update(Request $request)
    {
        Tag::findOrFail($request->id)->update([
            'tag_name' => $request->tag_name
        ]);
        if ($request->ajax()) {
            return json_encode([
                'title'    => "Updating!",
                'message' => "Tag has been updated successfully!",
                'type'    => "success"
            ]);

        }
    }

    public function delete($id)
    {
        Tag::findOrFail($id)->delete();
    }

}
