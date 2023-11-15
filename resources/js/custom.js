const btn_create = document.querySelector("#create");

btn_create.addEventListener("click", () => {
    var items_checked = document.querySelectorAll(".checked");
    var arr_usuarios=[];
    items_checked.forEach(function(item) {
        arr_usuarios.push(item.id)
    });
    var formulario = document.getElementById('create-form');
    var tokenCSRF = formulario.querySelector('input[name="_token"]').value;
    var data_enviar = {
        _token: tokenCSRF,
        users: arr_usuarios,
    };
    fetch('chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data_enviar),
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('La solicitud no se completó correctamente');
      }
      return response.json();
    })
    .then(data => {
      const resp_alert = document.getElementById("resp_alert");

      if(data.status==1){
        resp_alert.innerHTML = `<div class="alert alert-success" role="alert" id="alerttt">`+data.txt+`</div>`;
        setTimeout(function() {
          location.reload();
      }, 2000);
      }
      else{
        resp_alert.innerHTML = `<div class="alert alert-danger" role="alert" id="alerttt">`+data.txt+`</div>`;
      }
    })
});

const closetBtn = document.querySelector("#close_button");
const xBtn = document.querySelector("#x_button");
closetBtn.addEventListener("click", () => {
  limpiar();
});
xBtn.addEventListener("click", () => {
  limpiar();
});
function limpiar(){
  const divElement = document.getElementById("alerttt");
  if (divElement) {
    divElement.remove();
  }

  const divsConClaseChecked = document.querySelectorAll('li.checked');
  divsConClaseChecked.forEach(div => {
    div.classList.remove('checked');
  });

  const btnText = document.querySelector(".btn-text");
  btnText.innerText = "Nothing selected";
  btn_create.disabled=true;

  const selectBtn = document.querySelector(".select-btn");
  selectBtn.classList.remove('open');
}
setInterval(chat_update,1200);
$("body").on("click",".chat-item",function(){
    $(this).addClass("chat-select").siblings().removeClass("chat-select");
    var c_id=$(this).attr("id");
    var tk =$("#create-msg-form").find("input[name=_token]").val();
    $("#create-msg-form").find("#chat-id").val(c_id);

    var el=$(this);
    msg_load(c_id,tk,10,true,el);

    var textarea=$("#msg");
    var typingStatus=$("#typing_on");
    var lastTypedTime=new Date(0);
    var typingDelayMillis=4000;
    function refreshTypingStatus(){
      if(!textarea.attr("disabled")&&textarea.is(":focus")){
        if(textarea.val()==""||new Date().getTime()-lastTypedTime.getTime()>typingDelayMillis){
          console.log("affs")
          set_typing(0);
        }else{
          set_typing(1);
        }
      }
    }
    function updateLastTypedTime(){
      lastTypedTime=new Date();
    }
    setInterval(refreshTypingStatus,2000);
    textarea.keypress(updateLastTypedTime);
    textarea.blur(function(){
      set_typing(0);
    });

    setInterval(function() {
      new_msg_load(c_id, tk, 0, 0);
      }, 5000);
    setInterval(check_typing,1000);
})

$("body").on("click","#create-msg",function(){
  var bt=$(this);
  var tf=$("#msg");
  var tk=$("#create-msg-form").find("input[name=_token]").val();
  var msg=$("#msg").val();
  var c_id=$(".chat-select").attr("id");
  $.ajax({
    method:"post",
    url:"message",
    data:{
      "msg":msg,
      "c_id":c_id,
      "_token":tk
    }
  }).done(function(resp){
    resp=$.parseJSON(resp);
    if(resp.status==1){
      tf.val('');
      if(resp.fst==0){
        var fst=0;
      }else{
        var fst=1;
      }
      new_msg_load(c_id,tk,1,fst);
    }else if(resp.status==0){

    }
  });
})

