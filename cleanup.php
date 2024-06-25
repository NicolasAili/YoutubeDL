<?php
require_once 'db_connection.php';

function writeToLog($message) {
    $logFile = '/var/www/html/YoutubeDL/cleanup_log.txt';
    $timestamp = date('[Y-m-d H:i:s]');
    $logMessage = "$timestamp $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        writeToLog("Not a directory: $dir");
        return false;
    }

    $items = array_diff(scandir($dir), array('.', '..'));
    foreach ($items as $item) {
        $path = "$dir/$item";
        if (is_dir($path)) {
            if (!deleteDirectory($path)) {
                writeToLog("Failed to delete sub-directory: $path");
                return false;
            }
        } else {
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            writeToLog("File permissions for $path: $perms");
            
            // Attempt to change file permissions if needed
            if (!is_writable($path)) {
                writeToLog("Attempting to change permissions for $path");
                if (!chmod($path, 0666)) {
                    writeToLog("Failed to change permissions for $path");
                    continue;
                }
            }
            
            // Attempt to change ownership if needed
            $fileOwner = fileowner($path);
            $scriptOwner = posix_getuid();
            if ($fileOwner !== $scriptOwner) {
                writeToLog("Attempting to change ownership for $path");
                if (!chown($path, $scriptOwner)) {
                    writeToLog("Failed to change ownership for $path");
                    continue;
                }
            }
            
            // Attempt to delete the file
            if (!unlink($path)) {
                writeToLog("Failed to delete file: $path. Error: " . error_get_last()['message']);
                return false;
            }
        }
    }

    if (!rmdir($dir)) {
        writeToLog("Failed to remove directory: $dir. Error: " . error_get_last()['message']);
        return false;
    }
    return true;
}

$currentTime = time();
$expireTime = 5400; // 90 minutes in seconds

writeToLog("Cleanup script started.");

try {
    $stmt = $pdo->query("SELECT session_id, creation_time FROM session_logs");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($sessions as $session) {
        $sessionId = $session['session_id'];
        $creationTime = $session['creation_time'];
        $sessionDir = "/var/www/html/YoutubeDL/$sessionId";
        writeToLog("Processing session directory: $sessionDir");
        writeToLog("Time difference: " . ($currentTime - $creationTime));

        if (($currentTime - $creationTime) > $expireTime) {
            $tarFile = "/var/www/html/YoutubeDL/$sessionId.tar";

            // Delete session directory
            if (is_dir($sessionDir)) {
                if (deleteDirectory($sessionDir)) {
                    writeToLog("Deleted session directory: $sessionDir");
                } else {
                    writeToLog("Failed to delete session directory: $sessionDir");
                }
            } else {
                writeToLog("Session directory does not exist: $sessionDir");
            }

            // Delete tar file if it exists
            if (file_exists($tarFile)) {
                if (unlink($tarFile)) {
                    writeToLog("Deleted tar file: $tarFile");
                } else {
                    writeToLog("Failed to delete tar file: $tarFile. Error: " . error_get_last()['message']);
                }
            } else {
                writeToLog("Tar file does not exist: $tarFile");
            }

            // Remove entry from the database
            $deleteStmt = $pdo->prepare("DELETE FROM session_logs WHERE session_id = :session_id");
            $deleteStmt->bindParam(':session_id', $sessionId);
            if ($deleteStmt->execute()) {
                writeToLog("Removed session entry from database for session: $sessionId");
            } else {
                writeToLog("Failed to remove session entry from database for session: $sessionId");
            }
        }
    }

    writeToLog("Cleanup script completed.");

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    writeToLog("Database error: " . $e->getMessage());
}
?>
