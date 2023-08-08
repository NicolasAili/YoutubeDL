<?php
class VideoDownloadManager
{
    // Step 1: Define the log file path and name
    public $logFilePath = 'logs/script_log.txt';
    // Step 2: Create a variable to hold the log content
    public static $logContent = '';
    // Step 3: Add text to the variable (e.g., log messages)
    /*
    $logContent .= 'Log entry 1' . PHP_EOL;
    $logContent .= 'Log entry 2' . PHP_EOL;
    $logContent .= 'Log entry 3' . PHP_EOL;

    // Step 4: Write the log content to the log file
    file_put_contents($logFilePath, $logContent, FILE_APPEND);

    // Step 5: Optionally, you can also add a timestamp to each log entry
    $logContentWithTimestamp = '[' . date('Y-m-d H:i:s') . '] ' . $logContent;

    // Write the log content with timestamps to the log file
    file_put_contents($logFilePath, $logContentWithTimestamp, FILE_APPEND);*/
    private $tasks = [];
    private $firstValidDownload;
    private $validFilenameCount;

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
                rename('contenu/' . $task->filename, 'contenu/' . $newfilename); //on renomme
                $i++;
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
                    $this->firstValidDownload = 'contenu/' . $task->clearedFilename;
                }
                $validFilenameCount++;
            }
        }
        $this->validFilenameCount = $validFilenameCount;
        return $validFilenameCount;
    }

    public function makeArchive($rename)
    {
        if ($rename == 'contenu') {
            exec('tar -cvf contenu.tar contenu'); //crée l'archive avec nom par défaut
        } else {
            exec('tar -cvf ' . $rename . '.tar --transform \'s/^contenu/' . $rename . '/\'' . ' contenu'); //crée l'archive renommée par l'user
        }
    }

    public function download($rest)
    {
        if (file_exists($rest)) {
            ob_start();
            header('Content-Description: File Transfer');
            header('Content-Type: application/x-tar-gz');
            header('Content-Disposition: attachment; filename="'.basename($rest).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($rest));
        
            // Clean the output buffer and flush before reading the file
            ob_clean();
            flush();
        
            readfile($rest);
            ob_end_flush();
            // Terminate script execution
            //exit;
        } else {
            echo "erreur, un bug est apparu, pas de chance :/";
        }
    }

    public function deleteFiles($rename){
        exec('yes | rm -r contenu/*');
        if($this->validFilenameCount > 1){
            exec('yes | rm ' . $rename . '.tar');
        }
    }
}

class VideoDownloadTask
{
    private $url;
    public $filename;
    public $clearedFilename;
    private $format;
    private $formatType;
    private $hasTimer;
    public $extension;
    private $timerBegin;
    private $timerEnd;

    public function handleErrors(array $output, string $formatType)
    {
        $downloadCommand = 'cd contenu && yt-dlp' . ($this->formatType == 'audio' ? ' -x' : '') . ' ' . $this->url
            . ($this->formatType == 'audio' && $this->format != 'besta'
                ? ' --audio-format ' . $this->format
                : ($this->formatType == 'video' && $this->format != 'bestv'
                    ? ' -f ' . $this->format
                    : ''))
            . ($this->hasTimer ? ' --download-sections *' . $this->timerBegin . '-' . $this->timerEnd . ' --force-keyframes-at-cuts' : '')
            . ' 2>&1';
        exec($downloadCommand, $output, $retval); //telecharge la video 

        $errorMessage = 'Requested format is not available';
        foreach ($output as $line) {
            if (strpos($line, $errorMessage) !== false) {
                return true;
            }
        }
        return false;
    }

    public function clearFilename($filename)
    {
        // Use preg_replace to remove the part between brackets and the space before it
        $result = preg_replace('/\s\[[^\]]+\]/', '', $filename);
        return $result;
    }