function msg_load(c_id,tk,limit,first,el){
  if(c_id==null||c_id==""){
    var c_id=$(".chat-select").attr("id");
  }
  if(tk==null||tk==""){
    var tk=$("#create-msg-form").find('input[name=_token]').val();
  }
  if(c_id!=null&&c_id!=''){
    var data_enviar = {
      c_id:c_id,
      limit:limit,
      _token: tk
    };
    fetch('message-list', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data_enviar),
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('La solicitud no se completó correctamente');
      }
      return response.json();
    })
    .then(data => {
      if(data.status==1){
        $("#msg-body").empty().html(data.txt);
        var objDiv=document.getElementById("msg-body");

        if((Math.ceil($("#msg-body").scrollTop()+$("#msg-body").innerHeight()))>=(objDiv.scrollHeight-110)||first==true){
          objDiv.scrollTop=objDiv.scrollHeight;
        }
        $("#create-msg-form").find("#msg").prop("disabled",false);
        $("#create-msg-form").find("#create-msg").prop("disabled",false);
        msg_seen(c_id,tk,el);
        make_active(c_id,tk);
      }
    });
  }
}

function new_msg_load(c_id,tk,me=0,fst){
  if(c_id!=null&&c_id!=''){
    var data_enviar = {
      c_id:c_id,
      me:me,
      _token: tk
    };
    fetch('new-message-list', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data_enviar),
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('La solicitud no se completó correctamente');
      }
      return response.json();
    })
    .then(data => {
      if(data.status==1){
        if(fst==0){
          $("#msg-body").append(data.txt);
        }else{
          $("#msg-body").html(data.txt);
        }
        var objDiv=document.getElementById("msg-body");

        if((Math.ceil($("#msg-body").scrollTop()+$("#msg-body").innerHeight()))>=(objDiv.scrollHeight-110)||first==true){
          objDiv.scrollTop=objDiv.scrollHeight;
        }
        $("#create-msg-form").find("#msg").prop("disabled",false);
        $("#create-msg-form").find("#create-msg").prop("disabled",false);
        msg_seen(c_id,tk);
        make_active(c_id,tk);
      }
    });
  }
}

function msg_seen(c_id,tk,el){
  if(c_id!=null&&c_id!=''){
    var data_enviar = {
      c_id:c_id,
      _token: tk
    };
    console.log(data_enviar);
    fetch('message-seen', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data_enviar),
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('La solicitud no se completó correctamente');
      }
      return response.json();
    })
    .then(data => {
      if(data.status==1){
        if(el!=null){
          el.removeClass("new-msg");
          el.find(".new-msg-count").remove();
        }
      }
    });
  }
}

function make_active(c_id,tk){
  if(c_id!=null && c_id!="" && !$("#msg").attr("disabled")){
    var data_enviar = {
      c_id:c_id,
      _token: tk
    };
    console.log(data_enviar);
    fetch('active', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data_enviar),
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('La solicitud no se completó correctamente');
      }
      return response.json();
    })
    .then(data => {
      if(data.status==1){
        
      }
    });
  }
}

function set_typing(con){
  var c_id=$(".chat-select").attr("id");
  var tk=$("#create-msg-form").find('input[name=_token]').val();
  if(c_id!=null && c_id!="" && !$("#msg").attr("disabled")){
    var data_enviar = {
      con:con,
      c_id:c_id,
      _token: tk
    };
    fetch('set-active', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data_enviar),
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('La solicitud no se completó correctamente');
      }
      return response.json();
    })
    .then(data => {
      if(data.status==1){
        
      }
    });
  }
}

function check_typing(){
  var c_id=$(".chat-select").attr("id");
  var tk=$("#create-msg-form").find('input[name=_token]').val();
  if(c_id!=null && c_id!="" && !$("#msg").attr("disabled")){
    var data_enviar = {
      c_id:c_id,
      _token: tk
    };
    fetch('check-active', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data_enviar),
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('La solicitud no se completó correctamente');
      }
      return response.json();
    })
    .then(data => {
      if(data.status==1){
        $("#typing_on").html(data.user_name+" typing ...");
      }else{
        $("#typing_on").html("");
      }
    });
  }
}

function chat_update(){//para que se actualize constantemente la notificacion de cantida de mensajes nuevos
  fetch('chat-update', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('La solicitud no se completó correctamente');
    }
    return response.json();
  })
  .then(data => {
    $(".chat-item").each(function(){
      var el=$(this);
      var id=el.attr("id");

      if(data!=""){
        $.each(data,function(k,v){
          if(id==k){
            el.removeClass("new-msg");
            $(".new-msg-count").remove();
            el.addClass("new-msg");
            el.append("<div class='new-msg-count'>"+v+'</div>');
          }
        })
      }else{
        el.removeClass("new-msg");
        $(".new-msg-count").remove();
      }
    })
  });
}