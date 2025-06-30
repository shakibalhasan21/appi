<?php
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $filepath = "server/" . $file; // Adjust this path

    // Check if file exists
    if (file_exists($filepath)) {
        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        // Clear output buffer
        ob_clean();
        flush();

        // Send the file to the browser
        readfile($filepath);
        exit;
    } else {
        echo "File not found!";
    }
} else {
    echo "No file specified!";
}
?>
