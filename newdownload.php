<?php
ignore_user_abort(true);
// Set error reporting to show all errors except warnings
error_reporting(E_ALL & ~E_WARNING);

// Display errors and warnings on the screen
ini_set('display_errors', 1);

// Do not log warnings to the Apache log file
ini_set('log_errors', 1);

if (!defined('SIGTERM')) {
    define('SIGTERM', 15);
}

class VideoDownloadManager
{
    private $tasks = [];
    private $firstValidDownload;
    private $validFilenameCount;

    private static $folderId;

    public static function setFolderId($var)
    {
        self::$folderId = $var;
    }

    public function addTask(string $url, string $format, string $timerBegin, string $timerEnd)
    {
        // Create a new VideoDownloadTask object and add it to the tasks array
        $this->tasks[] = new VideoDownloadTask($url, $format, $timerBegin, $timerEnd);
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function getFirstValidDownload()
    {
        return $this->firstValidDownload;
    }

    public static function isValidFormatType(string $format): bool
    {
        $validFormats = [
            'besta',
            'aac',
            'flac',
            'mp3',
            'm4a',
            'opus',
            'vorbis',
            'wav',
            'bestv',
            '3gp',
            'aac',
            'flv',
            'mp4',
            'ogg',
            'webm'
        ];
        return in_array($format, $validFormats);
    }

    public static function isValidURL(string $url): bool
    {
        // Use the filter_var function with FILTER_VALIDATE_URL to check if the string is a valid URL
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function isValidTimerFormat(string $timer): bool
    {
        // Regular expression to match the accepted formats
        $pattern = '/^((\d{1,2}):)?(\d{1,2}):(\d{1,2})|(\d{1,2})$/';

        // Check if the timer matches the pattern
        if (preg_match($pattern, $timer, $matches)) {
            $hours = isset($matches[2]) ? (int)$matches[2] : 0;
            $minutes = isset($matches[3]) ? (int)$matches[3] : 0;
            $seconds = isset($matches[4]) ? (int)$matches[4] : (int)$matches[5];

            // Validate the individual components (hours, minutes, and seconds)
            if ($hours >= 0 && $minutes >= 0 && $minutes < 60 && $seconds >= 0 && $seconds < 60) {
                return true; // Valid timer format
            }
        }
        return false; // Invalid timer format
    }


    public function executeAllDownloads()
    {
        foreach ($this->tasks as $task) {
            $task->executeDownload();
        }
    }

    public function renameAndOrder($order)
    {
        $i = 0;
        foreach ($this->tasks as $task) {
            if ($task->filename) {
                $newfilename = $task->clearedFilename;
                if ($order) {
                    if ($i < 10) {
                        $j = '0' . strval($i); //pour les fichier 1 à 9 on met 00,01,02...
                    } else {
                        $j = strval($i); //sinon juste 10,11,12...
                    }
                    $newfilename = $j . ' - ' . $newfilename; //on met au format "numero - nomfichier"
                }
                rename(self::$folderId . '/' . $task->filename, self::$folderId . '/' . $newfilename); //on renomme
                $i++;
            } elseif ($task->isPlaylist == true && $task->isPlaylistValid == true) {
                $i = 0;
                foreach ($task->playlistFilesname as $filename) {
                    $newfilename = $task->clearedPlaylistFilesname[$i];
                    if ($order) {
                        if ($i < 10) {
                            $j = '0' . strval($i); //pour les fichier 1 à 9 on met 00,01,02...
                        } else {
                            $j = strval($i); //sinon juste 10,11,12...
                        }
                        $newfilename = $j . ' - ' . $newfilename; //on met au format "numero - nomfichier"
                    }
                    rename(self::$folderId . '/' . $task->playlistName . '/' . $filename, self::$folderId . '/' . $task->playlistName . '/' . $newfilename);
                    $i++;
                }
            }
        }
    }

    public function getValidCount()
    {
        $validFilenameCount = 0;
        foreach ($this->tasks as $task) {
            if (!empty($task->filename)) {
                // If this is the first valid 'filename', store it
                if ($validFilenameCount === 0) {
                    $this->firstValidDownload = self::$folderId . '/' . $task->clearedFilename;
                }
                $validFilenameCount++;
            }
            if ($task->isPlaylistValid == true) {
                $validFilenameCount = 2;
            }
        }
        $this->validFilenameCount = $validFilenameCount;
        return $validFilenameCount;
    }

    public function makeArchive($rename)
    {
        $escapedRename = escapeshellarg($rename);
        if ($rename == self::$folderId) {
            $makeArchiveCommand = 'tar -cvf ' . self::$folderId . '.tar ' . self::$folderId;
            shell_exec($makeArchiveCommand);
        } else {
            // Rename the directory
            $renameCommand = "mv " . self::$folderId . " {$escapedRename}";
            shell_exec($renameCommand);

            // Create the archive
            $makeArchiveCommand = "tar -cvf {$escapedRename}.tar {$escapedRename}";
            shell_exec($makeArchiveCommand);

            // Rename the directory back
            $renameBackCommand = "mv {$escapedRename} " . self::$folderId;
            shell_exec($renameBackCommand);
        }
    }

    public function deleteFiles($rename)
    {
        shell_exec('rm -rf /var/www/html/YoutubeDL/' . self::$folderId);
        if ($this->validFilenameCount > 1) {
            shell_exec('rm -f "/var/www/html/YoutubeDL/' . $rename . '.tar"');
        }
    }
}

class VideoDownloadTask
{
    private $url;
    public $isPlaylist;
    public $isPlaylistValid;
    public $playlistName;
    public $playlistFilesname = [];
    public $clearedPlaylistFilesname = [];
    public $filename;
    public $clearedFilename;
    private $format;
    private $formatType;
    private $hasTimer;
    public $extension;
    private $timerBegin;
    private $timerEnd;

    private static $folderId;


    public static function setFolderId($var)
    {
        self::$folderId = $var;
    }

    public function __construct(string $url, string $format, string $timerBegin, string $timerEnd)
    {
        $this->url = $url;
        if (strpos($url, '/playlist?list=') !== false) {
            $error = 1;
            $j = 1;
            $this->isPlaylist = true;
            while ($error == 1 && $j < 11) {
                $getPlaylistName = 'yt-dlp -i -I ' . $j . ' -o "%(playlist_title)s" --get-filename --no-warnings ' . $url;
                exec($getPlaylistName, $output, $retval);
                $j++;
                if (!empty($output)) {
                    $error = 0;
                    $this->playlistName = $output[0];

                    $directoryPath = self::$folderId . '/' . $this->playlistName;
                    if (mkdir($directoryPath, 0777, true)) {
                    } else {
                        //error_log('Failed to create directory for playlist ' . $url . PHP_EOL);
                    }
                }
            }
            if ($error == 0) {
                $this->isPlaylistValid = true;
            } else {
                $this->isPlaylistValid = false;
            }
        }
        $this->format = $format;
        $audioFormats = ['besta', 'aac', 'flac', 'mp3', 'm4a', 'opus', 'vorbis', 'wav'];
        $videoFormats = ['bestv', '3gp', 'aac', 'flv', 'mp4', 'ogg', 'webm'];

        //définit si on est sur de l'audio ou de la vidéo
        if (in_array($format, $audioFormats)) {
            $this->formatType = 'audio';
        } else if (in_array($format, $videoFormats)) {
            $this->formatType = 'video';
        }
        $this->timerBegin = $timerBegin;
        $this->timerEnd = $timerEnd;
        if (empty($timerBegin) && empty($timerEnd)) {
            $this->hasTimer = 0;
        } else if (empty($timerBegin) && !empty($timerEnd)) {
            $this->timerBegin = '00:00';
            $this->hasTimer = 1;
        } else if (empty($timerEnd) && !empty($timerBegin)) {
            //check for video duration
            exec('yt-dlp ' . $url . ' --get-duration', $output);

            $pos = strpos($output[0], ':');

            if ($pos === false) {
                $this->timerEnd = '00:' . $output[0];
            }
            $this->hasTimer = 1;
        } else {
            $this->hasTimer = 1;
        }
    }

    private function getOutputfilePath()
    {
        $outputFile = "output.txt";

        if ($this->isPlaylist == true && $this->isPlaylistValid == true) {

            $playlistNameEscapedCmd = escapeshellcmd($this->playlistName);
            $playlistNameEscaped = str_replace(' ', '\ ', $playlistNameEscapedCmd);
            $outfileFoldered = self::$folderId . '/' . $playlistNameEscapedCmd . '/' . $outputFile;
        } else {
            $outfileFoldered = self::$folderId . '/' . $outputFile;
        }
        return $outfileFoldered;
    }

    private function isProcessRunning($pid)
    {
        $result = shell_exec(sprintf("ps %d", $pid));
        return (count(preg_split("/\n/", $result)) > 2);
    }

    private function executeDownloadCommand($downloadCommand)
    {
        error_log("download : $downloadCommand");
        $pid = exec($downloadCommand, $outputPid);

        while (ob_get_level()) {
            ob_get_contents();
            ob_end_clean();
        }

        while ($this->isProcessRunning($pid)) {
            error_log("process is running : $pid");
            ob_flush();
            flush();

            if (connection_aborted()) {
                error_log("PHP is shutting down.");
                posix_kill($pid, SIGTERM);
                error_log("shutting down : $pid");
                exit();
            }
            sleep(1);
        }
        error_log("log : " . print_r($outputPid, true));
    }

    private function processAndDeleteFile($filePath)
    {
        // Read the file contents
        $output = file_get_contents($filePath);
        $output = explode("\n", $output);

        // Escape the path to make it safe for shell execution
        $escapedPath = escapeshellarg($filePath);

        // Construct the command to delete the file
        $command = 'rm ' . $escapedPath;

        // Execute the command
        exec($command, $execOutput, $return_var);

        // Check if the command was successful
        if ($return_var === 0) {
            error_log("File deleted successfully.");
        } else {
            error_log("Failed to delete the file.");
        }

        // Return the file contents as an array
        return $output;
    }

    private function handleDownloadErrors($output, $wasAlreadyChecked)
    {
        if ($this->isPlaylist == true) {
            foreach ($output as $item) {
                if (strpos($item, 'ERROR') === 0) {
                    // Match the YouTube video ID and capture the sentence after the matched value
                    if (preg_match('/([^\]]{11}): ([^.]+)\./', $item, $matches)) {
                        $videoId = $matches[1];
                        $firstSentence = $matches[2];
                        echo "There was a problem with the following download: youtube.com/watch?v=" . $videoId . " (" . $firstSentence . ")\n";
                        echo "<br>";
                    }
                } else if (!empty($item)) {
                    $this->playlistFilesname[] = basename($item);
                    $this->clearedPlaylistFilesname[] = $this->clearFilename(basename($item));
                }
            }
        } else {
            if (strpos($output[0], 'ERROR') === 0) {
                $errorMsg = substr($output[0], 7); // 5 is the length of 'ERROR'
                if ($wasAlreadyChecked) {
                    echo "Il y a eu un problème avec le téléchargement suivant $this->url ($errorMsg)";
                    $this->filename = null;
                } else {
                    if (strpos($output[0], 'Requested format is not available') !== false) {
                        echo "Le format n'est pas disponible pour ce téléchargement : $this->url, le format par défaut sera sélectionné";
                        $downloadCommand = 'cd ' . self::$folderId . ' && yt-dlp'
                            . ($this->formatType == 'audio' ? ' -x' : '')
                            . ($this->hasTimer
                                ? ' --download-sections *' . $this->timerBegin . '-' . $this->timerEnd . ' --force-keyframes-at-cuts'
                                : '')
                            . ' --print after_move:filepath,ext'
                            . ' ' . $this->url
                            . " > output.txt 2>&1 & echo $!;";

                        $this->executeDownloadCommand($downloadCommand);
                        $outfileFoldered = $this->getOutputfilePath();
                        $output = $this->processAndDeleteFile($outfileFoldered);
                        $this->handleDownloadErrors($output, true);
                    } else {
                        echo "Il y a eu un problème avec le téléchargement suivant $this->url ($errorMsg)";
                        $this->filename = null;
                    }
                }
            } else {
                echo "The string does not start with 'ERROR'.";
                $this->extension = $output[1];
                $this->filename = basename($output[0]);
                $this->clearedFilename = $this->clearFilename(basename($output[0]));
            }
        }
    }

    public function executeDownload()
    {
        $outputFile = "output.txt";

        if ($this->isPlaylist == true && $this->isPlaylistValid == true) {
            $playlistNameEscapedCmd = escapeshellcmd($this->playlistName);
            $playlistNameEscaped = str_replace(' ', '\ ', $playlistNameEscapedCmd);
            $outfileFoldered = self::$folderId . '/' . $playlistNameEscapedCmd . '/' . $outputFile;
        } else {
            $outfileFoldered = self::$folderId . '/' . $outputFile;
        }

        //commande de téléchargement
        if ($this->isPlaylist == true && $this->isPlaylistValid == true) {
            $downloadCommand = "cd " . self::$folderId . "/{$playlistNameEscaped} && yt-dlp --no-warnings --print after_move:filepath"
                . ($this->formatType == 'audio' ? ' -x' : '')
                . ($this->formatType == 'audio' && $this->format != 'besta'
                    ? ' --audio-format ' . $this->format
                    : ($this->formatType == 'video' && $this->format != 'bestv'
                        ? ' -f ' . $this->format
                        : ''))
                . ' ' . $this->url
                . " > $outputFile 2>&1 & echo $!; ";
        } else {
            $downloadCommand = 'cd ' . self::$folderId . ' && yt-dlp --no-warnings --print after_move:filepath,ext'
                . ($this->formatType == 'audio' ? ' -x' : '')
                . ($this->formatType == 'audio' && $this->format != 'besta'
                    ? ' --audio-format ' . $this->format
                    : ($this->formatType == 'video' && $this->format != 'bestv'
                        ? ' -f ' . $this->format
                        : ''))
                . ($this->hasTimer ? ' --download-sections *' . $this->timerBegin . '-' . $this->timerEnd . ' --force-keyframes-at-cuts' : '')
                . ' ' . $this->url
                . " > $outputFile 2>&1 & echo $!;";
        }

        $this->executeDownloadCommand($downloadCommand);

        $output = $this->processAndDeleteFile($outfileFoldered);

        $this->handleDownloadErrors($output, false);
    }

    public function clearFilename($filename)
    {
        // Use preg_replace to remove the part between brackets and the space before it
        $result = preg_replace('/\s\[[^\]]+\]/', '', $filename);
        return $result;
    }
}

function deleteDuplicateUrls(&$urls, &$formats, &$selectedElements, &$timers)
{
    $j = 0;
    /*Cette partie supprime les liens ayant été mis plusieurs fois*/
    $uniqueValues = array();
    foreach ($urls as $key => $value) {
        // Check if the value has already occurred in the array
        if (in_array($value, $uniqueValues) || empty($value)) {
            // If the value is a duplicate, remove it from the array
            unset($urls[$key]);
            unset($formats[$key]);
            unset($selectedElements[$key]);
            if ($selectedElements[$key] != -1) {
                unset($timers[$j]);
                unset($timers[$j + 1]);
            }
        } else {
            // If the value is unique, add it to the $uniqueValues array
            $uniqueValues[] = $value;
        }
        if ($selectedElements[$key] != -1) {
            // If we're on a link with timer we move onto the next timer 
            $j = $j + 2;
        }
    }
    // Re-index the array after removing duplicates
    $urls = array_values($urls);
    $formats = array_values($formats);
    $selectedElements = array_values($selectedElements);
    if (isset($timers) && !empty($timers)) {
        $timers = array_values($timers);
    }
}


// Start the session
session_start();
ob_start();

require_once 'db_connection.php';

$sessionId = session_id() . '_' . uniqid();
$creationTime = time();

$maxAttempts = 20; // Maximum number of attempts
$attempt = 0;

do {
    $attempt++;

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert log entry
        $stmt = $pdo->prepare("INSERT INTO session_logs (session_id, creation_time) VALUES (:session_id, :creation_time)");
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':creation_time', $creationTime);

        // Execute the statement
        if ($stmt->execute()) {
            // Commit the transaction
            $pdo->commit();

            // Log successful write to debug log
            error_log("Successfully wrote: $sessionId $creationTime");

            // Break out of the loop if successfully written
            break;
        } else {
            // Rollback the transaction in case of failure
            $pdo->rollBack();
            error_log("Failed to insert log entry (Attempt $attempt)");
        }
    } catch (PDOException $e) {
        // Rollback the transaction in case of exception
        $pdo->rollBack();
        error_log("Database error: " . $e->getMessage() . " (Attempt $attempt)");
    }

