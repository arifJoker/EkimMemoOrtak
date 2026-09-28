<?php
$logPath = __DIR__ . '/error_log';
if (file_exists($logPath)) {
    $lines = array_slice(file($logPath), -20);
    echo implode("", $lines);
} else {
    echo "No error_log found.";
}
