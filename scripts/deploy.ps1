param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("tambaski.com.tr", "bykcut.com.tr")]
    [string]$ProjectName
)

$cpanelHost = "104.247.168.131:2083"
$cpanelUser = "arifuzco"
$cpanelToken = "ZTI4T342FVZFFMRHVWL9MHBJEWZ3W58R"
$authHeader = "cpanel ${cpanelUser}:${cpanelToken}"
$targetDir = "/home/arifuzco/$ProjectName"
$localDir = (Resolve-Path (Join-Path $PSScriptRoot "..\$ProjectName")).Path
$tempZip = Join-Path $PSScriptRoot "deploy.zip"
$unpackerLocal = Join-Path $PSScriptRoot "unpacker_auto.php"

Write-Host ">>> $ProjectName canli sunucuya deploy ediliyor..." -ForegroundColor Cyan

# 1. Zip olustur (Linux uyumlu FORWARD SLASH / yapisi ile)
if (Test-Path $tempZip) { Remove-Item $tempZip -Force }
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$zip = [System.IO.Compression.ZipFile]::Open($tempZip, [System.IO.Compression.ZipArchiveMode]::Create)
$files = Get-ChildItem -Path $localDir -Recurse -File
foreach ($file in $files) {
    if ($file.FullName -like "*\.git\*" -or $file.Name -eq "deploy.zip") {
        continue
    }
    $rel = $file.FullName.Substring($localDir.TrimEnd('\').Length + 1).Replace("\", "/")
    if (![string]::IsNullOrEmpty($rel)) {
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $file.FullName, $rel) | Out-Null
    }
}
$zip.Dispose()

Write-Host ">>> Zip olusturuldu (Forward slash standardi ile)." -ForegroundColor Gray

# 2. cPanel'e Zip yukle
Write-Host ">>> Zip paketi sunucuya yukleniyor..." -ForegroundColor Gray
$uploadUrl = "https://$cpanelHost/execute/Fileman/upload_files"
$resUpload = & curl.exe -s -k -H "Authorization: $authHeader" -F "dir=$targetDir" -F "file-1=@$tempZip" -F "overwrite=1" "$uploadUrl"

# 3. Zip dosyasini sunucuda ac (Extract - PHP ZipArchive ile %100 Overwrite garantili + OPCache temizligi)
Write-Host ">>> Sunucuda arsiv aciliyor (ZipArchive entry-by-entry force overwrite)..." -ForegroundColor Gray
$unpackerCode = @"
<?php
set_time_limit(300);
`$zipPath = '$targetDir/deploy.zip';
`$dest = '$targetDir';
`$res = 'ZIP_NOT_FOUND';

// Yetkisiz / eski mock dosyalari temizle
`$rogueFiles = [
    `$dest . '/admin/includes/admin_functions.php',
    `$dest . '/admin/includes',
    `$dest . '/admin/product_edit.php',
    `$dest . '/admin/order_detail.php',
    `$dest . '/vehicle_sticker_customizer.php',
    `$dest . '/vehicle_studio.php',
    `$dest . '/assets/js/vehicle_customizer.js',
    `$dest . '/assets/js/vehicle_3d_engine.js',
    `$dest . '/tambaski_deploy.zip',
    `$dest . '/temp_up.zip'
];
foreach (`$rogueFiles as `$rf) {
    if (is_file(`$rf)) @unlink(`$rf);
    elseif (is_dir(`$rf)) @rmdir(`$rf);
}

// assets/vehicles klasorunu komple temizle
function rrmdir(`$dir) {
    if (is_dir(`$dir)) {
        `$objects = scandir(`$dir);
        foreach (`$objects as `$object) {
            if (`$object != "." && `$object != "..") {
                if (is_dir(`$dir . DIRECTORY_SEPARATOR . `$object) && !is_link(`$dir . "/" . `$object))
                    rrmdir(`$dir . DIRECTORY_SEPARATOR . `$object);
                else
                    @unlink(`$dir . DIRECTORY_SEPARATOR . `$object);
            }
        }
        @rmdir(`$dir);
    }
}
rrmdir(`$dest . '/assets/vehicles');

if (file_exists(`$zipPath)) {
    `$zip = new ZipArchive();
    if (`$zip->open(`$zipPath) === TRUE) {
        `$count = 0;
        for (`$i = 0; `$i < `$zip->numFiles; `$i++) {
            `$entryName = `$zip->getNameIndex(`$i);
            `$targetFile = `$dest . '/' . `$entryName;

            if (substr(`$entryName, -1) === '/') {
                if (!is_dir(`$targetFile)) {
                    @mkdir(`$targetFile, 0755, true);
                }
                continue;
            }

            `$dir = dirname(`$targetFile);
            if (!is_dir(`$dir)) {
                @mkdir(`$dir, 0755, true);
            }

            `$content = `$zip->getFromIndex(`$i);
            if (`$content !== false) {
                file_put_contents(`$targetFile, `$content);
                @chmod(`$targetFile, 0644);
                `$count++;
            }
        }
        `$zip->close();
        `$res = "EXTRACT_OK_ENTRIES_" . `$count;
    } else {
        `$res = 'EXTRACT_OPEN_FAILED';
    }
    @unlink(`$zipPath);
}

// Opcache temizle
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

// Gecmisten kalan yanlis backslashli dosyalari temizle
foreach (glob('$targetDir/*\\\\*') as `$brokenFile) {
    @unlink(`$brokenFile);
}

echo `$res;
@unlink(__FILE__);
"@

[System.IO.File]::WriteAllText($unpackerLocal, $unpackerCode)
$resUnpacker = & curl.exe -s -k -H "Authorization: $authHeader" -F "dir=$targetDir" -F "file-1=@$unpackerLocal" -F "overwrite=1" "$uploadUrl"
if (Test-Path $unpackerLocal) { Remove-Item $unpackerLocal -Force }

$unpackerDomain = if ($ProjectName -eq "tambaski.com.tr") { "https://tambaski.com.tr" } else { "https://bykcut.com.tr" }
$extractResult = & curl.exe -s -k "$unpackerDomain/unpacker_auto.php"
Write-Host "   Sunucu Acma Sonucu: $extractResult" -ForegroundColor Yellow

# 4. Yerel zip'i temizle
if (Test-Path $tempZip) { Remove-Item $tempZip -Force }

Write-Host ">>> $ProjectName basariyla canli sunucuya aktarildi ve yayinlandi!" -ForegroundColor Green
