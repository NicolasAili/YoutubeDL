<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log("starting file", 3, "cleanup_log.txt");
error_log("debugging");


function writeToLog($message)
{
    $logFile = '/var/www/html/YoutubeDL/cleanup_log.txt';
    $timestamp = date('[Y-m-d H:i:s]');
    $logMessage = "$timestamp $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

$logFile = '/var/www/html/YoutubeDL/session_log.txt';
$lines = file($logFile, FILE_IGNORE_NEW_LINES);
$currentTime = time();
$expireTime = 60; // 5 minutes in seconds

writeToLog("Cleanup script started.");

foreach ($lines as $line) {
    list($sessionId, $creationTime) = explode(' ', $line);
    $timeRemaining = $currentTime - $creationTime;
    writeToLog("session ID : $sessionId");
    writeToLog("time remaining = $timeRemaining");


    if (($currentTime - $creationTime) > $expireTime) {
        $sessionDir = "/var/www/html/YoutubeDL/$sessionId";
        $tarFile = "/var/www/html/YoutubeDL/$sessionId.tar";

        // Delete session directory
        if (is_dir($sessionDir)) {
            array_map('unlink', glob("$sessionDir/*"));
            if (rmdir($sessionDir)) {
                writeToLog("Deleted session directory: $sessionDir");
            } else {
                writeToLog("Failed to delete session directory: $sessionDir");
            }
        }

        // Delete tar file if it exists
        if (file_exists($tarFile)) {
            if (unlink($tarFile)) {
                writeToLog("Deleted tar file: $tarFile");
            } else {
                writeToLog("Failed to delete tar file: $tarFile");
            }
        }

        // Remove line from log file
        /*$lines = array_filter($lines, function($line) use ($sessionId) {
            return !str_starts_with($line, $sessionId);
        });

        writeToLog("Removed session entry from log file for session: $sessionId");*/
    }
}

// Save the updated log file
/*if (file_put_contents($logFile, implode("\n", $lines))) {
    writeToLog("Updated session log file.");
} else {
    writeToLog("Failed to update session log file.");
}*/

writeToLog("----------------------------------------------------");

