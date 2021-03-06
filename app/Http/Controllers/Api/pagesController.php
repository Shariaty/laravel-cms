<?php

namespace App\Http\Controllers\Api;

use App\Admin\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;


class pagesController extends Controller
{
    public function getAllPages()
    {
        $pages = Page::all();
        return response()
            ->json($pages);
    }

    public function getSpecificPage($slug)
    {
        $page = Page::whereTranslation('slug' , $slug)->first();
        if (!$page){
            return response()->json(null , 403);
        }
        return response()->json(['title' => $page->{'title:fa'} , 'slug' => $page->{'slug:fa'} , 'meta' => $page->{'meta:fa'} ,  'p_body' => $page->{'desc:fa'}]);
    }

    public function getSpecificPageInAllLanguages($slug)
    {
        $page = Page::whereTranslation('slug' , $slug)->first();

        $locales = Config::get('translatable.localeList');
        $finalPageData = [];
        foreach ($locales as $key => $value) {
            $finalPageData[$key] = ['title' => $page->{"title:$key"} , 'slug' => $page->{"slug:$key"} , 'meta' => $page->{"meta:$key"} ,  'p_body' => $page->{"desc:$key"}];
        }

        if (count($finalPageData) < 1){
            return response()->json(null , 403);
        }
        return response()->json($finalPageData);
    }

    public function sendEmail(Request $request)
    {
        $from = $request->input('email');
        $body = $request->input('message');
        $email = env('MAIL_USERNAME');
        $siteName = env('APP_NAME');

        $data = array( 'title' => 'Email from '.$siteName,
                       'sender' => $from,
                       'content' => $body);

        Mail::send('emails.simple', compact('data') , function($message) use ($from, $email , $siteName){
            $message->to($email)->subject('Email from '.$siteName);
            $message->from($from , 'Email from '.$siteName);
        });

        // check for failures
        if (Mail::failures()) {
            return response()
                ->json(['status' => 2 , 'message' => 'Sending your message is not possible at the moment, please try again later.']);
        }

        return response()
            ->json(['status' => 1 , 'message' => 'Email has been sent successfully.']);
    }
}
