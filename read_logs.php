<?php
$logFile = __DIR__ . '/../ferrindep/storage/logs/laravel.log';

echo "<h1>Log Reader</h1>";
echo "Reading: $logFile <br>";

if (!file_exists($logFile)) {
    die("Log file not found.");
}

$lines = file($logFile);
$count = count($lines);
$offset = max(0, $count - 200);
$lastLines = array_slice($lines, $offset);

echo "<pre>";
foreach ($lastLines as $line) {
    echo htmlspecialchars($line);
}
echo "</pre>";
