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
$localDir = Join-Path $PSScriptRoot "..\$ProjectName"

Write-Host "🚀 $ProjectName cPanel'e canlıya yükleniyor..." -ForegroundColor Cyan

# Find files to upload (excluding markdown docs and git files)
$files = Get-ChildItem -Path $localDir -Recurse -File | Where-Object {
    $_.FullName -notmatch '\.git' -and 
    $_.Name -ne 'PROJECT_STATE.md' -and 
    $_.Name -ne 'ACTIVITY_LOG.md' -and 
    $_.Name -ne 'ARCHITECTURE.md' -and 
    $_.Name -ne 'TECHNICAL_DOC.md'
}

if ($files.Count -eq 0) {
    Write-Host "ℹ️ Yüklenecek kod dosyası bulunamadı." -ForegroundColor Yellow
    exit 0
}

foreach ($file in $files) {
    $relativePath = $file.FullName.Substring($localDir.Length).TrimStart('\', '/')
    $destSubDir = Split-Path -Path "$targetDir/$relativePath" -Parent
    $destSubDir = $destSubDir.Replace('\', '/')

    Write-Host "📤 Yükleniyor: $relativePath -> $destSubDir" -ForegroundColor Gray
    
    # Upload via cPanel UAPI Fileman::upload_files
    $uploadUrl = "https://$cpanelHost/execute/Fileman/upload_files"
    
    curl.exe -s -k -H "Authorization: $authHeader" -F "dir=$destSubDir" -F "file-1=@$($file.FullName);filename=$($file.Name)" -F "overwrite=1" $uploadUrl | Out-Null
}

Write-Host "✅ $ProjectName başarıyla cPanel'e yüklendi!" -ForegroundColor Green
