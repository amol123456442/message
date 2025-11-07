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
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
  </style>
</head>

<body>

  <div class="chat-container">
    <div class="chat-header">Welcome, <span><?= $user_name; ?></span> 👋</div>
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
      <input type="file" id="fileInput" style="display:none;">
      <button id="uploadBtn" class="btn btn-secondary">📎</button>
      <input type="text" id="message" placeholder="Type your message...">
      <button id="sendBtn">Send</button>
    </div>
  </div>

  <div id="msgPopup"></div>
  <audio id="msgSound" src="<?= base_url('assets/mixkit-confirmation-tone-2867.wav'); ?>" preload="auto"></audio>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let userId = <?= $this->session->userdata('user_id'); ?>;
    let activeReceiverId = null,
      lastMsgId = 0;
    const bc = new BroadcastChannel('chat_channel');
    if (Notification.permission !== "granted") Notification.requestPermission();

    function playSound() {
      document.getElementById("msgSound").play().catch(() => {});
    }

    function showPopup(sender, msg) {
      $("#msgPopup").html(`<b>${sender}:</b> ${msg}`).fadeIn();
      setTimeout(() => $("#msgPopup").fadeOut(), 4000);
    }

    function systemNotify(sender, msg) {
      if (Notification.permission === "granted") {
        new Notification(`${sender} sent a message`, {
          body: msg,
          icon: "https://cdn-icons-png.flaticon.com/512/733/733585.png"
        });
      }
    }

    function appendFileMessage(fileUrl, type) {
      let fileHtml = "";
      if (fileUrl.match(/\.(jpg|jpeg|png|gif)$/i)) {
        fileHtml = `<img src="${fileUrl}" class="img-fluid rounded mb-2" style="max-width:200px;"><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-light">Download</a>`;
      } else if (fileUrl.match(/\.(mp4|webm|ogg)$/i)) {
        fileHtml = `<video src="${fileUrl}" controls style="max-width:200px;border-radius:10px;"></video><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-light">Download</a>`;
      } else if (fileUrl.match(/\.(mp3|wav)$/i)) {
        fileHtml = `<audio controls src="${fileUrl}"></audio><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-light">Download</a>`;
      } else {
        const fileName = fileUrl.split('/').pop();
        fileHtml = `<a href="${fileUrl}" target="_blank" class="btn btn-outline-light btn-sm">📄 ${fileName}</a><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-light">Download</a>`;
      }
      $("#chat-body").append(`<div class="message ${type}">${fileHtml}</div>`);
      $("#chat-body").scrollTop($("#chat-body")[0].scrollHeight);
    }

    function appendMessage(msg, type) {
      if (msg.match(/^https?:\/\/.+\.(jpg|jpeg|png|gif|mp4|mp3|wav|pdf|docx?|xlsx?|pptx?)$/i)) appendFileMessage(msg, type);
      else $("#chat-body").append(`<div class="message ${type}">${msg}</div>`).scrollTop($("#chat-body")[0].scrollHeight);
    }

    // send text
    $("#sendBtn").click(function() {
      let msg = $("#message").val().trim(),
        rid = $("#receiver_id").val();
      if (!rid || !msg) return;
      $.post("<?= base_url('welcome/send_message'); ?>", {
        receiver_id: rid,
        message: msg
      }, () => {
        appendMessage(msg, "sent");
        $("#message").val("");
      }, 'json');
    });

    // load messages
    $("#receiver_id").change(function() {
      activeReceiverId = $(this).val();
      if (!activeReceiverId) return;
      $.getJSON("<?= base_url('welcome/get_messages'); ?>?receiver_id=" + activeReceiverId, function(data) {
        $("#chat-body").html("");
        data.forEach(m => appendMessage(m.message, m.sender_id == userId ? 'sent' : 'received'));
        if (data.length) lastMsgId = data[data.length - 1].id;
      });
    });

    // auto refresh every 2s
    setInterval(() => {
      if (!activeReceiverId) return;
      $.getJSON("<?= base_url('welcome/get_messages'); ?>?receiver_id=" + activeReceiverId, function(data) {
        if (!data.length) return;
        let latest = data[data.length - 1];
        if (latest.id > lastMsgId) {
          let newMsgs = data.filter(m => m.id > lastMsgId);
          newMsgs.forEach(m => {
            const type = m.sender_id == userId ? 'sent' : 'received';
            appendMessage(m.message, type);
            if (type === 'received') {
              playSound();
              showPopup(m.sender_name, m.message);
              systemNotify(m.sender_name, m.message);
              bc.postMessage({
                sender: m.sender_name,
                message: m.message
              });
              localStorage.setItem("msg_notify", JSON.stringify({
                sender: m.sender_name,
                message: m.message,
                time: Date.now()
              }));
            }
          });
          lastMsgId = latest.id;
        }
      });
    }, 2000);

    // cross-tab
    bc.onmessage = e => {
      let d = e.data;
      playSound();
      showPopup(d.sender, d.message);
      systemNotify(d.sender, d.message);
    };
    window.addEventListener("storage", e => {
      if (e.key === "msg_notify" && e.newValue) {
        let d = JSON.parse(e.newValue);
        playSound();
        showPopup(d.sender, d.message);
        systemNotify(d.sender, d.message);
      }
    });

    // upload file
    $("#uploadBtn").click(() => $("#fileInput").click());
    $("#fileInput").change(function() {
      const file = this.files[0],
        rid = $("#receiver_id").val();
      if (!rid) return alert("Select a user first.");
      if (!file) return;
      let formData = new FormData();
      formData.append('receiver_id', rid);
      formData.append('file', file);
      $.ajax({
        url: "<?= base_url('welcome/send_file_message'); ?>",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            appendFileMessage(res.file_url, 'sent');
          } else {
            alert("Upload failed: " + res.msg);
          }
        }
      });
    });
  </script>
</body>

</html>