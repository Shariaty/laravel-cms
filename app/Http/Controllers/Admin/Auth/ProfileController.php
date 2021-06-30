<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function dashboard()  {
        return view('admin.dashboard')->with('title' , trans('admin.DASHBOARD_PAGE_TITLE'));
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function profile()
    {
        return view('admin.users.profile')->with('title' , 'User Profile');
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function profileEdit()
    {
        $user = getCurrentAdminUser();
        return view('admin.users.profileEdit' , compact('user'))->with('title' , 'Edit Profile');
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function profileUpdate(Request $request)
    {
        Validator::make($request->all(), [
            'firstname' => 'required|max:100',
            'lastname' => 'required|max:100',
            'password' => 'nullable|min:6|confirmed'
        ])->validate();

        if(!empty($request->input('password'))){
            $request->replace(array( 'password' => bcrypt($request->input('password')) ));
            $data = $request->except(['_token' , 'email']);
        } else {
            $data = $request->except(['_token' , 'email' , 'password']);
        }

        $admin = getCurrentAdminUser();
        $admin->update($data);

        $request->session()->flash('success', trans('notify.UPDATE_SUCCESS_NOTIFICATION)'));
        return redirect(route('admin.profile'));
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function ajaxUpload(Request $request)
    {
        if($request->ajax()) {
            if($request->hasFile('file'))  {

                $rules = array('file' => 'required|mimes:jpg,jpeg,png|max:1024');
                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                }else{
                    $file = $request->file('file');
                    $user = $request->user();
                    $name = $this->ImageUpload($file);
                    $this->profilePictureRemove($user);
                    getCurrentAdminUser()->update(['img' => $name]);
                    $result = array('success' => true , 'message' => $name);
                }
                return response()->json($result);
            } else {
                return response()->json('No file available');
            }
        }
        return 'forbidden';
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function ImageUpload($file)
    {
        $name = time().'.'.$file->getClientOriginalExtension();
        $img = Image::make($file->getRealPath());
        $destinationPath = PATH_ROOT.'/uploads/admins/profile-pictures';
        $img->resize(200, 200 , function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$name);

        return $name;
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function profilePictureRemove()
    {
        $user = getCurrentAdminUser();

        if ($this->removeProfilePicture($user)) {
            return redirect(route('admin.profile'))->with('success' , 'Image Has Been Removed');
        }

        return redirect(route('admin.profile'))->with('error' , 'There Was Problem in removing the picture!');
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function removeProfilePicture ($user) {
        $filePath = PATH_ROOT.'/uploads/admins/profile-pictures/';

        $fullFileName = $filePath . $user->img;

        if (File::exists($fullFileName)) {
            File::delete($fullFileName);
            $user->img = null;
            $user->update();
            return true;
        } else {
            return false;
        }
    }
    /** ------------------------------------------------------------------------------------------------------------- */

}
