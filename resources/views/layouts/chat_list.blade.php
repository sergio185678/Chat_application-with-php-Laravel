@if (count($chats)>0)

    @foreach ($chats as $chat)
    <div id="{{$chat->id}}" class="chat-item">
        <div class="d-blockk">
        <!-- Mira si el chat es grupal o individual -->
        @if(count($chat->users)>2)
            <img class="chat-item-img" src="{{asset('img/group-default.png')}}" style="border-radius: 50%;">
        @else
            <!-- Cambia la foto de perfil en los chats -->
            <?php
                foreach($chat->users as $u){
                    if($me->id != $u->id){
                        $img=$u->pic;
                    }
                }
            ?>
            <img class="chat-item-img" src="{{asset('img/'.$img)}}" style="border-radius: 50%;">
        @endif
            <!-- Para poner el nombre de los integrantes del chat sin contar con nosotros -->
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
        <!-- Cantidad de mensajes nuevos -->
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