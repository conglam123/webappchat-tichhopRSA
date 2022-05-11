<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    include_once  '../RSA/RSA.php';
    $p = new RSA();
    $message = $_POST['message'];
    $message = mysqli_real_escape_string($conn, $message);
        if (isset($_FILES['image'])) {
            $size = $_FILES['image']['size'];
            if ($size == 0) {
                $tmp_message=$message;
                if (trim($tmp_message) != '') {
                    $message = $p->encrypt($message);
                    $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg)
                VALUES ({$incoming_id}, {$outgoing_id}, '{$message}')") or die();
                }
            } else {
                $img_name = $_FILES['image']['name'];
                $img_type = $_FILES['image']['type'];
                $tmp_name = $_FILES['image']['tmp_name'];
                $img_explode = explode('.', $img_name);
                $img_ext = end($img_explode);
                $extensions = ["jpeg", "png", "jpg"];
                if (in_array($img_ext, $extensions) === true) {
                    $types = ["image/jpeg", "image/jpg", "image/png"];
                    if (in_array($img_type, $types) === true) {
                        $time = time();
                        $new_img_name = $time . $img_name;
                        if (move_uploaded_file($tmp_name, "../uploads/" . $new_img_name)){
                            $path = "../uploads/" . $new_img_name;
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $data = file_get_contents($path);
                            file_put_contents($path, base64_encode($data));
                            $message = $p->encrypt($message);
                            $new_img_name=$p->encrypt($new_img_name);
                            // $result = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg,image)
                                                            VALUES ({$incoming_id}, {$outgoing_id}, '{$message}','{$new_img_name}')") or die();
                        }
                    }
                }
            }
    }
} else {
    header("location: ../login.php");
}
