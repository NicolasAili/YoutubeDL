<?while (!$isDownloadOver) {
            // If ignore_user_abort = false, script will terminate right here.
            // Shutdown functions will being.
            // Otherwise, script will continue for all 10 loops and then shutdown.
            clearstatcache(); // Clear PHP's file status cache to get fresh file information
            if (file_exists($outfileFoldered)) {
                error_log("file exists");
                if (is_readable($outfileFoldered)) {
                    error_log("file is readable");
                    $handle = fopen($outfileFoldered, 'r');
                    error_log("");
                    error_log("opening file");
                    error_log("filesize : " . filesize($outfileFoldered));
                    fclose($handle);
                    error_log("closing file");
                }
            }
            else {
                error_log("File does not exist yet.");
            }
            flush();

            $connAbortedStr = connection_aborted() ? "YES" : "NO";

            if (connection_aborted()) {
                $isDownloadOver = true;
                error_log("PHP is shutting down.");
                exit();
                // NOTE
                // If connection_aborted() AND ignore_user_abort = false, PHP will immediately terminate
                // this function when it encounters flush. This means your shutdown functions can end
                // prematurely if: connection is aborted, ignore_user_abort=false, and you try to flush().
                echo "Test.";
                flush();
            }
            error_log("flush()'d $numSleeps times. Connection aborted is now: $connAbortedStr\n");
            sleep(2);
            $numSleeps++;
        }