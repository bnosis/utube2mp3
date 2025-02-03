<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $url = trim($_POST['url']);
    if (!$url) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => "URL is required.",
        ]);
        exit;
    }

    // Sanitize and validate the URL
    $url = filter_var($url, FILTER_SANITIZE_URL);

    if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube|youtu|youtube-nocookie)\.(com|be)\/(watch\?v=|embed\/|playlist\?list=)[\w-]+/', $url)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => "Invalid YouTube URL provided.",
        ]);
        exit;
    }

    // Ensure download directory exists
    $downloadDir = realpath(__DIR__ . '/../downloads/');
    if (!$downloadDir) {
        mkdir(__DIR__ . '/../downloads/', 0777, true);
        $downloadDir = realpath(__DIR__ . '/../downloads/');
    }

    // Get the YouTube video title
    $titleCommand = "yt-dlp --get-title " . escapeshellarg($url) . " 2>&1";
    $videoTitle = shell_exec($titleCommand);
    if (!$videoTitle) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => "Failed to retrieve video title.",
        ]);
        exit;
    }

    // Sanitize the video title to create a valid filename
    $safeTitle = preg_replace('/[^A-Za-z0-9\- ]/', '', trim($videoTitle)); // Allow letters, numbers, hyphens, and spaces
    $fileName = $safeTitle . '.mp3';
    $filePath = $downloadDir . DIRECTORY_SEPARATOR . $fileName;

    // Download and convert the video to MP3 with the best quality (320 kbps)
    $command = "yt-dlp -x --audio-format mp3 --audio-quality 0 --output " . escapeshellarg($filePath) . " " . escapeshellarg($url) . " 2>&1";
    $output = shell_exec($command);

    // Check if the file was created
    if (file_exists($filePath)) {
        $downloadUrl = "/downloads/" . rawurlencode($fileName); // Ensure proper URL encoding
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => "Conversion successful!",
            'file' => $downloadUrl,
            'fileName' => $fileName,
        ]);
    } else {
        error_log("File not found: $filePath");
        error_log("Command output: $output");
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => "Failed to process URL. Output: $output",
        ]);
    }
}
