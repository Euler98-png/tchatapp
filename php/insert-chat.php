<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        $msg_type = 'text'; 
        $file_path = NULL;

        if(isset($_FILES['media']) && $_FILES['media']['name'] !== ""){
            $filename = $_FILES['media']['name'];
            $tmp_name = $_FILES['media']['tmp_name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowed_image = ['jpg', 'jpeg', 'png', 'gif'];
            $allowed_video = ['mp4', 'mov', 'avi', 'webm'];
            $upload_dir = "../uploads/";
            if(!is_dir($upload_dir)) mkdir($upload_dir);

            $new_filename = uniqid() . "." . $file_ext;
            $destination = $upload_dir . $new_filename;

            if(in_array($file_ext, $allowed_image)){
                $msg_type = "image";
            } elseif(in_array($file_ext, $allowed_video)){
                $msg_type = "video";
            }

            if(move_uploaded_file($tmp_name, $destination)){
                $file_path = "uploads/" . $new_filename;
            }
        }

        if(!empty($message) || !empty($file_path)){
            $sql_query = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, msg_type, file_path)
            VALUES ('{$incoming_id}', '{$outgoing_id}', '{$message}', '{$msg_type}', '{$file_path}')";

            if (!mysqli_query($conn, $sql_query)) {
                echo "Erreur SQL : " . mysqli_error($conn);
            }
        }

    }else{
        header("location: ../login.php");
    }
?>