    // Wait for a short period before retrying (optional)
    usleep(100000); // 100 milliseconds

} while ($attempt < $maxAttempts);

if ($attempt >= $maxAttempts) {
    error_log("Failed to write to database after $maxAttempts attempts");
    // Handle failure to write after maximum attempts
} else {
    // Successfully wrote to the database
    echo "Successfully wrote to database";
    echo "<br>";
}



$directoryPath = $sessionId;
if (mkdir($sessionId, 0777, true)) {
} else {
    error_log('Failed to create directory for playlist ' . $url . PHP_EOL);
}



VideoDownloadManager::setFolderId($sessionId);
VideoDownloadTask::setFolderId($sessionId);


// Instantiate the VideoDownloadManager
$downloadManager = new VideoDownloadManager();

$urls = $_POST['title']; //liste des urls
$formats = $_POST['format']; //format pour chaque url
$timers = $_POST['posTimer']; //récupère les sections debut et fin
$selectedElements = $_POST['selectedElements']; //récupère les liens concernés par un timer
$rename = $_POST['rename']; //if multiple files specified name for renaming tar containing files
if (!$rename) {
    $rename = $sessionId;
}
$ordre = $_POST['ordre']; //garder l'ordre ou non




// Call the function and pass the variables as arguments
deleteDuplicateUrls($urls, $formats, $selectedElements, $timers);

