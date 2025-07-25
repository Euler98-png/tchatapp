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
            ORDER BY msg_id ASC";

    $query = mysqli_query($conn, $sql);

    if(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            $msgType = $row['msg_type'];
            $filePath = htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8');
            
            // Message content selon type
            if($msgType === 'image'){
                $content = '<img src="'.$filePath.'" alt="image reçue" class="chat-image">';

            } elseif($msgType === 'video'){
                $content = '<video controls ">
                                <source src="'.$filePath.'" type="video/mp4">
                                Votre navigateur ne supporte pas la vidéo.
                            </video>';
            } elseif($msgType === 'file'){
                $filename = basename($filePath);
                $content = '<a href="'.$filePath.'" download>
                                📎 '.$filename.'
                            </a>';
            } else {
                $content = '<p>'. nl2br(htmlspecialchars($row['msg'])) .'</p>';
            }

            // Affichage dans la bonne direction
            if($row['outgoing_msg_id'] === $outgoing_id){
                $output .= '<div class="chat outgoing">
                                <div class="details">'.$content.'</div>
                            </div>';
            } else {
                $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="avatar" class="avatar"   >
                                <div class="details">'.$content.'</div>
                                
                            </div>';
            }
        }
    } else {
        $output .= '<div class="text">Aucun message pour le moment.</div>';
    }

    echo $output;
} else {
    header("location: ../login.php");
}
?>
