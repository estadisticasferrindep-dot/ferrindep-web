<?php
$files = [
    __DIR__ . '/../app/Http/Controllers/WebController.php',
    __DIR__ . '/../ferrindep/app/Http/Controllers/WebController.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "Processing $file...<br>";
        $content = file_get_contents($file);
        $bom = pack('H*', 'EFBBBF');
        if (substr($content, 0, 3) == $bom) {
            echo "BOM FOUND! Removing it...<br>";
            $content = substr($content, 3);
            if (file_put_contents($file, $content)) {
                echo "BOM Removed successfully.<br>";
            } else {
                echo "Failed to write file (Permissions?).<br>";
            }
        } else {
            echo "No BOM found at start. Checking whitespace...<br>";
            if (substr($content, 0, 5) !== '<?php') {
                echo "Warning: File does not start with &lt;?php. Hex dump: " . bin2hex(substr($content, 0, 10)) . "<br>";
                // Attempt to trim
                $clean = trim($content);
                if (substr($clean, 0, 5) === '<?php') {
                    echo "Trimming whitespace...<br>";
                    file_put_contents($file, $clean);
                }
            } else {
                echo "File starts with &lt;?php correctly.<br>";
            }
        }
    } else {
        echo "File not found: $file<br>";
    }
}
