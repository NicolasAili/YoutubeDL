<?php class DestructTester {
    private $fileHandle;

    public function __construct($fileHandle){
        // fileHandle that we log to
        $this->fileHandle = $fileHandle;
        // call $this->onShutdown() when PHP is shutting down.
        register_shutdown_function(array($this, "onShutdown"));
    }

    public function onShutdown() {
        $isAborted = connection_aborted();
        fwrite($this->fileHandle, "PHP is shutting down. isAborted: $isAborted\n");

        // NOTE
        // If connection_aborted() AND ignore_user_abort = false, PHP will immediately terminate
        // this function when it encounters flush. This means your shutdown functions can end
        // prematurely if: connection is aborted, ignore_user_abort=false, and you try to flush().
        echo "Test.";
        flush();
        fwrite($this->fileHandle, "This was written after a flush.\n");
    }
    public function __destruct() {
        $isAborted = connection_aborted();
        fwrite($this->fileHandle, "DestructTester is getting destructed. isAborted: $isAborted\n");
    }
}

// Create a DestructTester
// It'll log to our file on PHP shutdown and __destruct().
$fileHandle = fopen("test.txt", "a+");
fwrite($fileHandle, "---BEGINNING TEST---\n");
$dt = new DestructTester($fileHandle);

// Set this value to see how the logs end up changing
// ignore_user_abort(true);

// Remove any buffers so that PHP attempts to send data on flush();
while (ob_get_level()){
    ob_get_contents();
    ob_end_clean();
}

$outputFile = "output.txt";
$downloadCommand = "yt-dlp --no-warnings --print after_move:filepath,ext -x https://www.youtube.com/watch?v=p2AWYanIHkc";
exec($downloadCommand . " > $outputFile & echo $!; ");   


// Let's loop for 10 seconds
//   If ignore_user_abort=true:
//      This will continue to run regardless.
//   If ignore_user_abort=false:
//      This will immediate terminate when the user disconnects and PHP tries to flush();
//      PHP will begin its shutdown process.
// In either case, connection_aborted() should subsequently return "true" after the user
// has disconnected (hit STOP button in browser), AND after PHP has attempted to flush().
$numSleeps = 0;
$isDownloadOver = false;
$numSleeps = 0;
while ($numSleeps++ < 10) {
    $connAbortedStr = connection_aborted() ? "YES" : "NO";
    $str = "Slept $numSleeps times. Connection aborted: $connAbortedStr";
    echo "$str<br>";
    // If ignore_user_abort = false, script will terminate right here.
    // Shutdown functions will being.
    // Otherwise, script will continue for all 10 loops and then shutdown.
    flush();

    $connAbortedStr = connection_aborted() ? "YES" : "NO";
    fwrite($fileHandle, "flush()'d $numSleeps times. Connection aborted is now: $connAbortedStr\n");
    sleep(1);
}
echo "DONE SLEEPING!<br>";
die;

//____________________________________________________________________________


            /*clearstatcache(); // Clear PHP's file status cache to get fresh file information
            if (file_exists($outfileFoldered)) {
                //error_log("File exists: $outfileFoldered");
                if (is_readable($outfileFoldered)) {
                    //error_log("file is readable");
                    $handle = fopen($outfileFoldered, 'r');
                    //error_log("opening file");
                    if (flock($handle, LOCK_SH)) {
                        // Lock acquired, check if the file is non-empty
                        if (filesize($outfileFoldered) > 0) {
                            $isDownloadOver = true;
                            //error_log("filesize : " . filesize($outfileFoldered));
                            error_log("Download is complete.");
                        } else {
                            //error_log("still downloading..." . filesize($outfileFoldered));
                        }
                        flock($handle, LOCK_UN); // Release the lock
                    }
                    //error_log("");
                    fclose($handle);
                    //error_log("closing file");
                }
            } else {
                //error_log("File does not exist yet: $outfileFoldered");
            }*/

?>