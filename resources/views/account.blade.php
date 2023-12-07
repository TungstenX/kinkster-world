@extends('layouts.master')

@section('title')
KW - Account
@endsection

@section('content')
  @include('includes.message-block')
  <section class="row new-post">
    <div class="col-md-6 col-md-offset-3">
      <header><h3>Your Account</h3></header>
        <form action="{{ route('account.save') }}" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label for="name">Nickname</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" id="name">
          </div>

          <!-- Can be made better if we use a type and source for prefs? -->
          <!-- TODO: Figure out how to send dropdown via post -->
          @foreach($prefs as $pref)
            <div class="form-group">
              <label for="pref_{{ $pref->key }}">{{ $pref->key }}</label>
              @if($pref->key == 'Post Audience')
                <div class="dropdown">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="pref_{{ preg_replace('/\s+/', '_', $pref->key) }}" name="pref_{{ preg_replace('/\s+/', '_', $pref->key) }}">
                    {{ ($pref->value && strlen($pref->value) > 0) ? ucfirst($pref->value) : 'Public' }}
                    <span class="caret"></span>
                  </button>
                  <input type="hidden" id="f_pref_{{ preg_replace('/\s+/', '_', $pref->key) }}" name="f_pref_{{ preg_replace('/\s+/', '_', $pref->key) }}" value="{{ ($pref->value && strlen($pref->value) > 0) ? strtolower($pref->value) : 'public' }}">
                  <ul class="dropdown-menu">
                    @if($pref->key && $pref->key = 'Post Audience')
                      <li><a href="#" data-value="private">Private</a></li>
                      <li><a href="#" data-value="friends">Friends</a></li>
                      <li><a href="#" data-value="circle">Circle</a></li>
                      <li><a href="#" data-value="public">Public</a></li>
                    @endif
                  </ul>
                </div>
              @endif
              @if($pref->key == 'Posts Per Page' || $pref->key == 'Friends Per Page')
                <input type="number" name="f_pref_{{ preg_replace('/\s+/', '_', $pref->key) }}" class="form-control" value="{{ $pref->value }}" id="f_pref_{{ preg_replace('/\s+/', '_', $pref->key) }}">
              @endif
            </div>
          @endforeach

          <div class="form-group">
            <label for="image">Profile Image</label>
            <input type="file" name="image" class="form-control" id="image">
          </div>
          <button type="submit" class="btn btn-primary">Save Account</button>
          <input type="hidden" value="{{ Session::token() }}" name="_token">
        </form>
      </div>
  </section>

  @if (Storage::disk('local')->has('public/pics/' . $profilePic))
    <section class="row new-post">
      <div class="col-md-6 col-md-offset-3">
        <img src="{{ route('account.image', ['filename' => 'tn']) }}" alt="{{ $user->name }}" class="img-responsive" id="pic-thumbnail">
      </div>
    </section>
    <div class="modal fade" tabindex="-1" role="dialog" id="pic-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <img src="{{ route('account.image', ['filename' => 'normal']) }}" alt="{{ $user->name }}" class="img-responsive" id="pic-original">
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  @endif

@endsection
