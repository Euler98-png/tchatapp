<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
          $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
          if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
          }else{
            header("location: users.php");
          }
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="php/images/<?php echo $row['img']; ?>" alt="">
        <div class="details">
          <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
          <p><?php echo $row['status']; ?></p>
        </div>
      </header>
      <div class="chat-box">

      </div>
      <form action="#" class="typing-area" enctype="multipart/form-data" id="message-form" method="POST">
      <input type="hidden" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>">
      <div class="emoji-container">
        <button type="button" id="emoji-btn">
          <i class="far fa-smile"></i>
        </button>
        <div id="emoji-picker">
          <span class="emoji">😃</span>
          <span class="emoji">😅</span>
          <span class="emoji">😉</span>
          <span class="emoji">🥰</span>
          <span class="emoji">🤔</span>
          <span class="emoji">😡</span>
          <span class="emoji">🎉</span>
          <span class="emoji">🔥</span>
          <span class="emoji">💯</span>
          <span class="emoji">🥳</span>
          <span class="emoji">🤗</span>
          <span class="emoji">😴</span>
          <span class="emoji">🤩</span>
          <span class="emoji">😇</span>
        </div>
      </div>
      <input type="file" name="file" id="media-input" accept="image/*,video/*" style="display:none;">
      <label for="media-input" class="media-btn" style="cursor:pointer;">
        <i class="fas fa-paperclip"></i>
      </label>
      <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
      <button type="submit"><i class="fab fa-telegram-plane"></i></button>
    </form>
    </section>
  </div>

  <script src="javascript/chat.js"></script>

</body>
</html>
