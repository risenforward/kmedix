@extends('layouts.app')

@section('title', '| Log in')
@section('body_class', 'login-page')
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="/">Kmedix Group</a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="{{ url('/login') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                <input name="email" type="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
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
            @if ($msg = session('deactivated_user'))
                <div class="alert alert-danger">
                    <ul>
                        <li>{{ $msg }}</li>
                    </ul>
                </div>
            @endif
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
            </div>
        </form>
        <a href="{{ url('/password/reset') }}">I forgot my password</a><br>
    </div>
</div>
@endsection