<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Messenger</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .chat-wrapper {
      width: 95%;
      max-width: 1400px;
      height: 90vh;
      display: flex;
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      border: 1px solid #ddd;
    }

    /* Sidebar */
    .sidebar {
      width: 380px;
      background: #ffffff;
      border-right: 1px solid #e4e6eb;
      display: flex;
      flex-direction: column;
    }

    .sidebar-top {
      padding: 16px 20px;
      background: #0866ff;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .sidebar-top .title {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 20px;
      font-weight: 600;
    }

    .sidebar-top .greeting {
      font-size: 13px;
      opacity: 0.9;
    }

    .search-box {
      padding: 10px 16px;
      background: #f0f2f5;
    }

    .search-box input {
      width: 100%;
      padding: 10px 16px;
      border: none;
      border-radius: 25px;
      background: #ffffff;
      font-size: 15px;
      outline: none;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    #userList {
      flex: 1;
      overflow-y: auto;
      position: relative;
    }

    .sidebar .user {
      padding: 12px 16px;
      cursor: pointer;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: background 0.2s;
      position: relative;
    }

    .sidebar .user:hover {
      background: #f5f6f8;
    }

    .sidebar .user.active {
      background: #e7f3ff;
      border-left: 4px solid #0866ff;
    }

    .user img {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ddd;
    }

    .online-dot {
      width: 12px;
      height: 12px;
      background: #31a24c;
      border: 2px solid white;
      border-radius: 50%;
      position: absolute;
      bottom: 8px;
      left: 48px;
    }

    .offline-dot {
      background: #95a5a6;
      border: 2px solid #ddd;
    }

    .unread-badge {
      position: absolute;
      top: 12px;
      right: 16px;
      background: #e41e3f;
      color: white;
      font-size: 10px;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.3);
      }

      100% {
        transform: scale(1);
      }
    }

    /* Chat area */
    .chat-container {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: #f0f2f5;
    }

    .chat-header {
      background: #0866ff;
      color: white;
      padding: 10px 20px;
      font-weight: 600;
      font-size: 17px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .chat-header .user-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .chat-header img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }

    .default-header {
      text-align: center;
      color: #65676b;
      font-size: 18px;
      padding: 30px 20px;
    }

    .chat-body {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      background: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png') repeat;
      background-size: 400px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .message {
      padding: 10px 16px;
      border-radius: 18px;
      max-width: 65%;
      word-wrap: break-word;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
      position: relative;
      animation: msgFade 0.3s ease;
    }

    .message.sent {
      background: #0866ff;
      color: white;
      align-self: flex-end;
      border-bottom-right-radius: 4px;
    }

    .message.received {
      background: #ffffff;
      color: #1c1e21;
      align-self: flex-start;
      border-bottom-left-radius: 4px;
    }

    .message-time {
      font-size: 11px;
      opacity: 0.8;
      margin-top: 4px;
      text-align: right;
    }

    .download-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      margin-top: 8px;
      cursor: pointer;
    }

    .download-btn:hover {
      background: rgba(255, 255, 255, 0.5);
    }

    .download-btn i {
      font-size: 18px;
      color: white;
    }

    audio {
      width: 300px !important;
      height: 42px;
      border-radius: 12px;
    }

    /* Footer */
    .chat-footer {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      background: white;
      border-top: 1px solid #ddd;
      gap: 10px;
    }

    .attach-btn,
    .like-btn,
    #sendBtn {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 22px;
      transition: 0.2s;
      background: #e4e6eb;
      color: #65676b;
    }

    .attach-btn {
      color: #0866ff;
      font-size: 24px;
    }

    .attach-btn:hover,
    .like-btn:hover {
      background: #d8dadf;
    }

    #sendBtn {
      background: #0866ff !important;
      color: white !important;
      border: none !important;
    }

    .chat-footer input[type=text] {
      flex: 1;
      padding: 12px 18px;
      border: 1px solid #ddd;
      border-radius: 30px;
      outline: none;
      font-size: 15px;
      background: #f0f2f5;
      transition: 0.2s;
    }

    .chat-footer input[type=text]:focus {
      background: white;
      border-color: #0866ff;
    }

    #msgPopup {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #0866ff;
      color: white;
      padding: 16px 24px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(8, 102, 255, 0.4);
      z-index: 9999;
      font-weight: 500;
      max-width: 320px;
      animation: slideUp 0.4s ease, fadeOut 0.4s ease 3.5s forwards;
      display: none;
    }

    @keyframes slideUp {
      from {
        transform: translateY(100px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: translateY(20px);
      }
    }

    @keyframes msgFade {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .chat-body::-webkit-scrollbar,
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .chat-body::-webkit-scrollbar-thumb,
    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(0, 0, 0, 0.2);
      border-radius: 3px;
    }

    @media (max-width: 768px) {
      .chat-wrapper {
        flex-direction: column;
        height: 100vh;
        border-radius: 0;
      }

      .sidebar {
        width: 100%;
        height: 180px;
        border-bottom: 1px solid #ddd;
      }

      #userList {
        display: flex;
        overflow-x: auto;
        padding: 10px;
      }

      .sidebar .user {
        flex-direction: column;
        min-width: 90px;
        text-align: center;
      }

      .user img {
        width: 50px;
        height: 50px;
      }

      audio {
        width: 300px !important;
      }
    }
  </style>
  <script>
