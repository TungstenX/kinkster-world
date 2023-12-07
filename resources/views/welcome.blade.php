@extends('layouts.master')

@section('title')
  Login
@endsection

@section('content')
@include('includes.message-block')

<div class="row gx-5 px-3">
  <div class="col-md-6"><!--   -->
    <div class="card" data-bs-theme="dark">
      <div class="card-header">Register</div>
      <form action="{{ route('register') }}" method="post">
        <div class="card-body">
          <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}" style="text-align: left;">
            <label for="email">Your E-mail</label>
            <input class="form-control" type="email" name="email" id="email" value="{{ Request::old('email') }}">
          </div>
          <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}" style="text-align: left;">
            <label for="name">Your Nickname</label>
            <input class="form-control" type="text" name="name" id="name" value="{{ Request::old('name') }}">
          </div>
          <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}" style="text-align: left;">
            <label for="password">Your Password</label>
            <input class="form-control" type="password" name="password" id="password" value="{{ Request::old('password') }}">
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
          <input type="hidden" name="_token" value="{{ Session::token() }}">
        </div>
      </form>
    </div>
  </div>
  <div class="col-md-6"> <!--  border border-primary border-2 rounded-3 p-3 -->
    <div class="pane cell">
      <h3>Sign In</h3>
      <form action="{{ route('signin') }}" method="post">
        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
          <label for="email">Your E-mail</label>
          <input class="form-control" type="email" name="email" id="email" value="{{ Request::old('email') }}">
        </div>
        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
          <label for="password">Your Password</label>
          <input class="form-control" type="password" name="password" id="password"  value="{{ Request::old('password') }}">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <input type="hidden" name="_token" value="{{ Session::token() }}">
      </form>
    </div>
  </div>
</div>

@endsection
