<?php
/**
TODO List:
1. Paginate posts
**/

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Pic;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class PostController extends Controller
{
  public function getDashboard()
  {
    $user = Auth::user();
    $take = (int)$user->userPrefs()->where('key', 'Posts Per Page')->first()->value;
    $posts = Post::where(
      function ($query) {
        $query->where('user_id', '=', Auth::user()->id)
          ->orWhere('display', '=', 'public'); // or friends/circle
      })
      ->skip(0)//Not sure this is needed?
      ->take($take)
      ->orderBy('updated_at', 'desc')
      ->orderBy('created_at', 'desc')
      ->get();
    $likesOfPosts = array();
    $postExtras = array();

    foreach($posts as $post)
    {
      $likes = Like::where('post_id', $post->id)->where('like', true)->count();
      $dislikes = Like::where('post_id', $post->id)->where('like', false)->count();
      $extra = array();
      $extra['when'] = $this->calcWhen($post->created_at, $post->updated_at);
      $extra['likes'] = $likes <= 1 ? '' : (string)$likes;
      $extra['dislikes'] = $dislikes <= 1 ? '' : (string)$dislikes;
      $postExtras[$post->id] = $extra;
    }

    return view('dashboard', ['posts' => $posts, 'extra' => $postExtras]);
  }

  public function getPost($post_id)
  {
    return Post::where('id', $post_id)->first();
  }

  public function postCreatePost(Request $request)
  {
    $this->validate($request, [
      'body' => 'required|max:1000',
      'display' => 'required|in:private,friends,circle,public'
    ]);

    $post = new Post();
    $post->content = $request['body'];
    $post->display = strtolower($request['display']);
//         Upload and save image:

    $message = 'There was an error';
    if ($request->user()->posts()->save($post)) {
      $message = 'Post successfully created!';
    }

    return response()->json([
      'id' => $post->id,
      'display' => $post->display,
      'content' => $post->content,
      'user_name' => $request->user()->name,
      'created_at' => $post->created_at,
      'del_route' => route('post.delete', ['post_id' => $post->id]),
      'profile_route' => route('post.profile', ['post_id' => $post->id]),
      'message' => $message
      ], 200);
  }

  public function getDeletePost($post_id)
  {
    $post = Post::where('id', $post_id)->first();
    if (Auth::user() != $post->user) {
      return redirect()->back();
    }

    $likes = Like::where('post_id', $post_id)->get();
    if (($likes) && (count($likes) > 0))
    {
      $likes->each(function($like)
      {
        $like->delete();
      });
    };

    $post->delete();
    //Unlink image
    $img = Pic::where('post_id', $post_id)->first();
    if($img) {
      $img->post_id = null;
      $img->update();
    }

    return redirect()->route('dashboard')->with(['message' => 'Successfully deleted!']);
  }

  public function postEditPost(Request $request)
  {
    $this->validate($request, [
      'body' => 'required'
    ]);
    $post = Post::find($request['postId']);
    if (Auth::user() != $post->user) {
      return redirect()->back();
    }
    $post->content = $request['body'];
    $post->update();
    return response()->json(['new_body' => $post->content], 200);
  }

  public function postLikePost(Request $request)
  {
    $post_id = $request['postId'];
    $is_like = $request['isLike'] === 'true';
    $update = false;
    $post = Post::find($post_id);
    if (!$post) {
      return null;
    }
    $user = Auth::user();
    $like = $user->likes()->where('post_id', $post_id)->first();
    if ($like)
    {
      $update = true;
      $already_like = $like->like;
      if ($already_like == $is_like)
      {
        $like->delete();
        return null;
      }
    }
    else
    {
      $like = new Like();
    }
    $like->like = $is_like;
    $like->user_id = $user->id;
    $like->post_id = $post->id;
    if ($update)
    {
      $like->update();
    }
    else
    {
      $like->save();
    }
    return null;
  }

  private function calcWhen($created_at, $updated_at) {
    $created_dt = new Carbon($created_at);
    $updated_dt = new Carbon($updated_at);
    $use_dt = $updated_dt->greaterThan($created_dt) ? $updated_dt : $created_dt;
    return $use_dt->diffForHumans();
  }
}
