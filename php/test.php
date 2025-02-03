<?php
if ($argc !== 2){
    echo "Usage: php script.php <Youtube_URL>\n";
    exit(1);
}

$url = $argv[1];

if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]{11}(&.*)?$/', $url)) {
    echo "Error: The provided URL is not a valid YouTube video link.\n";
    exit(1);
}

$tempDir = sys_get_temp_dir();
$tempFile = $tempDir . '/' . uniqid("yt_", true) . '.mp3';

$command = "yt-dlp -x --audio-format mp3 --output " . escapeshellarg($tempFile) . " " . escapeshellarg($url) . " 2>&1";
$output = shell_exec($command);

if (file_exists($tempFile)) {
    echo "Conversion successful! File: $tempFile\n";
} else {
    echo "Conversion failed. Output: $output\n";
}