    public function __construct(string $url, string $format, string $timerBegin, string $timerEnd)
    {
        $this->url = $url;
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

    public function executeDownload()
    {
        //commande de téléchargement
        $downloadCommand = 'cd contenu && yt-dlp' . ($this->formatType == 'audio' ? ' -x' : '') . ' ' . $this->url
            . ($this->formatType == 'audio' && $this->format != 'besta'
                ? ' --audio-format ' . $this->format
                : ($this->formatType == 'video' && $this->format != 'bestv'
                    ? ' -f ' . $this->format
                    : ''))
            . ($this->hasTimer ? ' --download-sections *' . $this->timerBegin . '-' . $this->timerEnd . ' --force-keyframes-at-cuts' : '')
            . ' --print after_move:filepath,ext';

        exec($downloadCommand, $output, $retval); //telecharge

        //s'il y a une erreur
        if (empty($output)) {
            $error = $this->handleErrors($output, $this->formatType);
            if ($error) {
                //on retente le download avec le meilleur format disponible
                $downloadCommand = 'cd contenu && yt-dlp' . ($this->formatType == 'audio' ? ' -x' : '') . ' ' . $this->url
                    . ($this->hasTimer ? ' --download-sections *' . $this->timerBegin . '-' . $this->timerEnd . ' --force-keyframes-at-cuts' : '')
                    . ' --print after_move:filepath,ext';

                exec($downloadCommand, $output, $retval); //telecharge l'audio

                if (!empty($output)) {
                    $this->extension = $output[1];
                    $this->filename = basename($output[0]);
                    $this->clearedFilename = $this->clearFilename(basename($output[0]));
                } else { //erreur générale sur ce download
                    VideoDownloadManager::$logContent .= "erreur générale après erreur format pour " . $this->url . PHP_EOL;
                    $this->filename = null;
                }
            } else { //erreur générale sur ce download
                VideoDownloadManager::$logContent .= "erreur générale pour " . $this->url . PHP_EOL;
                $this->filename = null;
            }
        } else {
            $this->extension = $output[1];
            $this->filename = basename($output[0]);
            $this->clearedFilename = $this->clearFilename(basename($output[0]));
        }
    }
}

function deleteDuplicateUrls(&$urls, &$formats, &$selectedElements, &$timers)
{
    $j = 0;
    /*Cette partie supprime les liens ayant été mis plusieurs fois*/
    $uniqueValues = array();
    foreach ($urls as $key => $value) {
        // Check if the value has already occurred in the array
        if (in_array($value, $uniqueValues)) {
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

function displayVariables($urls, $formats, $timers, $selectedElements, $rename, $ordre)
{
    print_r($urls);
    echo "<br>";
    print_r($formats);
    echo "<br>";

    print_r($timers);
    echo "<br>";

    print_r($selectedElements);
    echo "<br>";

    echo $rename;
    echo "<br>";
    echo $ordre;
    echo "<br>";
    echo "_______________________________________________________";
    echo "<br>";
}


// Instantiate the VideoDownloadManager
$downloadManager = new VideoDownloadManager();

$urls = $_POST['title']; //liste des urls
$formats = $_POST['format']; //format pour chaque url
$timers = $_POST['posTimer']; //récupère les sections debut et fin
$selectedElements = $_POST['selectedElements']; //récupère les liens concernés par un timer
$rename = $_POST['rename']; //if multiple files specified name for renaming tar containing files
if (!$rename) {
    $rename = 'contenu';
}
$ordre = $_POST['ordre']; //garder l'ordre ou non





// Call the function and pass the variables as arguments
deleteDuplicateUrls($urls, $formats, $selectedElements, $timers);

//displayVariables($urls, $formats, $timers, $selectedElements, $rename, $ordre);

// Add tasks to the download manager and validations
$j = 0;
for ($i = 0; $i < count($urls); $i++) {
    if (VideoDownloadManager::isValidURL($urls[$i])) {
        if (!VideoDownloadManager::isValidFormatType($formats[$i])) {
            VideoDownloadManager::$logContent .= 'Wrong format for ' . $urls[$i]  . '( ' . $formats[$i] . ' )' . PHP_EOL;
            $formats[$i] = 'besta';
        }
        if ($selectedElements[$i] != -1) {
            if (VideoDownloadManager::isValidTimerFormat($timers[2 * $j]) && VideoDownloadManager::isValidTimerFormat($timers[2 * $j + 1])) {
                $downloadManager->addTask($urls[$i], $formats[$i], $timers[2 * $j], $timers[2 * $j + 1]);
            } else {
                VideoDownloadManager::$logContent .= 'Wrong timer for ' . $urls[$i] . '( ' . $timers[2 * $j] . ' - ' . $timers[2 * $j + 1] . ' )' . PHP_EOL;
                $downloadManager->addTask($urls[$i], $formats[$i], "", "");
            }
            $j++;
        } else {
            $downloadManager->addTask($urls[$i], $formats[$i], "", "");
        }
    } else {
        VideoDownloadManager::$logContent .= 'Wrong URL for ' . $urls[$i] . PHP_EOL;
    }
}

// Execute all downloads
$downloadManager->executeAllDownloads();
$downloadManager->renameAndOrder($ordre);

if ($downloadManager->getValidCount() > 1) {
    $downloadManager->makeArchive($rename);
    $downloadManager->download($rename . '.tar');
    echo $rename;
} else {
    $firstFilename = $downloadManager->getFirstValidDownload();
    $downloadManager->download($firstFilename);
}

$downloadManager->deleteFiles($rename);



echo VideoDownloadManager::$logContent;
echo "<br>";

// Terminate script execution
exit;
/*
logs
playlists
progressbar*/
?>
<a href="url.php"> retour </a>
