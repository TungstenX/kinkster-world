<?php
/**
TODO List:
1. Add clip-art-ish profile pics
1.1. Enable clicking on other images to change profile pic - not to be uploaded
2. Show all images belonging to user as thumb nail
3. Highlight current profile pic
4. Enable clicking on other images to change profile pic - not to be uploaded
**/
namespace App\Http\Controllers;

use App\Http\Controllers\ImageController;

use App\Models\User;
use App\Models\Pic;
use App\Models\UserPref;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
  public function postRegister(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email|unique:users',
      'name' => 'required|min:4|max:120',
      'password' => 'required|min:8'
    ]);

    $email = $request['email'];
    $nick = $request['name'];
    $password = bcrypt($request['password']);

    $user = new User();
    $user->email = $email;
    $user->name = $nick;
    $user->password = $password;

    $user->save();

    // create prefs
    $postAudiencePref = new UserPref();
    $postAudiencePref->user_id = $user->id;
    $postAudiencePref->key = 'Post Audience';
    $postAudiencePref->value = 'public';
    $postAudiencePref->save();
    $postAudiencePref = new UserPref();
    $postAudiencePref->user_id = $user->id;
    $postAudiencePref->key = 'Posts Per Page';
    $postAudiencePref->value = '20';
    $postAudiencePref->save();
    $postAudiencePref = new UserPref();
    $postAudiencePref->user_id = $user->id;
    $postAudiencePref->key = 'Friends Per Page';
    $postAudiencePref->value = '20';
    $postAudiencePref->save();

    Auth::login($user);
    return redirect()->route('dashboard');
  }

  public function postSignIn(Request $request)
  {
    $this->validate($request, [
      'email' => 'required',
      'password' => 'required'
    ]);

    if(Auth::attempt(['email' => $request['email'], 'password' => $request['password']]))
    {
      return redirect()->route('dashboard');
    }

    return redirect()->back();
  }

    public function getLogout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

  public function getAccount()
  {
    $user = Auth::user();
    if (!Auth::user()) {
      return redirect()->route('home');
    }
    $pic = Pic::where('user_id', $user->id)->where('profile', true)->first();

    $filename = "";//should be default blank one?
    if($pic)
    {
      $filename = $pic->filename;
    }
    return view('account', ['user' => $user, 'profilePic' => $filename, 'prefs' => $user->userPrefs()->get()]);
  }

  /**
  * TODO: Check file types
  **/
  public function postSaveAccount(Request $request)
  {
  /*
  nie required nie, net vir voorbeeld
  'attachment' => [
            'required',
            File::types(['mp3', 'wav'])
                ->min(1024)
                ->max(12 * 1024),

                       photo' => [
                               'required',
                                File::image()
                            ->min(1024)
                            ->max(12 * 1024)
                            ->dimensions(Rule::dimensions()->maxWidth(1000)->maxHeight(500)),
                */
    $this->validate($request, [
      'name' => 'required|max:120',
      'f_pref_Post_Audience' => 'required|in:private,friends,circle,public',
      'f_pref_Posts_Per_Page' => 'required|integer',
      'f_pref_Friends_Per_Page' => 'required|integer'
    ]);

    $user = Auth::user();
    $old_name = $user->name;
    $user->name = $request['name'];
    $user->update();

    $postAudiencePrefs = UserPref::where('user_id', $user->id)->get();
    foreach($postAudiencePrefs as $postAudiencePref)
    {
      if($postAudiencePref->key == 'Post Audience')
      {
        $postAudiencePref->value = strtolower($request['f_pref_Post_Audience']);
      }
      elseif($postAudiencePref->key == 'Posts Per Page')
      {
        $postAudiencePref->value = (string)$request['f_pref_Posts_Per_Page'];
      }
      elseif($postAudiencePref->key == 'Friends Per Page')
      {
        $postAudiencePref->value = (string)$request['f_pref_Friends_Per_Page'];
      }
      $postAudiencePref->save();
    }

    $result = (new ImageController)->saveImage($request, null);

    return redirect('account')->with('message', $result);
  }

    /**
    * TODO Move to ImageController
    **/
    public function getUserImage($which)
    {
        $user = Auth::user();
        $pic = Pic::where('user_id', $user->id)->where('profile', true)->first();
        if($pic) {
            $fileName = $pic->filename;
            $tn = '';
            if($which == 'tn') {
                $pathParts = explode('.', $fileName);
                $ext = $pathParts[1];
                $fileName = $pathParts[0] . '_tn.' . $ext;
                $tn = 'tn/';
            }
            $file = Storage::disk('local')->get('public/pics/' . $tn . $fileName);
            return new Response($file, 200);
        } else {
            return new Response("Image not found", 404);
        }
    }
}
