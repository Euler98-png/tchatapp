const form = document.querySelector(".typing-area"),
incoming_id = form.querySelector(".incoming_id").value,
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector('button[type="submit"]'),chatBox = document.querySelector(".chat-box");
const emojiBtn = document.getElementById("emoji-btn");
const emojiPicker = document.getElementById("emoji-picker");

form.onsubmit = (e)=>{
    
    e.preventDefault();
}

// Traitement des emojis
// Affiche ou cache le sélecteur d'emojis au clic sur le bouton
emojiBtn.onclick = (e) => {
    e.preventDefault();
    if(emojiPicker.style.display === "none" || emojiPicker.style.display === ""){
        emojiPicker.style.display = "block";
    } else {
        emojiPicker.style.display = "none";
    }
};

// Insère l'emoji sélectionné dans le champ de saisie
emojiPicker.onclick = (e) => {
    if(e.target.classList.contains("emoji")){
        inputField.value += e.target.textContent;
        emojiPicker.style.display = "none";
        inputField.focus();
    }
};

// Ferme le sélecteur si on clique ailleurs
document.addEventListener("click", function(e){
    if(!emojiPicker.contains(e.target) && e.target !== emojiBtn){
        emojiPicker.style.display = "none";
    }
});

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != ""){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}

sendBtn.onclick = ()=>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              inputField.value = "";
              scrollToBottom();
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}
chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}

chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}

setInterval(() =>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
            let data = xhr.response;
            chatBox.innerHTML = data;
            if(!chatBox.classList.contains("active")){
                scrollToBottom();
              }
          }
      }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id="+incoming_id);
}, 500);

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
  }
  