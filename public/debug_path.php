<?php
header('Content-Type: text/plain');
echo "CURRENT DIR: " . __DIR__ . "\n";
echo "PARENT DIR: " . dirname(__DIR__) . "\n";

echo "\n--- SCANDIR PARENT ---\n";
print_r(scandir(dirname(__DIR__)));

echo "\n--- CHECKING FERRINDEP ---\n";
if (is_dir(dirname(__DIR__) . '/ferrindep')) {
    echo "✅ 'ferrindep' folder exists.\n";
    print_r(scandir(dirname(__DIR__) . '/ferrindep'));
} else {
    echo "❌ 'ferrindep' folder NOT FOUND.\n";
}
