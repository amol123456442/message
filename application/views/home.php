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
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .chat-wrapper {
      width: 60%;
      height: 80vh;
      display: flex;
      background: #fff;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #f8f9fa;
      border-right: 1px solid #ddd;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
    }

    .sidebar-top {
      padding: 15px;
      text-align: center;
      font-weight: bold;
      font-size: 18px;
      border-bottom: 1px solid #ddd;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }

    .sidebar-top i {
      font-size: 22px;
      color: #007bff;
    }

    .sidebar .user {
      padding: 10px 15px;
      cursor: pointer;
      border-bottom: 1px solid #e0e0e0;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: background 0.3s, transform 0.2s;
    }

    .sidebar .user:hover {
      background: #e9ecef;
      transform: translateX(3px);
    }

    .sidebar .user.active {
      background: #007bff;
      color: white;
    }

    .user img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }

    /* Chat area */
    .chat-container {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: #e5ddd5;
    }

    .chat-header {
      background: #007bff;
      color: white;
      padding: 15px;
      font-weight: 600;
      font-size: 18px;
      border-bottom: 1px solid #0062cc;
    }

    .chat-body {
      flex: 1;
      padding: 15px;
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
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
      transition: transform 0.2s;
    }

    .message.sent {
      background: #dcf8c6;
      align-self: flex-end;
    }

    .message.received {
      background: #fff;
      align-self: flex-start;
    }

    /* Chat footer */
    .chat-footer {
      display: flex;
      align-items: center;
      padding: 10px 15px;
      border-top: 1px solid #ccc;
      background: #f8f9fa;
    }

    .chat-footer input[type=text] {
      flex: 1;
      padding: 10px 15px;
      border-radius: 25px;
      border: 1px solid #ccc;
      outline: none;
    }

    .chat-footer button,
    .chat-footer label {
      margin-left: 10px;
      border: none;
      border-radius: 25px;
      padding: 10px 15px;
      background: #007bff;
      color: white;
      cursor: pointer;
    }

    .chat-footer input[type=file] {
      display: none;
    }

    /* Notification popup */
    #msgPopup {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #ff00f2ff;
      color: black;
      padding: 12px 18px;
      border-radius: 15px;
      display: none;
      z-index: 9999;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
      font-weight: 500;
      animation: bounceIn 0.4s ease, fadeOut 0.4s ease 3.6s forwards;
      max-width: 280px;
    }

    #msgPopup b {
      display: block;
      margin-bottom: 4px;
    }

    /* Animations */
    @keyframes bounceIn {
      0% {
        opacity: 0;
        transform: translateY(50px) scale(0.8);
      }

      60% {
        opacity: 1;
        transform: translateY(-10px) scale(1.05);
      }

      100% {
        transform: translateY(0) scale(1);
      }
    }

    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: translateY(50px);
      }
    }

    /* Scrollbar style */
    .chat-body::-webkit-scrollbar,
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .chat-body::-webkit-scrollbar-thumb,
    .sidebar::-webkit-scrollbar-thumb {
      background-color: rgba(0, 0, 0, 0.2);
      border-radius: 3px;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .chat-wrapper {
        width: 90%;
      }
    }

    @media (max-width: 768px) {
      .chat-wrapper {
        flex-direction: column;
        height: 95vh;
      }

      .sidebar {
        width: 100%;
        height: 200px;
        flex-direction: row;
        overflow-x: auto;
        border-right: none;
        border-bottom: 1px solid #ddd;
      }

      .chat-container {
        flex: 1;
        height: calc(100% - 200px);
      }

      .sidebar .user {
        flex-direction: column;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
      }
    }

    audio {
      width: 200px !important;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="chat-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-top">
        <i class="fas fa-comment-dots"></i> Chat App
      </div>
      <div id="userList">
        <?php foreach ($users as $u) : ?>
          <div class="user" data-id="<?= $u->id ?>">
            <img src="https://img.freepik.com/premium-vector/vector-flat-illustration-grayscale-avatar-user-profile-person-icon-gender-neutral-silhouette-profile-picture-suitable-social-media-profiles-icons-screensavers-as-templatex9xa_719432-2210.jpg?semt=ais_hybrid&w=740&q=80" alt="avatar">
            <span><?= $u->full_name ?></span>
          </div>
        <?php endforeach; ?>
      </div>

    </div>

    <!-- Chat area -->
    <div class="chat-container">
      <div class="chat-header" id="chatHeader">Select a user to start chatting</div>
      <div class="chat-body" id="chatBody"></div>
      <div class="chat-footer">
        <input type="text" id="message" placeholder="Type a message...">
        <label for="fileInput">📎</label>
        <input type="file" id="fileInput">
        <button id="sendBtn">Send</button>
      </div>
    </div>
  </div>

  <div id="msgPopup"></div>
  <audio id="msgSound" src="<?= base_url('assets/mixkit-confirmation-tone-2867.wav'); ?>" preload="auto"></audio>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let userId = <?= $this->session->userdata('user_id'); ?>;
    let activeReceiverId = null,
      lastMsgId = 0;
    let cachedMessages = {};
    const bc = new BroadcastChannel('chat_channel');

    function playSound() {
      document.getElementById("msgSound").play().catch(() => {});
    }

    function showPopup(sender, msg) {
      $("#msgPopup").stop(true, true).fadeIn().html(`<b>${sender}:</b> ${msg}`).delay(4000).fadeOut();
    }

    function appendMessage(msg, type) {
      $("#chatBody").append(`<div class="message ${type}">${msg}</div>`);
      $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
    }

    function loadMessages(userId) {
      if (cachedMessages[userId]) {
        $("#chatBody").html("");
        cachedMessages[userId].forEach(m => appendMessage(m.message, m.sender_id == <?= $this->session->userdata('user_id'); ?> ? 'sent' : 'received'));
        lastMsgId = cachedMessages[userId].length ? cachedMessages[userId][cachedMessages[userId].length - 1].id : 0;
        return;
      }
      $.getJSON("<?= base_url('welcome/get_messages'); ?>?receiver_id=" + userId, function(data) {
        cachedMessages[userId] = data;
        $("#chatBody").html("");
        data.forEach(m => appendMessage(m.message, m.sender_id == <?= $this->session->userdata('user_id'); ?> ? 'sent' : 'received'));
        lastMsgId = data.length ? data[data.length - 1].id : 0;
      });
    }

    // select user
    $(".user").click(function() {
      $(".user").removeClass("active");
      $(this).addClass("active");
      activeReceiverId = $(this).data("id");
      $("#chatHeader").text($(this).find("span").text());
      loadMessages(activeReceiverId);
    });

    // send message function
    function sendMessage() {
      let msg = $("#message").val().trim();
      if (!activeReceiverId || !msg) return;
      $.post("<?= base_url('welcome/send_message'); ?>", {
        receiver_id: activeReceiverId,
        message: msg
      }, function(res) {
        appendMessage(msg, 'sent');
        $("#message").val("");
        if (!cachedMessages[activeReceiverId]) cachedMessages[activeReceiverId] = [];
        cachedMessages[activeReceiverId].push({
          id: lastMsgId + 1,
          sender_id: userId,
          message: msg
        });
      }, 'json');
    }

    // send on button click or Enter
    $("#sendBtn").click(sendMessage);
    $("#message").keypress(function(e) {
      if (e.which == 13) sendMessage();
    });

    // file upload
    $("#fileInput").change(function() {
      const file = this.files[0];
      if (!file || !activeReceiverId) return alert("Select user first!");
      let formData = new FormData();
      formData.append('receiver_id', activeReceiverId);
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
            appendMessage("📎 File sent", 'sent');
          } else alert(res.msg);
        }
      });
    });

    // auto refresh
    setInterval(() => {
      if (!activeReceiverId) return;
      $.getJSON("<?= base_url('welcome/get_messages'); ?>?receiver_id=" + activeReceiverId, function(data) {
        let newMsgs = data.filter(m => !cachedMessages[activeReceiverId] || !cachedMessages[activeReceiverId].some(c => c.id === m.id));
        if (newMsgs.length) {
          newMsgs.forEach(m => {
            appendMessage(m.message, m.sender_id == userId ? 'sent' : 'received');
            if (m.sender_id != userId) {
              playSound();
              showPopup(m.sender_name, m.message);
            }
          });
          cachedMessages[activeReceiverId] = data;
          lastMsgId = data[data.length - 1].id;
        }
      });
    }, 2000);

    function appendFileMessage(fileUrl, type) {
      let fileHtml = "";
      if (fileUrl.match(/\.(jpg|jpeg|png|gif)$/i)) {
        fileHtml = `<img src="${fileUrl}" class="img-fluid rounded mb-2" style="max-width:200px;"><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary">Download</a>`;
      } else if (fileUrl.match(/\.(mp4|webm|ogg)$/i)) {
        fileHtml = `<video src="${fileUrl}" controls style="max-width:200px;border-radius:10px;"></video><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary">Download</a>`;
      } else if (fileUrl.match(/\.(mp3|wav)$/i)) {
        fileHtml = `<audio controls src="${fileUrl}"></audio><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary">Download</a>`;
      } else if (fileUrl.match(/\.(pdf)$/i)) {
        fileHtml = `<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm">📄 PDF</a><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary">Download</a>`;
      } else if (fileUrl.match(/\.(xls|xlsx)$/i)) {
        fileHtml = `<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm">📊 Excel</a><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary">Download</a>`;
      } else if (fileUrl.match(/\.(doc|docx)$/i)) {
        fileHtml = `<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm">📝 Word</a><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary">Download</a>`;
      } else if (fileUrl.match(/\.(ppt|pptx)$/i)) {
        fileHtml = `<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm">📈 PPT</a><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary">Download</a>`;
      } else {
        const fileName = fileUrl.split('/').pop();
        fileHtml = `<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm">📄 ${fileName}</a><br><a href="${fileUrl}" download class="btn btn-sm btn-outline-secondary">Download</a>`;
      }

      $("#chatBody").append(`<div class="message ${type}">${fileHtml}</div>`);
      $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
    }

    function appendMessage(msg, type) {
      // check if message is a file URL
      if (msg.match(/^https?:\/\/.+\.(jpg|jpeg|png|gif|mp4|webm|ogg|mp3|wav|pdf|docx?|xlsx?|xls|pptx?|ppt)$/i)) {
        appendFileMessage(msg, type);
      } else {
        $("#chatBody").append(`<div class="message ${type}">${msg}</div>`);
        $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
      }
    }

    function showPopup(sender, msg) {
      const popup = $("#msgPopup");
      popup.stop(true, true).html(`<b>${sender}</b>${msg.length > 50 ? '<br>' + msg : ''}`).fadeIn();
      setTimeout(() => popup.fadeOut(), 2000);
    }
  </script>
</body>

</html>