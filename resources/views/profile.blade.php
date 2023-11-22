@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        {{$me->name}}'s Profile
        <div class="container-fluid row">
            @if (Session::has('success'))
                <div class="alert alert-success">
                    {{Session::get('success')}}
                </div>
            @endif
            @if ($errors)
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">
                        {{$error}}
                    </div>
                @endforeach
            @endif
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col">
                <!--Cuidado aca debes poner la ruta de post en web.php, "el name debe ser igual, no necesariamente la ruta"--->
                <form id="pic-save" enctype="multipart/form-data" action="{{route('pic-save')}}" method="POST">
                    @csrf
                    <img class="profile-pic" src="{{asset('img/'.$me->pic)}}">
                    <input type="button" name="pic_btn" id="pic_btn" class="btn-block btn btn-primary btn-sm" value="Upload">
                    <input type="file" style="display:none;" id="pic_file" name="pic_file">
                    <input type="submit" style="display:none;" id="pic_submit" name="pic_submit" value="Save">
                </form>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col">
                <h3>Personal Detail</h3>
                <form id="profile-save" action="{{route('profile-save')}}" method="POST" class="pb-4">
                    @csrf
                    <div class="mb-2">
                        <label for="name">Name</label>
                        <input class="form-control" type="text" name="name" id="name" placeholder="Write your Name.." value="{{$me->name}}">
                    </div>
                    <div class="mb-2">
                        <label for="name">Email</label>
                        <input class="form-control" type="email" name="email" id="email" placeholder="Write your Email.." value="{{$me->email}}">
                    </div>
                    <input type="submit" name="submit" class="btn btn-sm btn-primary gasf" value="Save">
                </form>

                <h3>Reset Password</h3>
                <form id="pass-save" action="{{route('pass-save')}}" method="POST">
                    @csrf
                    <div class="container-fluid row">   
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col">
                        <label for="new_password">New Password</label>
                        <input class="form-control" type="password" name="new_password" id="new_password">
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col mb-2">
                        <label for="new_password_confirmation">Confirm Password</label>
                        <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation">
                    </div>
                    </div>
                    <input name="submit-pass" type="submit" class="btn btn-sm btn-primary gasf" value="Update Password">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
