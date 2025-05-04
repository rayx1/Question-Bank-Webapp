<?php
// download.php
include 'config.php';

if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $filepath = "uploads/" . $file;

    if (file_exists($filepath)) {
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $file . "\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($filepath));
        flush();
        readfile($filepath);
        exit;
    } else {
        echo "File does not exist.";
    }
} else {
    echo "No file specified.";
}
?>
