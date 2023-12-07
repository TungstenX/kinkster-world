<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PostController;
use App\Models\Pic;
use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use Image;

class ImageController extends Controller
{

  public function saveImage(Request $request, $post_id)
  {
    if(!$request->hasFile('image')) {
      return "No file received to store.";// Doesn't have file
    }
    // Anti-virus scan
    $this->validate($request, [
      'image' => 'clamav'
    ]);

    //$request->validate(['image' => 'clamav']);

    $file = $request->file('image');
    if ($file)
    {
      $user = Auth::user();
      $filenameWithExtension = $file->getClientOriginalName();

      //get file extension
      $extension = $file->getClientOriginalExtension();

      $filename = (string) Str::uuid();
      $filenameThumbNail = $filename . '_tn.' . $extension;
      $filename .= '.' . $extension;

      //shell_exec('clamscan myuploadedfile.zip');
      $file->storeAs('public/pics', $filename);
      $file->storeAs('public/pics/tn', $filenameThumbNail);

      //create small thumbnail
      $smallThumbNailPath = public_path('storage/pics/tn/' . $filenameThumbNail);
      $this->createThumbnail($smallThumbNailPath, 150, 93);

//             Storage::disk('local')->put($filename, File::get($file));

      if(!$post_id)
      {
        $pic = Pic::where('user_id', $user->id)->where('profile', true)->first();
        //there is an old pic entry that needs to be updated
        if($pic)
        {
          $pic->profile = false;
          $pic->update();
        }
      }

      $pic = new Pic();
      $pic->user_id = $user->id;
      $pic->post_id = $post_id;
      $pic->filename = $filename;
      $pic->profile = $post_id == null;
      $pic->save();
      return "Image was successfully uploaded.";
    }
    return "Image was not successfully uploaded.";
  }

  /**
  * Create a thumbnail of specified size
  *
  * @param string $path path of thumbnail
  * @param int $width
  * @param int $height
  */
  private function createThumbnail($path, $width, $height)
  {
    $img = Image::make($path)->resize($width, $height, function ($constraint) {
      $constraint->aspectRatio();
    });
    $img->save($path);
  }

  public function getPostProfile(Request $request, $post_id)
  {
    $post = (new PostController)->getPost($post_id);
    if($post)
    {
      $pic = Pic::where('user_id', $post->user_id)->where('profile', true)->first();
      if($pic)
      {
        $fileName = $pic->filename;
        $pathParts = explode('.', $fileName);
        $ext = $pathParts[1];
        $fileName = $pathParts[0] . '_tn.' . $ext;
        $file = Storage::disk('local')->get('public/pics/tn/' . $fileName);
        return new Response($file, 200);
      }
    }
    return new Response("Image not found for " . $post_id , 404);
  }

  public function getUserProfilePic(Request $request, $user_id)
  {
    $pic = Pic::where('user_id', $user_id)->where('profile', true)->first();
    if($pic)
    {
      $fileName = $pic->filename;
      $pathParts = explode('.', $fileName);
      $ext = $pathParts[1];
      $fileName = $pathParts[0] . '_tn.' . $ext;
      $file = Storage::disk('local')->get('public/pics/tn/' . $fileName);
      return new Response($file, 200);
    }
    return new Response("Image not found for " . $post_id , 404);
  }
}
