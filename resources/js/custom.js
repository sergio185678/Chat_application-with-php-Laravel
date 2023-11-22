const btn_create = document.querySelector("#create");
var load_no=20;
var no_more=false;

if(btn_create!=null){ //pongo este if para que solo haga estas acciones cuando este logeado recien

  //evento click para crear un nuevochar
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
        users: arr_usuarios, //aca enviar un array de ids de usuario
    };
    //las rutas del post como "chat" aca deben configurarse en el web.php la parte de rutas
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
      //notificacion para cuando crees un nuevo chat
      //muestro el txt del data que obtengo de la respuesta del POST
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

  //evento para cerrar y limpiar cuando quieres crear un chat
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

  //llamo cada cierto tiempo a esta función para que actualize constantemente la notificacion de cantida de mensajes nuevos
  setInterval(chat_update,1200);
  function chat_update(){
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
            if(id==k){ //para cada chat le eliminas y creas denuevo para que se actualize su contados de mensajes
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

  //toda la funcionalidad al clickear un chat en especifico
  $("body").on("click",".chat-item",function(){
      //el que seleccione le agrega esa clase, y sus hermanos se le quita esa clase
      //para que solo el que clickeastes tenga solo esa clase
      $(this).addClass("chat-select").siblings().removeClass("chat-select");
      var c_id=$(this).attr("id");
      //aveces se puede obtener el token del input
      var tk =$("#create-msg-form").find("input[name=_token]").val();
      //$("#create-msg-form").find("#chat-id").val(c_id);

      var el=$(this);
      msg_load(c_id,tk,10,true,el); //para cargar por primera vez los mensajes del chat

      //todo esto para el typing 
      var textarea=$("#msg");//textarea donde redactas el mensaje
      var lastTypedTime=new Date(0);
      var typingDelayMillis=4000;
      //busca constantemente si esta escribiendo la persona o no
      setInterval(refreshTypingStatus,2000);
      function refreshTypingStatus(){
        if(!textarea.attr("disabled")&&textarea.is(":focus")){
          //el else es cuando recien esta escribiendo la persona, mientras que el if puede ser como que solo dejo el click en el textarea
          if(textarea.val()==""||new Date().getTime()-lastTypedTime.getTime()>typingDelayMillis){
            set_typing(0);
          }else{
            set_typing(1);
          }
        }
      }

      function updateLastTypedTime(){
        lastTypedTime=new Date();
      }
  
      textarea.keypress(updateLastTypedTime);//cuando presionas cualquier tecla
      textarea.blur(function(){ //con respecto al focus como que el usuario mueve su mouse afuera del textarea
        set_typing(0);
      });

      //permite actualizarle constantemente con mensajes nuevos que recibes de otra persona de los chats
      setInterval(function() {
        new_msg_load(c_id, tk, 0, 0);
        }, 5000);

      //complementa la función de typing mostrando quien esta escribiendo, etc
      setInterval(check_typing,1000);
  })

  //se encarga de actualizar en la tabla activechat el typing para ver si o no esta escribiendo la persona en un chat
  //en especifico
  function set_typing(con){
    //"con" es si esta escribiendo o no
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

  //se encarga de saber que usuarios estan escribiendo en un chat en especifico
  //y en caso positivo agregara en el html que usuario esta escribiendo
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
        //aca imprimi en el html
        if(data.status==1){
          $("#typing_on").html(data.user_name+" typing ...");
        }else{
          $("#typing_on").html("");
        }
      });
    }
  }

  //evento de crear envuar un nuevo mensaje
  $("body").on("click","#create-msg",function(){
    var tf=$("#msg");//texarea
    var tk=$("#create-msg-form").find("input[name=_token]").val();
    var msg=$("#msg").val();//texto dentro
    var c_id=$(".chat-select").attr("id");//chat activo seleccionado
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
      //fst se refiere que si solo hay un mensaje en total, o si hay mas de uno 
      //mirar el codigo de la función para entender mejor
      if(resp.status==1){
        tf.val('');
        if(resp.fst==0){
          var fst=0;
        }else{
          var fst=1;
        }
        new_msg_load(c_id,tk,1,fst);
      }
    });
  })

  //función para cargar por primera vez los mensajes
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
        limit:limit,//por defecto carga los 10 ultimos mensajes
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
          if(first==false){
            //busca agregar los nuevos mensajes
            $("#msg-body").prepend(data.txt);
            load_no=load_no+10;
            //en caso que haya mas mensajes agrega el boton de cargar mas
            if(data.end==true){
              no_more=true;
              $("#load_more").remove();
            }else{
              no_more=false;
              $("#load_more").show();
            }
          }else{
            $("#msg-body").empty().html(data.txt);
            var objDiv=document.getElementById("msg-body");

            if((Math.ceil($("#msg-body").scrollTop()+$("#msg-body").innerHeight()))>=(objDiv.scrollHeight-110)||first==true){
              objDiv.scrollTop=objDiv.scrollHeight;
            }
          }
          
          //habilita el textarea y boton
          $("#create-msg-form").find("#msg").prop("disabled",false);
          $("#create-msg-form").find("#create-msg").prop("disabled",false);
          msg_seen(c_id,tk,el);//llama a la función que cuando carge todo sea considerado como visto
          make_active(c_id,tk);//crea la opción para lo del typing con el activechat
        }
      });
    }
  }

  //recarga todo los nuevos mensajes
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

  //actualiza de mensajes no visto a visto
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

  //permite crear la opción de active creando filas en la tabla activechat
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
    }
  }

  //usando el evento scroll permite recargar el boton de load more en caso que haya mas mensajes
  //sino elimina ese boton
  $("#msg-body").on("scroll",function(){
    var scrollTop=$(this).scrollTop();
    if(scrollTop<=0 && no_more==false){
      $(this).prepend("<div id='load_more'>Load more</div>")
    }else{
      $(this).find("#load_more").remove();
    }
  });
  //carga los mensajes antiguos
  $("body").on("click","#load_more",function(){
    var c_id=$(".chat-select").attr("id");
    var tk=$("#create-msg-form").find('input[name=_token]').val();
    var el=$("#"+c_id);
    msg_load(c_id,tk,load_no,false,el);
  })

  //estos 2 funciones sirven para crear correctamente el evento de subir algun archivo
  $("#pic_btn").click(function(){
    $("#pic_file").click();
  });
  $("#pic_file").change(function(){
    $("#pic_submit").click();
  });
}
