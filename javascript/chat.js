const form = document.querySelector("#message-form");
const incomingId = form.querySelector(".incoming_id").value;
const inputField = form.querySelector(".input-field");
const sendBtn = form.querySelector('button[type="submit"]');
const chatBox = document.querySelector(".chat-box");
const fileInput = form.querySelector("#media-input");

const emojiBtn = document.getElementById("emoji-btn");
const emojiPicker = document.getElementById("emoji-picker");

// Prévisualisation temporaire
let sendingPreview = null;

// Envoi du formulaire
form.addEventListener("submit", (e) => {
  e.preventDefault();

  const messageText = inputField.value.trim();
  const file = fileInput.files[0];

  if (!messageText && !file) {
    console.warn("Aucun message ni fichier à envoyer.");
    return;
  }

  let formData = new FormData();
  formData.append("incoming_id", incomingId);
  formData.append("message", messageText);
  if (file) {
    formData.append("file", file);
  }

  fetch("php/insert-chat.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      console.log("Réponse reçue :", data);

      if (data.success) {
        if (data.type === "image") {
          chatBox.innerHTML += `
            <div class="chat outgoing">
              <div class="details">
                <img src="${data.url}" style="max-width: 200px; border-radius: 10px;">
              </div>
            </div>`;
        } else if (data.type === "video") {
          chatBox.innerHTML += `
            <div class="chat outgoing">
              <div class="details">
                <video src="${data.url}" controls style="max-width: 200px; border-radius: 10px;"></video>
              </div>
            </div>`;
        } else if (data.type === "file") {
          chatBox.innerHTML += `
            <div class="chat outgoing">
              <div class="details">
                <a href="${data.url}" target="_blank">📄 Fichier</a>
              </div>
            </div>`;
        } else {
          chatBox.innerHTML += `
            <div class="chat outgoing">
              <div class="details">
                <p>${data.message}</p>
              </div>
            </div>`;
        }

        // Réinitialiser champs
        inputField.value = "";
        fileInput.value = "";
        chatBox.scrollTop = chatBox.scrollHeight;
      } else {
        console.error("Échec lors de l'envoi :", data.error || "Aucune erreur précisée.");
      }
    })
    .catch((err) => {
      console.error("Erreur lors de l'envoi :", err);
    });
});


// Gestion du bouton emoji
emojiBtn.onclick = (e) => {
  e.preventDefault();
  emojiPicker.style.display = emojiPicker.style.display === "block" ? "none" : "block";
};

emojiPicker.onclick = (e) => {
  if (e.target.classList.contains("emoji")) {
    inputField.value += e.target.textContent;
    emojiPicker.style.display = "none";
    inputField.focus();

    // On attend un petit délai pour laisser le champ se mettre à jour
    setTimeout(() => {
      form.requestSubmit();
    }, 100);
  }
};

document.addEventListener("click", (e) => {
  if (!emojiPicker.contains(e.target) && e.target !== emojiBtn) {
    emojiPicker.style.display = "none";
  }
});

// Gérer l'activation du bouton
inputField.onkeyup = () => {
  sendBtn.classList.toggle("active", inputField.value.trim() !== "");
};

// Scroll automatique
function scrollToBottom() {
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Empêche le scroll automatique quand la souris est sur le chat
chatBox.onmouseenter = () => chatBox.classList.add("active");
chatBox.onmouseleave = () => chatBox.classList.remove("active");

// Rafraîchissement du chat
setInterval(() => {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "php/get-chat.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onload = () => {
    if (xhr.status === 200) {
      chatBox.innerHTML = xhr.response;
      if (!chatBox.classList.contains("active")) {
        scrollToBottom();
      }
    }
  };

  xhr.send("incoming_id=" + incomingId);
}, 500);
