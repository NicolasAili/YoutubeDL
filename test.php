<?php
error_log('-----------------------' . "\n");

// Generate a unique session ID
$sessionId = session_id() . '_' . uniqid();
$directoryPath = $sessionId;

// Create the directory for the session
if (mkdir($sessionId, 0777, true)) {
    error_log('Directory created: ' . $sessionId . PHP_EOL);
} else {
    error_log('Failed to create directory: ' . $sessionId . PHP_EOL);
}

chmod($sessionId, 0777);

// Construct the download command
$playlistUrl = 'https://www.youtube.com/watch?v=8p5X3C4jVHg&list=PL68pzcNEdQh_28vxrr7wXBVO7PKqCplJI';
$downloadCommand = "cd $sessionId && yt-dlp --no-warnings --print after_move:filepath,ext -x $playlistUrl > output.txt 2>&1 || touch output.txt & echo $!";

// Execute the command and capture output and errors
$output = [];
$return_var = 0;
exec($downloadCommand, $output, $return_var);

// Log the entire output for debugging
error_log('Output from yt-dlp command: ' . implode("\n", $output) . PHP_EOL);

// Check for errors
if ($return_var !== 0) {
    error_log('Error executing command: ' . implode("\n", $output) . PHP_EOL);
} else {
    // Command executed successfully, log PID
    $pid = isset($output[0]) ? $output[0] : null;
    error_log('PID of the background process: ' . $pid . PHP_EOL);
}

?>
