<?php

$downloadsFolder = __DIR__ . '/../downloads/';
$timeThreshold = 5 * 60; 
$safetyMargin = 30; 

while (true) {
    $currentTime = time();

    if (is_dir($downloadsFolder)) {
        $files = scandir($downloadsFolder);

        foreach ($files as $file) {
            $filePath = $downloadsFolder . DIRECTORY_SEPARATOR . $file;

            if ($file === '.' || $file === '..') {
                continue;
            }

            if (is_file($filePath)) {
                $fileTime = filectime($filePath); // Check file creation time
                $fileAge = $currentTime - $fileTime;

                // Log file information
                error_log("File: $file | File Creation Time: $fileTime | File Age: $fileAge");

                if ($fileAge > $timeThreshold + $safetyMargin) {
                    unlink($filePath); // Delete file if it's older than the threshold
                    error_log("Deleted: $filePath"); // Log the file deletion
                }
            }
        }
    }

    sleep(60); // Check every minute
}