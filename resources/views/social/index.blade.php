<!-- resources/views/social/index.blade.php -->
@extends('layouts.app')
@section('content')
<h1>Welcome to the Social Media Network</h1>
<div>
  <form action="{{ route('social.createPost') }}" method="POST">
    @csrf
      <div>
        <textarea name="content" rows="3" placeholder="What's on your mind?"></textarea>
      </div>
    <button type="submit">Post</button>
  </form>
</div>
<div>
  @foreach ($posts as $post)
    <div>
      <p>{{ $post->user->name }}</p>
      <p>{{ $post->content }}</p>
      <p>{{ $post->created_at }}</p>
    </div>
    <hr>
  @endforeach
</div>
@endsection
