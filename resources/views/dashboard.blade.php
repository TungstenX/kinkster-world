@extends('layouts.master')

@section('title')
  KW - Dashboard
@endsection

@section('content')
@include('includes.message-block')

<section class="row new-post">
  <div class="col-md-6 col-md-offset-3">
    <header><h3>What&#39;s on Y/your mind?</h3></header>
    <div class="form-group">
      <input class="form-control" name="fake-body" id="fake-body" type="text" placeholder="Your Post" data-display="{{ Auth::user()->userPrefs()->where('key','Post Audience')->first()->value }}">
    </div>
    <button type="submit" class="btn btn-primary" id="fake-button" data-display="{{ Auth::user()->userPrefs()->where('key','Post Audience')->first()->value }}">Create Post</button>
  </div>
</section>

<section class="row posts" id="posts">
  <div class="col-md-6 col-md-offset-3">
    <header><h3>What O/others are thinking</h3></header>
    @foreach($posts as $post)
      <article class="post" data-postid="{{ $post->id }}" data-display="{{ $post->display }}">
          <div class="info" style="overflow: auto;">
            <div style="float: left">
              <i class="bi bi-{{ $post->display == 'public' ? 'globe' : ($post->display == 'friends' ? 'people' : ($post->display == 'circle' ? 'circle' : 'slash-circle')) }}" style="float: left;"></i>
              <strong>{{ $post->user->name }}</strong> <br> {{ $extra[$post->id]['when'] }}
            </div>
            <div style="float: right">
              <img src="{{ route('post.profile', ['post_id' => $post->id]) }}" alt="{{ Auth::user()->name }}" class="img-thumbnail" id="post-thumbnail" style="height: 50px;">
            </div>
          </div>
          <p style="clear: left;">{{ $post->content }}</p>
          <div class="interaction">
            <a href="#" class="like"><i class="bi {{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 1 ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' : 'bi-hand-thumbs-up'  }}"></i></a>
            {{ (strlen($extra[$post->id]['likes']) > 0 ? ('<sup>' . $extra[$post->id]['likes'] . '</sup>') : '') }} |
            <a href="#" class="like"><i class="bi {{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 0 ? 'bi-hand-thumbs-down-fill' : 'bi-hand-thumbs-down' : 'bi-hand-thumbs-down'  }}"></i></a>
            {{ (strlen($extra[$post->id]['dislikes']) > 0 ? ('<sup>' . $extra[$post->id]['dislikes'] . '</sup>') : '') }}
            @if(Auth::user() == $post->user)
              |
              <a href="#" class="edit"><i class="bi bi-pencil"></i></a> |
              <a href="{{ route('post.delete', ['post_id' => $post->id]) }}" class="del"><i class="bi bi-eraser"></i></a>
            @endif
          </div>
      </article>
    @endforeach
  </div>
</section>

<div class="modal fade" tabindex="-1" role="dialog" id="edit-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Post</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="post-body-audience">Post Audience</label>
            <div class="dropdown">
              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="post-body-audience">
                Public
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="#" data-value="private">Private</a></li>
                <li><a href="#" data-value="friends">Friends</a></li>
                <li><a href="#" data-value="circle">Circle</a></li>
                <li><a href="#" data-value="public">Public</a></li>
              </ul>
            </div>

            <label for="post-body" id="label-post-body">Edit the Post</label>
            <textarea class="form-control" name="post-body" id="post-body" rows="5"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <span class="error" id="modal-error"></span>&nbsp;&nbsp;
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="modal-save">Save changes</button>
      </div>
    </div>
  </div>
</div>

<script>
  var token = '{{ Session::token() }}';
  var urlNewPost = '{{ route('post.create') }}';
  var urlEdit = '{{ route('edit') }}';
  var urlLike = '{{ route('like') }}';
</script>
@endsection
