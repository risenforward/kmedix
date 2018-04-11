@extends('layouts.app')

@section('title', '| Reset password')
@section('body_class', 'login-page')
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="/">Kmedix Group</a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Reset Password</p>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ url('/password/reset') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                <input name="email" type="email" class="form-control" placeholder="Email" value="{{ $email or old('email') }}">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if ($errors->has('email'))
                    <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
            </div>
            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                <input name="password" type="password" class="form-control" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                    <span class="help-block">{{ $errors->first('password') }}</span>
                @endif
            </div>
            <div class="form-group has-feedback{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <input name="password_confirmation" type="password" class="form-control" placeholder="Password confirmation">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                @endif
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary btn-flat center-block">Reset Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
