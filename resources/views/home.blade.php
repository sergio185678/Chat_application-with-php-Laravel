@extends('layouts.app')

@section('content')
<div class="container-fluid row">
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col">
        <div class="card">
            <div class="card-header">Chat list</div>
            <div id="chat-body" class="card-body">
                @include("layouts.chat_list")
            </div>
            
        </div>

    </div>
    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col">
        <div class="card">
            <div class="card-header">Conversation</div>
            <div id="msg-body" class="card-body">
                @include("layouts.msg_list")
            </div>
            <div class="card-footer">
                    <form id="create-msg-form" class="row" action="">
                        @csrf
                        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                            <fieldset class="form-group">
                                <textarea name="msg" id="msg" class="form-control" placeholder="Write your Message.." disabled></textarea>
                                <p id="typing_on"></p>
                            </fieldset>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                            <fieldset class="form-group">
                                <input disabled type="button" name="sub" id="create-msg" class="btn btn-primary btn-block" value="Send">
                            </fieldset>
                            
                        </div>
                    </form>
            </div>
        </div>

    </div>
</div>
@endsection
