<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Page;
use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    protected  $redirectPath = 'administrator/pages/list';

    protected function validatePage($data){
        $rules = [];
        $locales = Config::get('translatable.localeList');

        foreach ($locales as $key => $value) {
            $rules[$key] = ['required','array'];
            $rules[$key.'.title'] = ['required','max:100'];
//            $rules[$key.'.slug'] = ['required','max:100','unique:page_translations,slug'];
        }

        $validator =  Validator::make($data, $rules);

        if ($validator->fails())
        {
            $error = $validator->errors()->first();
            return $validator->errors();
        }
        return false;
    }

    protected function validatePageUpdate($data , $page){
        $rules = [];
        $locales = Config::get('translatable.localeList');


        foreach ($locales as $key => $value) {
            $rules[$key] = ['required','array'];
            $rules[$key.'.title'] = ['required','max:100'];
//            $rules[$key.'.slug'] = ['required','max:100','unique:page_translations,slug,'.$page->id.',page_id'   ];
        }

        $validator =  Validator::make($data, $rules);

        if ($validator->fails())
        {
            $error = $validator->errors()->first();
            return $validator->errors();
        }
        return false;
    }

    // -------------------------------------------------------------------------------
    public function index()
    {
        $pages = Page::orderBy('created_at')->paginate(20);
        return view('admin.pages.list' , compact('pages'))->with('title' , 'Pages List');
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $tags = Tag::pluck('tag_name' , 'id');
        $locales = Config::get('translatable.localeList');
        return view('admin.pages.add' , compact('locales' , 'tags'))->with('title' , 'Page Create');
    }
    // -------------------------------------------------------------------------------
    public function create(Request $request)
    {
        $slug = slug_utf8($request->input('slug'));
        $request->merge(['slug' => $slug]);

        $validationResult = $this->validatePage($request->all());

        if ($validationResult) {
            return redirect()->back()->withInput($request->input())->withErrors($validationResult);
        }

        $adminUser = $request->user('web_admin');

        $request->merge(['is_publish' => 'y']);
        $request->merge(['admin_user_id' => $adminUser->id]);
        $data = $request->except(['_token']);

        $page = Page::create($data);

        // Sync Keywords
        $keywords = $request->input('keywords');

        $finalKeywords = [];
        if ($keywords){
            foreach ($keywords as $key => $value) {
                $tag = Tag::whereId($value)->first();
                if ($tag) {
                    array_push($finalKeywords , $tag->id);
                } else {
                    $newTag = Tag::create(['tag_name' => $value]);
                    if ($newTag) {
                        array_push($finalKeywords , $newTag->id);
                    }
                }
            }
        }

        $page->tags()->sync($finalKeywords);
        // Sync Keywords

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    public function edit(Page $page)
    {
        $locales = Config::get('translatable.localeList');
        $selectedValue = null;

        $tags = Tag::pluck('tag_name' , 'id');

        if ($page->tags) {
            $selectedTags = $page->tags->pluck('id');
        } else {
            $selectedTags = '';
        }
        return view('admin.pages.edit' , compact('page' , 'locales' , 'tags' , 'selectedTags'))->with('title' , 'Edit: '.$page->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , Page $page)
    {
        $validationResult = $this->validatePageUpdate($request->all() , $page);

        if ($validationResult) {
            return redirect()->back()->withInput($request->input())->withErrors($validationResult);
        }


        $data = $request->except(['_token']);
        $page->update($data);

        // Sync Keywords
        $keywords = $request->input('keywords');

        $finalKeywords = [];
        if ($keywords){
            foreach ($keywords as $key => $value) {
                $tag = Tag::whereId($value)->first();
                if ($tag) {
                    array_push($finalKeywords , $tag->id);
                } else {
                    $newTag = Tag::create(['tag_name' => $value]);
                    if ($newTag) {
                        array_push($finalKeywords , $newTag->id);
                    }
                }
            }
        }

        $page->tags()->sync($finalKeywords);
        // Sync Keywords

        $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function delete(Page $page , Request $request){
        $page->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function PageImageDelete(Page $page){
        $this->imageDelete($page->img , $this->destinationPathOfNews);
        $page->update(array('img' => null));
        return redirect()->back();
    }
    // -------------------------------------------------------------------------------

}
