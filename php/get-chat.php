<?php 
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";

    $sql = "SELECT * FROM messages 
            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
            OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) 
            ORDER BY msg_id";

    $query = mysqli_query($conn, $sql);

    if(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            // Déterminer le contenu selon le type de message
            if($row['msg_type'] === 'image'){
                $content = '<img src="'.$row['file_path'].'" alt="image" style="max-width: 220px; border-radius: 10px;">';
            } elseif($row['msg_type'] === 'video'){
                $content = '<video controls style="max-width: 250px; border-radius: 10px;">
                                <source src="'.$row['file_path'].'" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>';
            } else {
                $content = '<p>'. nl2br($row['msg']) .'</p>'; // texte et emojis
            }

            // Générer le bloc de message
            if($row['outgoing_msg_id'] === $outgoing_id){
                $output .= '<div class="chat outgoing">
                                <div class="details">
                                    '.$content.'
                                </div>
                            </div>';
            } else {
                $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    '.$content.'
                                </div>
                            </div>';
            }
        }
    } else {
        $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
    }

    echo $output;
} else {
    header("location: ../login.php");
}
?>