// Page load hote hi permission maang lo
if ("Notification" in window && Notification.permission === "default") {
  Notification.requestPermission();
}
</script>
</head>

<body>
  <div class="chat-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-top">
        <div class="title">
          <i class="fas fa-comment-dots"></i>
          <div>
            <div>Messenger</div>
            <div class="greeting">Hi, <strong><?= $this->session->userdata('user_name') ?></strong></div>
          </div>
        </div>
        <a href="<?= base_url('auth/logout') ?>" style="color:white; opacity:0.8;">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </div>
      <div class="search-box">
        <input type="text" placeholder="Search Here...">
      </div>
      <div id="userList">
        <?php foreach ($users as $u) :
          $is_online = $this->User_model->is_online($u->id);
        ?>
          <div class="user" data-id="<?= $u->id ?>">
            <div style="position:relative;">
              <img src="https://img.freepik.com/premium-vector/vector-flat-illustration-grayscale-avatar-user-profile-person-icon-gender-neutral-silhouette-profile-picture-suitable-social-media-profiles-icons-screensavers-as-templatex9xa_719432-2210.jpg?w=740" alt="avatar">
              <div class="online-dot <?= $is_online ? '' : 'offline-dot' ?>"></div>
            </div>
            <div class="user-info" style="flex:1; position:relative;">
              <div class="user-name"><?= $u->full_name ?></div>
              <div class="user-status">
                <?= $u->department ?>
                <small style="color:<?= $is_online ? '#31a24c' : '#95a5a6' ?>;">
                  <?= $is_online ? 'Online' : 'Offline' ?>
                </small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Chat area -->
    <div class="chat-container">
      <div class="chat-header" id="chatHeader">
        <div class="default-header">
          <!-- <i class="fas fa-comment-medical fa-3x mb-3" style="color:#0866ff;"></i><br>
          <strong>Select a user to start chatting</strong> -->
        </div>
      </div>
      <div class="chat-body" id="chatBody"></div>

      <div class="chat-footer">
        <label for="fileInput" class="attach-btn">
          <i class="fas fa-plus-circle"></i>
        </label>
        <input type="file" id="fileInput" style="display:none;" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">

        <input type="text" id="message" placeholder="Type a message..." autocomplete="off">

        <button id="sendBtn" style="display:none;">
          <i class="fas fa-paper-plane"></i>
        </button>

        <button id="likeBtn" class="like-btn">
          <i class="far fa-thumbs-up"></i>
        </button>
      </div>
    </div>
  </div>

  <div id="msgPopup"></div>
  <audio id="msgSound" src="<?= base_url('assets/mixkit-confirmation-tone-2867.wav'); ?>" preload="auto"></audio>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let userId = <?= $this->session->userdata('user_id'); ?>;
    let activeReceiverId = null;
    let cachedMessages = {};

    function playSound() {
      document.getElementById("msgSound").play().catch(() => {});
    }

    function showPopup(sender, msg) {
      $("#msgPopup").stop(true, true)
        .html(`<b>${sender}:</b> ${msg.substring(0,50)}${msg.length>50?'...':''}`)
        .fadeIn();
      setTimeout(() => $("#msgPopup").fadeOut(), 3500);
    }

    function appendFileMessage(fileUrl, type) {
      const fileName = fileUrl.split('/').pop();
      let fileHtml = '';
      let icon = '';
      let bgColor = type === 'sent' ? 'rgba(255,255,255,0.2)' : '#f0f0f0';
      let textColor = type === 'sent' ? 'white' : '#1c1e21';

      if (fileUrl.match(/\.(jpg|jpeg|png|gif)$/i)) {
        fileHtml = `<img src="${fileUrl}" class="img-fluid rounded" style="max-width:260px; border-radius:12px;">
                    <div class="download-btn"><a href="${fileUrl}" download><i class="fas fa-download"></i></a></div>`;
      } else if (fileUrl.match(/\.(mp4|webm|ogg)$/i)) {
        fileHtml = `<video src="${fileUrl}" controls style="max-width:300px; border-radius:12px;"></video>
                    <div class="download-btn"><a href="${fileUrl}" download><i class="fas fa-download"></i></a></div>`;
      } else if (fileUrl.match(/\.(mp3|wav)$/i)) {
        fileHtml = `<audio controls src="${fileUrl}" style="width:380px;"></audio>
                    <div class="download-btn"><a href="${fileUrl}" download><i class="fas fa-download"></i></a></div>`;
      } else {
        if (fileUrl.match(/\.(xlsx?|xls)$/i)) icon = '<i class="fas fa-file-excel fa-3x" style="color:#1e7e34;"></i>';
        else if (fileUrl.match(/\.(doc|docx)$/i)) icon = '<i class="fas fa-file-word fa-3x" style="color:#1b5faa;"></i>';
        else if (fileUrl.match(/\.(pdf)$/i)) icon = '<i class="fas fa-file-pdf fa-3x" style="color:#e02d2d;"></i>';
        else if (fileUrl.match(/\.(ppt|pptx)$/i)) icon = '<i class="fas fa-file-powerpoint fa-3x" style="color:#d24726;"></i>';
        else if (fileUrl.match(/\.(zip|rar|7z)$/i)) icon = '<i class="fas fa-file-archive fa-3x" style="color:#f39c12;"></i>';
        else icon = '<i class="fas fa-file fa-3x" style="color:#95a5a6;"></i>';

        fileHtml = `
          <div style="background:${bgColor}; padding:16px; border-radius:16px; text-align:center; max-width:280px;">
            ${icon}
            <div style="margin:12px 0; font-weight:600; color:${textColor}; font-size:14px; word-break:break-all;">
              ${fileName.length > 30 ? fileName.substring(0,27) + '...' : fileName}
            </div>
            <a href="${fileUrl}" download style="background:rgba(255,255,255,0.3); color:${textColor}; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:13px;">
              Download
            </a>
          </div>
        `;
      }

      $("#chatBody").append(`<div class="message ${type}">${fileHtml}</div>`);
      $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
    }

    function appendMessage(msg, type) {
      if (msg.match(/^https?:\/\/.+\.(jpg|jpeg|png|gif|mp4|webm|ogg|mp3|wav|pdf|docx?|xlsx?|pptx?|zip|rar|7z)$/i)) {
        appendFileMessage(msg, type);
      } else {
        const time = new Date().toLocaleTimeString([], {
          hour: '2-digit',
          minute: '2-digit'
        });
        $("#chatBody").append(`<div class="message ${type}">${msg}<div class="message-time">${time}</div></div>`);
        $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
      }
    }

    function loadMessages(userId) {
      if (cachedMessages[userId]) {
        $("#chatBody").html("");
        cachedMessages[userId].forEach(m => appendMessage(m.message, m.sender_id == userId ? 'sent' : 'received'));
        return;
      }
      $.getJSON("<?= base_url('welcome/get_messages'); ?>?receiver_id=" + userId, function(data) {
        cachedMessages[userId] = data;
        $("#chatBody").html("");
        data.forEach(m => appendMessage(m.message, m.sender_id == <?= $this->session->userdata('user_id'); ?> ? 'sent' : 'received'));
      });
    }

    $(".user").click(function() {
      $(".user").removeClass("active");
      $(this).addClass("active").find('.unread-badge').remove();
      activeReceiverId = $(this).data("id");
      const name = $(this).find(".user-name").text();
      const img = $(this).find('img').attr('src');

      $("#chatHeader").html(`
        <div class="user-info">
          <img src="${img}" alt="">
          <div>
            <div>${name}</div>
            <small style="opacity:0.8;">Active now</small>
          </div>
        </div>
        <div class="actions">
          <i class="fas fa-video me-2"></i>
          <i class="fas fa-phone me-2"></i>
          <i class="fas fa-info-circle"></i>
        </div>
      `);
      loadMessages(activeReceiverId);
    });

    function sendMessage() {
      let msg = $("#message").val().trim();
      if (!activeReceiverId || !msg) return;
      $.post("<?= base_url('welcome/send_message'); ?>", {
        receiver_id: activeReceiverId,
        message: msg
      }, function(res) {
        appendMessage(msg, 'sent');
        $("#message").val("");
        $("#sendBtn").hide();
        $("#likeBtn").show();
      }, 'json');
    }

    $("#message").on("input", function() {
      if ($(this).val().trim()) {
        $("#sendBtn").show();
        $("#likeBtn").hide();
      } else {
        $("#sendBtn").hide();
        $("#likeBtn").show();
      }
    });

    $("#message").keypress(function(e) {
      if (e.which == 13 && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });

    $("#sendBtn").click(sendMessage);

    $("#likeBtn").click(function() {
      if (!activeReceiverId) return alert("Select a user first!");
      $.post("<?= base_url('welcome/send_message'); ?>", {
        receiver_id: activeReceiverId,
        message: "❤️"
      }, function() {
        appendMessage("❤️", 'sent');
      }, 'json');
    });

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
            appendMessage(res.file_url, 'sent');
          } else alert(res.msg);
        }
      });
    });

    // GLOBAL REAL-TIME POLLING — KABHI MISS NHI HOGA!
    setInterval(() => {
      if ($(".user").length === 0) return;

      $(".user").each(function() {
        let receiverId = $(this).data("id");
        if (!receiverId) return;

        $.getJSON("<?= base_url('welcome/get_messages'); ?>?receiver_id=" + receiverId + "&t=" + Date.now(), function(data) {
          if (!cachedMessages[receiverId]) {
            cachedMessages[receiverId] = data;
            return;
          }

          let newMsgs = data.filter(m =>
            !cachedMessages[receiverId].some(c => c.id === m.id)
          );

          if (newMsgs.length > 0) {
            cachedMessages[receiverId] = data;

            newMsgs.forEach(m => {
              if (m.sender_id != userId) {
                showPopup(m.sender_name || 'User', m.message);
                playSound();

                let userElem = $(`.user[data-id="${receiverId}"]`);
                if (!userElem.find('.unread-badge').length && activeReceiverId != receiverId) {
                  userElem.find(".user-info").append('<span class="unread-badge">●</span>');
                }
              }

              if (activeReceiverId == receiverId) {
                appendMessage(m.message, m.sender_id == userId ? 'sent' : 'received');
              }
            });
          }
        });
      });
    }, 2000);

    // Keep user online
    setInterval(() => {
      $.post("<?= base_url('welcome/update_online_status') ?>");
    }, 30000);

    function showRealNotification(name, msg, senderId) {
  if (Notification.permission === "granted" && (document.hidden || !document.hasFocus())) {
    const notif = new Notification(name + " ने message bheja", {
      body: msg.length > 70 ? msg.substr(0,70)+"..." : msg,
      icon: "https://img.freepik.com/premium-vector/vector-flat-illustration-grayscale-avatar-user-profile-person-icon-gender-neutral-silhouette-profile-picture-suitable-social-media-profiles-icons-screensavers-as-templatex9xa_719432-2210.jpg?w=740",
      tag: "msg_"+senderId
    });

    notif.onclick = function() {
      window.focus();
      document.querySelector(`.user[data-id="${senderId}"]`).click();
      notif.close();
    };

    // Sound + Title blink
    document.getElementById("msgSound").play();
    let i = 0;
    let blink = setInterval(() => {
      document.title = i % 2 == 0 ? "("+name+") New Message!" : "Messenger";
      i++;
    }, 800);
    notif.onclose = () => clearInterval(blink);
  }
}
  </script>

  
</body>

</html>