<?php
$zipFile = __DIR__ . '/baski_deploy_linux.zip';
@unlink($zipFile);

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Zip açılamadı");
}

$rootPath = __DIR__;
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$exclude = ['baski_deploy.zip', 'baski_deploy_linux.zip', 'make_zip.php', 'unzip.php'];

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        $relativePath = str_replace('\\', '/', $relativePath);

        if (in_array(basename($relativePath), $exclude)) {
            continue;
        }

        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();
echo "UNIX forward-slash ZIP created successfully: " . filesize($zipFile) . " bytes\n";
