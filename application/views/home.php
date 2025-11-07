<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messenger</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
body {
  background: #f0f2f5;
  font-family: 'Poppins', sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
}
.chat-container {
  width: 60%;
  height: 80vh;
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.chat-header {
  background: #007bff;
  color: white;
  padding: 15px;
  font-size: 18px;
  font-weight: 600;
  text-align: center;
}
.chat-body {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}
.message {
  padding: 10px 14px;
  border-radius: 15px;
  margin-bottom: 10px;
  max-width: 70%;
  word-wrap: break-word;
}
.sent {
  background: #007bff;
  color: white;
  align-self: flex-end;
}
.received {
  background: #e4e6eb;
  color: #111;
  align-self: flex-start;
}
.chat-footer {
  display: flex;
  border-top: 1px solid #ddd;
  padding: 10px;
}
.chat-footer input {
  flex: 1;
  border: none;
  padding: 10px;
  border-radius: 20px;
  background: #f1f1f1;
  outline: none;
}
.chat-footer button {
  border: none;
  background: #007bff;
  color: white;
  border-radius: 20px;
  padding: 10px 20px;
  margin-left: 10px;
}

/* Floating popup (toast) */
#msgPopup {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: #007bff;
  color: #fff;
  padding: 14px 18px;
  border-radius: 12px;
  display: none;
  font-weight: 500;
  max-width: 300px;
  z-index: 9999;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  animation: fadeUp 0.4s ease;
}
@keyframes fadeUp {
  from { transform: translateY(60px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
</style>
</head>

<body>
<div class="chat-container">
  <div class="chat-header">
    Welcome, <span><?= $user_name; ?></span> 👋
  </div>

  <div class="chat-body" id="chat-body">
    <p class="text-center text-muted">Select a user to start chatting</p>
  </div>

  <div class="chat-footer">
    <select id="receiver_id" class="form-select" style="width:150px;margin-right:10px;">
      <option value="">Select User</option>
      <?php foreach ($users as $u): ?>
        <option value="<?= $u->id; ?>"><?= $u->full_name; ?></option>
      <?php endforeach; ?>
    </select>
    <input type="text" id="message" placeholder="Type your message...">
    <button id="sendBtn">Send</button>
  </div>
</div>

<!-- Popup + sound -->
<div id="msgPopup"></div>
<audio id="msgSound" src="<?= base_url('assets/mixkit-confirmation-tone-2867.wav'); ?>" preload="auto"></audio>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let userId = <?= $this->session->userdata('user_id'); ?>;
let activeReceiverId = null;
let lastMsgId = 0;
const bc = new BroadcastChannel('chat_channel');

// 🔹 Ask Notification permission
if (Notification.permission !== "granted") Notification.requestPermission();

// 🔹 Register service worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register("<?= base_url('service-worker.js'); ?>");
}

// 🔹 Helper functions
function playSound() {
  document.getElementById("msgSound").play().catch(()=>{});
}
function showPopup(sender, msg) {
  let box = document.getElementById("msgPopup");
  box.innerHTML = `<b>${sender}:</b> ${msg}`;
  box.style.display = "block";
  setTimeout(()=>box.style.display="none", 4000);
}
function systemNotify(sender, msg) {
  if (Notification.permission === "granted") {
    new Notification(`${sender} sent a message`, {
      body: msg,
      icon: "https://cdn-icons-png.flaticon.com/512/733/733585.png"
    });
  }
}
function appendMessage(msg, type) {
  $("#chat-body").append(`<div class="message ${type}">${msg}</div>`);
  $("#chat-body").scrollTop($("#chat-body")[0].scrollHeight);
}

// 🔹 Send message
$("#sendBtn").click(function() {
  let msg = $("#message").val().trim();
  let rid = $("#receiver_id").val();
  if (!rid || !msg) return;
  $.post("<?= base_url('welcome/send_message'); ?>", {receiver_id: rid, message: msg}, function() {
    appendMessage(msg, "sent");
    $("#message").val("");
  }, 'json');
});

// 🔹 Load messages
$("#receiver_id").change(function() {
  activeReceiverId = $(this).val();
  if (!activeReceiverId) return;
  $.getJSON("<?= base_url('welcome/get_messages'); ?>?receiver_id="+activeReceiverId, function(data){
    $("#chat-body").html("");
    data.forEach(m=>{
      appendMessage(m.message, m.sender_id == userId ? 'sent' : 'received');
    });
    if (data.length) lastMsgId = data[data.length-1].id;
  });
});

// 🔹 Auto update every 2s
setInterval(()=>{
  if (!activeReceiverId) return;
  $.getJSON("<?= base_url('welcome/get_messages'); ?>?receiver_id="+activeReceiverId, function(data){
    if (!data.length) return;
    let latest = data[data.length-1];
    if (latest.id > lastMsgId) {
      let newMsgs = data.filter(m=>m.id>lastMsgId);
      newMsgs.forEach(m=>{
        const type = m.sender_id==userId?'sent':'received';
        appendMessage(m.message, type);
        if (type==='received') {
          playSound();
          showPopup(m.sender_name, m.message);
          systemNotify(m.sender_name, m.message);
          bc.postMessage({sender: m.sender_name, message: m.message});
          localStorage.setItem("msg_notify", JSON.stringify({sender:m.sender_name,message:m.message,time:Date.now()}));
        }
      });
      lastMsgId = latest.id;
    }
  });
},2000);

// 🔹 Other tab listener
bc.onmessage = e => {
  let d = e.data;
  playSound();
  showPopup(d.sender, d.message);
  systemNotify(d.sender, d.message);
};
window.addEventListener("storage", e=>{
  if (e.key==="msg_notify" && e.newValue) {
    let d = JSON.parse(e.newValue);
    playSound();
    showPopup(d.sender, d.message);
    systemNotify(d.sender, d.message);
  }
});
</script>

<script>
// Subscribe to push notifications
async function subscribeToPush() {
  if ('serviceWorker' in navigator && 'PushManager' in window) {
    const registration = await navigator.serviceWorker.ready;
    
    const subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array('YOUR_PUBLIC_VAPID_KEY')
    });

    // Send subscription to server
    await fetch('<?= base_url("welcome/save_subscription"); ?>', {
      method: 'POST',
      body: JSON.stringify(subscription),
      headers: { 'Content-Type': 'application/json' }
    });
  }
}

// VAPID public key helper
function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
  const rawData = window.atob(base64);
  return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
}

// Call this after login
window.onload = () => {
  if (Notification.permission === 'granted') {
    subscribeToPush();
    
    // Register background sync
    navigator.serviceWorker.ready.then(reg => {
      reg.periodicSync.register('check-new-messages', {
        minInterval: 20 * 1000 // 20 seconds
      }).catch(() => console.log('Periodic sync not supported'));
    });
  }
}
</script>
</body>
</html>
