<?php
    function download($filename)
    {
        // Check if the file exists
        if (file_exists($filename)) {
            // Get the basename of the file (without path)
            $basename = basename($filename);
    
            // Turn off output buffering to prevent memory issues
            if (ob_get_level()) {
                ob_end_clean();
            }
    
            // Set the appropriate headers to force download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $basename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
    
            // Ensure headers are sent and flush the system output buffer
            flush();
    
            // Read the file and send it to the output buffer
            error_log(readfile($filename));
    
            // Ensure no further output is sent after the file is downloaded
            exit;
        } else {
            // Log error if file not found
            error_log('File not found: ' . $filename);
            die('File not found');
        }
    }

    // Check if the 'file' parameter exists in the GET request
if (isset($_GET['file'])) {
    $file = htmlspecialchars($_GET['file']);

    download($file);

    // Here, you can add code to handle the file (e.g., initiate a download)
} else {
    echo "<h1>No file received</h1>";
}

?>