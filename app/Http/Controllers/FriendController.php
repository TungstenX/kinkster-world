<?php
/**
TODO List:
1. getFriends validate start_page
**/
namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
  public function getFriends(Request $request, $start_page = 0)
  {
//     $this->validate($request, [
//       'start_page' => 'integer'
//     ]);

    $user = Auth::user();
    $friends = $user->friends()->orderBy('status', 'desc')->orderBy('created_at', 'desc')->get();
    $friendRequestReceived = Friend::where('friend_id', $user->id)->orderBy('status', 'desc')->orderBy('created_at', 'desc')->get();
    $allUsers = $this->getAllUsers($request);
    return view('friends', ['friends' => $friends, 'requests' => $friendRequestReceived, 'users' => $allUsers]);
  }

  /**
  * TODO:
  * use search criteria in request
  **/
  public function getAllUsers(Request $request)
  {
    $this->validate($request, [
      'start_page' => 'integer'
    ]);
    $user = Auth::user();
    $take = (int)$user->userPrefs()->where('key', config("constants.PREF_FRIENDS_PER_PAGE"))->first()->value;
    $skip = (int)($request['start_page'] ? $request['start_page'] : 0) * $take;
    $ignoreList = array();
    $ignoreList[] = $user->id;
    foreach($user->friends()->whereIn('status', ['request', 'accept', 'block'])->get() as $ignoreFriend)
    {
      $ignoreList[] = $ignoreFriend->friend_id;
    }
//     error_log("##!!!\n\tignore list: " . print_r($ignoreList, 1), 0);
    return User::whereNotIn('id', $ignoreList)->skip($skip)->take($take)->get();
  }

  public function postRequest(Request $request) {
    $this->validate($request, [
      'friendid' => 'required|integer'
    ]);
    if($request['friendid'] == Auth::user()->id) {
      return response()->json([
        'message' => 'Y/you can be friends with Y/yourself, but not here.'
        ], 422);
    }
    // WHERE (user_id = auth AND friend_id = friendid) OR (user_id = friendid AND friend_id = auth) AND (status is not 'decline' OR updated_at cooled off)
    $friend = Friend::where(
      function ($query) use ($request) {
        $query->where( function($q) use ($request) {// WHERE (user_id = auth AND friend_id = friendid)
          $q->where('user_id', '=', Auth::user()->id)
          ->where('friend_id', '=', $request['friendid']);
          })
          ->orWhere( function ($q) use ($request) { // OR (user_id = friendid AND friend_id = auth)
            $q->where('user_id', '=', $request['friendid'])
            ->orWhere('friend_id', '=', Auth::user()->id);
          });
      })
      ->where(// AND (the status is not decline OR the update is older than the friend cool off interval
      function ($query) {
              $query->where('status', '<>', 'decline')
              ->orWhere('updated_at', '<=', Carbon::now()->subDays(config("constants.FRIENDS_COOL_OFF")));
      })
      ->first();

    if($friend)
    {
      $msg = "Not specified";
      if($friend->user_id == Auth::user()->id)
      {
        $msg = 'Y/you already send the request';
      }
      elseif($friend->friend_id == Auth::user()->id)
      {
        $msg = 'Oi, do some house keeping! A friend request was already send to Y/you.';
      }

      return response()->json([
        'message' => $msg
        ], 422);
    }

    // See if we got an ex-cool off friend

    $friend = Friend::where(
      function ($query) use ($request) {
        $query->where( function($q) use ($request) {// WHERE (user_id = auth AND friend_id = friendid)
          $q->where('user_id', '=', Auth::user()->id)
          ->where('friend_id', '=', $request['friendid']);
          })
          ->orWhere( function ($q) use ($request) { // OR (user_id = friendid AND friend_id = auth)
            $q->where('user_id', '=', $request['friendid'])
            ->orWhere('friend_id', '=', Auth::user()->id);
          });
      })
      ->where(// AND (the status is  decline AND the update is older than the friend cool off interval
      function ($query) {
              $query->where('status', '=', 'decline')
              ->Where('updated_at', '<=', Carbon::now()->subDays(config("constants.FRIENDS_COOL_OFF")));
      })
      ->first();
    if($friend && $friend->friend_id == Auth::user()->id)
    {
      // The friend request but user declined, but we are now asking
      $friend->friend_id = $friend->user_id;
      $friend->user_id = Auth::user()->id;
      $friend->status = 'request';
      $friend->update();
    }
    else
    {
      $friend = new Friend();
      $friend->user_id = Auth::user()->id;
      $friend->friend_id = $request['friendid'];
      $friend->status = 'request';
      $friend->save();
    }
    return response()->json(['status' => $friend->status], 200);
  }

  /**
  * TODO: Can this be used for unfriending as well?
  **/
  public function cancelRequest(Request $request) {
    $this->validate($request, [
      'friendid' => 'required|integer'
    ]);

    $user = Auth::user();
    $friend = $user->friends()->where('friend_id', $request['friendid'])->first();
    if($friend) {
      $friend->delete();
      return response()->json(['msg' => 'Friend request deleted'], 200);
    }
    return response()->json(['msg' => 'Friend request not found'], 404);
  }
}
