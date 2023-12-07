@extends('layouts.master')

@section('title')
KW - Friends
@endsection

@section('content')
  @include('includes.message-block')
  <!-- Request received -->
<section class="row" id="requests">
  <div class="col-md-6 col-md-offset-3">
    <header><h3><i class="bi bi-arrows"></i> Request Received</h3></header>
    @if (count($requests) > 0)
      @foreach($requests as $r)
      @endforeach
    @else
      No request waiting
    @endif
  </div>
<section>

  <!-- List friends -->
<section class="row" id="friends">
  <div class="col-md-6 col-md-offset-3">
    <header><h3><i class="bi bi-people-fill"></i> F/friends</h3></header>
    <div class="container" style="border: 1px solid green;">
      <div class="row align-items-start" style="border: 1px solid red;">
        @if (count($friends) > 0)
          @foreach($friends as $friend)
            <div class="col" style="border: 1px solid blue;">
              {{ $friend->user()->first()->id == Auth::user()->id ? $friend->friend()->first()->name : $friend->user()->first()->name }} <br>
              <img src="{{ route('image.profile', ['user_id' => $friend->user()->first()->id == Auth::user()->id ? $friend->friend()->first()->id : $friend->user()->first()->id]) }}" alt="{{ $friend->user()->first()->id == Auth::user()->id ? $friend->friend()->first()->name : $friend->user()->first()->name }}" class="img-responsive"> <br>
              <div class="interaction" data-friendid="{{ $friend->user()->first()->id == Auth::user()->id ? $friend->friend()->first()->id : $friend->user()->first()->id }}" data-name="{{ $friend->user()->first()->id == Auth::user()->id ? $friend->friend()->first()->name : $friend->user()->first()->name }}">
                <a href="#" class="profile"><i class="bi bi-person-circle"></i></a> | <a href="#" class="request"><i class="bi bi-people-fill"></i></a>
              </div>
            </div>
          @endforeach
        @else
          No friends, shame.
        @endif
      </div>
    </div>
  </div>
<section>

<!-- List follower -->
<section class="row" id="friends">
  <div class="col-md-6 col-md-offset-3">
    <header><h3><i class="bi bi-arrows"></i> F/followers</h3></header>
    @if (count($friends) > 0)
      @foreach($friends as $friend)
      @endforeach
    @else
      No one is following Y/you
    @endif
  </div>
<section>

<!-- List following -->
<section class="row" id="friends">
  <div class="col-md-6 col-md-offset-3">
    <header><h3><i class="bi bi-arrows"></i> Following</h3></header>
    @if (count($friends) > 0)
      @foreach($friends as $friend)
      @endforeach
    @else
      Not following anyone.
    @endif
  </div>
<section>

  <!-- Search all ppl -->
  <!-- List all ppl -->
<section class="row" id="users">
  <div class="col-md-6 col-md-offset-3">
    <header><h3><i class="bi bi-people-fill"></i> E/everyone</h3></header>
    <div class="container" style="border: 1px solid green;">
      <div class="row align-items-start" style="border: 1px solid red;">
        @if (count($users) > 0)
          @foreach($users as $user)
            <div class="col" style="border: 1px solid blue;">
              {{$user->name}} <br>
              <img src="{{ route('image.profile', ['user_id' => $user->id]) }}" alt="{{ $user->name }}" class="img-responsive"> <br>
              <div class="interaction" data-friendid="{{$user->id}}" data-name="{{ $user->name }}">
                <a href="#" class="profile"><i class="bi bi-person-circle"></i></a> | <a href="#" class="request"><i class="bi bi-people"></i></a> | <a href="#" class="follow"><i class="bi bi-arrows"></i></a>
              </div>
            </div>
          @endforeach
        @else
          Where did everyone go?
        @endif
      </div>
    </div>
  </div>
<section>

@include('includes.confirm')

<script>
  var token = '{{ Session::token() }}';
  var urlNewFriend = '{{ route('post.request') }}';
  var urlUnfriend = '{{ route('cancel.request') }}';
</script>
@endsection

