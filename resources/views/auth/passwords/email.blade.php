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

        <form action="{{ url('/password/email') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                <input name="email" type="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if ($errors->has('email'))
                    <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary btn-flat center-block">Send Password Reset Link</button>
            </div>
        </form>
    </div>
</div>
@endsection
