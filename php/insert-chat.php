<?php
session_start();
include_once "config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Session invalide ou ID de destinataire manquant"
    ]);
    exit();
}

$outgoing_id = $_SESSION['unique_id'];
$incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id'] ?? '');
$message = isset($_POST['message']) ? trim(mysqli_real_escape_string($conn, $_POST['message'])) : "";

$file_uploaded = false;
$file_type = null;
$file_url = null;

// Gérer les fichiers joints
if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type_mime = mime_content_type($file_tmp);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'avi'];

    if (in_array($file_ext, $allowed)) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $new_name = time() . "_" . basename($file_name);
        $target_path = $upload_dir . $new_name;

        if (move_uploaded_file($file_tmp, $target_path)) {
            $file_uploaded = true;
            $file_url = "php/" . $target_path;
            $file_type = str_contains($file_type_mime, "image") ? "image" :
                         (str_contains($file_type_mime, "video") ? "video" : "file");

            // Si aucun message texte n'est fourni, on stocke juste le fichier
            if (empty($message)) {
                $message = $file_url;
            }
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Échec du téléchargement du fichier"
            ]);
            exit();
        }
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Type de fichier non autorisé"
        ]);
        exit();
    }
}

// Si on a un message ou un fichier à enregistrer
if (!empty($message) || $file_uploaded) {
    if ($file_uploaded) {
        $stmt = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, msg_type, file_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $incoming_id, $outgoing_id, $message, $file_type, $file_url);
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $incoming_id, $outgoing_id, $message);
    }

    $stmt->execute();
    $stmt->close();

    echo json_encode([
        "success" => true,
        "type" => $file_type,
        "url" => $file_url,
        "message" => $message
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Aucun message ou fichier détecté",
        "debug" => [
            "message" => $message,
            "file_uploaded" => $file_uploaded,
            "file_error" => $_FILES['file']['error'] ?? 'non défini',
            "file_tmp" => $_FILES['file']['tmp_name'] ?? 'non défini',
            "FILES" => $_FILES
        ]
    ]);
}
