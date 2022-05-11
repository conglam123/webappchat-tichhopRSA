<?php
session_start();
include_once "php/config.php";
include_once "./RSA/RSA.php";
if (!isset($_SESSION['unique_id'])) {
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
        if (mysqli_num_rows($sql) > 0) {
          $row = mysqli_fetch_assoc($sql);
        } else {
          header("location: users.php");
        }
        $p=new RSA();
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="php/images/<?php echo $p->decrypt($row['img']); ?>" alt="">
        <div class="details">
        <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
          <p><?php echo $row['status']; ?></p>
        </div>
      </header>
        <div id="container-image">
        <img id="output">
        </div>
      <div class="chat-box">
        
      </div>
      <form action="#" class="typing-area" enctype="multipart/form-data">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
        <input id="theFile" name="image" type="file" style="display: none;" onchange="getData(event)" accept="image/x-png,image/gif,image/jpeg,image/jpg" required/>
        <div class="btn" onclick="document.getElementById('theFile').click();">
          <i class="fas fa-images"></i>
        </div>
        <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button class="send-message">
          <i class="fab fa-telegram-plane"></i>
        </button>
      </form>
    </section>
  </div>
  <script src="javascript/chat.js"></script>
</body>

</html>