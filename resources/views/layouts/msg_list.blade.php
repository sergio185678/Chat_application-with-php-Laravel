@if (count($msgs)>0)
  @foreach ($msgs->reverse() as $msg)
    <div id="{{$msg->id}}" class="msg-item <?php echo ($msg->user_id == $me->id)?'me':''; ?>">
      <img class="msg-item-img" src="{{asset('img/user-default.PNG')}}">
      <div class="msg-item-txt">
          {{$msg->msg}}
          <div class="msg-item-data">
              @if ($msg->created_at->diffInHours(\Carbon\Carbon::now(),false)>24)
                {{$msg->created_at->format('d F Y h:i A')}}
              @else
                {{$msg->created_at->diffForHumans()}}
              @endif
          </div>
      </div>
    </div>
  @endforeach
@else
  <div class="no-record text-center">No Message Exit</div>
@endif
