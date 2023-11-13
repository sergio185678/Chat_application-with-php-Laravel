@if (count($chats)>0)

    @foreach ($chats as $chat)
    <div id="{{$chat->id}}" class="chat-item">
        <div class="d-blockk">
        @if(count($chat->users)>2)
            <img class="chat-item-img" src="{{asset('img/group-default.png')}}">
        @else
            <img class="chat-item-img" src="{{asset('img/user-default.PNG')}}">
        @endif
            <div class="chat-items-users">
                <?php
                    $un=[];
                    foreach($chat->users as $u){
                        if($me->id !== $u->id){ #no me cuento a mi
                            $un[]=$u->name;
                        }
                    }
                    $un=implode(", ", $un);
                    echo( strlen($un)>17)? substr($un,0,17) ."...":$un;
                ?>
            </div>
        </div>
        <?php
            if(array_key_exists($chat->id,$total_msg)){
                $c=($total_msg[$chat->id]>20)?"20+":$total_msg[$chat->id];
                echo "<div class='new-msg-count'>".$c."</div>";
            }
        ?>
    </div>
    @endforeach
    
@else
    <div class="no-record text-center"> No chat Exist </div>  
@endif 