$length = count($urls);
$ordre = ($length > 1) ? $ordre : false;
$rename = ($length > 1) ? $rename : $sessionId;



// Add tasks to the download manager and validations
$j = 0;
for ($i = 0; $i < count($urls); $i++) {
    if (VideoDownloadManager::isValidURL($urls[$i])) {
        if (!VideoDownloadManager::isValidFormatType($formats[$i])) {
            error_log('Wrong format for ' . $urls[$i]  . '( ' . $formats[$i] . ' )' . PHP_EOL);
            $formats[$i] = 'besta';
        }
        if ($selectedElements[$i] != -1) {
            if (empty($timers[2 * $j])) {
                if (VideoDownloadManager::isValidTimerFormat($timers[2 * $j + 1])) {
                    $downloadManager->addTask($urls[$i], $formats[$i], "", $timers[2 * $j + 1]);
                } else {
                    error_log('Wrong timer for ' . $urls[$i] . '( ' . $timers[2 * $j] . ' - ' . $timers[2 * $j + 1] . ' )' . PHP_EOL);
                    $downloadManager->addTask($urls[$i], $formats[$i], "", "");
                }
            } elseif (empty($timers[2 * $j + 1])) {
                if (VideoDownloadManager::isValidTimerFormat($timers[2 * $j])) {
                    $downloadManager->addTask($urls[$i], $formats[$i], $timers[2 * $j], "");
                } else {
                    error_log('Wrong timer for ' . $urls[$i] . '( ' . $timers[2 * $j] . ' - ' . $timers[2 * $j + 1] . ' )' . PHP_EOL);
                    $downloadManager->addTask($urls[$i], $formats[$i], "", "");
                }
            } else {
                if (VideoDownloadManager::isValidTimerFormat($timers[2 * $j]) && VideoDownloadManager::isValidTimerFormat($timers[2 * $j + 1])) {
                    $downloadManager->addTask($urls[$i], $formats[$i], $timers[2 * $j], $timers[2 * $j + 1]);
                } else {
                    error_log('Wrong timer for ' . $urls[$i] . '( ' . $timers[2 * $j] . ' - ' . $timers[2 * $j + 1] . ' )' . PHP_EOL);
                    $downloadManager->addTask($urls[$i], $formats[$i], "", "");
                }
            }
            $j++;
        } else {
            $downloadManager->addTask($urls[$i], $formats[$i], "", "");
        }
    } else {
        error_log('Wrong URL for ' . $urls[$i] . PHP_EOL);
    }
}

// Execute all downloads
$downloadManager->executeAllDownloads();
$downloadManager->renameAndOrder($ordre);


if ($downloadManager->getValidCount() > 1) {
    $downloadManager->makeArchive($rename);
    //$downloadManager->download($rename . '.tar');
    $file = $rename . '.tar';
} else {
    $firstFilename = $downloadManager->getFirstValidDownload();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //$downloadManager->download($firstFilename);
        $file = $firstFilename;
    }
}
echo $file;
?>
<form action="finaldownload.php" method="get">
    <input type="hidden" name="file" value="<?php echo htmlspecialchars($file); ?>">
    <input type="submit" value="Telecharger">
</form>
<?php

//$downloadManager->deleteFiles($rename);


// Flush output to make sure any progress messages are sent to the client
flush();
// Terminate script execution
exit;
/*
problème ordre qui s'affiche pas
pouvoir ajouter plusieurs liens simultanément
problème 2 cases à cocher qui s'affichent
cacher timer si playlist
progressbar
tuto en bas + FAQ
night mode
*/
?>
<a href="url.php"> retour </a>