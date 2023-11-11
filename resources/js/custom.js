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

$("body").on("click",".chat-item",function(){
    $(this).addClass("chat-select").siblings().removeClass("chat-select");
    var c_id=$(this).attr("id");
    var tk =$("#create-msg-form").find("input[name=_token]").val();
    $("#create-msg-form").find("#chat-id").val(c_id);

    var el=$(this);

    msg_load(c_id,tk,10,true,el);
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
      new_msg_load(c_id,tk,1);
    }else if(resp.status==0){

    }
  })
})

function msg_load(c_id,tk,limit,first,el){
  if(c_id==null||c_id==""){
    var c_id=$("#chat-id").val();
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
    console.log(data_enviar);
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
      }
    });
  }
}

function new_msg_load(c_id,tk,me=0){
  if(c_id==null||c_id==""){
    var c_id=$(".chat-select").attr("id");
  }
  if(tk==null||tk==""){
    var tk=$("#create-msg-form").find('input[name=_token]').val();
  }
  if(c_id!=null&&c_id!=''){
    var data_enviar = {
      c_id:c_id,
      me:me,
      _token: tk
    };
    console.log(data_enviar);
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
        console.log(data);
        $("#msg-body").append(data.txt);
        var objDiv=document.getElementById("msg-body");

        if((Math.ceil($("#msg-body").scrollTop()+$("#msg-body").innerHeight()))>=(objDiv.scrollHeight-110)||first==true){
          objDiv.scrollTop=objDiv.scrollHeight;
        }
        $("#create-msg-form").find("#msg").prop("disabled",false);
        $("#create-msg-form").find("#create-msg").prop("disabled",false);
      }
    });
  }
}