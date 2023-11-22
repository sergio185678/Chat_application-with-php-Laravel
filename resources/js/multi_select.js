const selectBtn = document.querySelector(".select-btn"),
      items = document.querySelectorAll(".item"),
      btn_create = document.querySelector("#create");

if(selectBtn!== null){
    selectBtn.addEventListener("click", () => {
        selectBtn.classList.toggle("open");
    });
    
    items.forEach(item => {
        item.addEventListener("click", () => {
            item.classList.toggle("checked");
    
            let checked = document.querySelectorAll(".checked"),
                btnText = document.querySelector(".btn-text");
    
                if(checked && checked.length > 0){
                    btnText.innerText = `${checked.length} Selected`;
                    btn_create.removeAttribute("disabled");
                }
                else{
                    btnText.innerText = "Nothing selected";
                    btn_create.disabled=true;
                }
        });
    })
}