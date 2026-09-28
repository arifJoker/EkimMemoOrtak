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
$tempZip = Join-Path $PSScriptRoot "..\temp_deploy.zip"

Write-Host "🚀 $ProjectName canlı sunucuya deploy ediliyor..." -ForegroundColor Cyan

# 1. Zip oluştur
if (Test-Path $tempZip) { Remove-Item $tempZip -Force }
Compress-Archive -Path "$localDir\*" -DestinationPath $tempZip -Force

# 2. cPanel'e Zip yükle
Write-Host "📤 Zip paketi sunucuya yükleniyor..." -ForegroundColor Gray
$uploadUrl = "https://$cpanelHost/execute/Fileman/upload_files"
$uploadRes = curl.exe -s -k -H "Authorization: $authHeader" -F "dir=$targetDir" -F "file-1=@$tempZip;filename=deploy.zip" -F "overwrite=1" $uploadUrl

# 3. Zip dosyasını sunucuda aç (Extract)
Write-Host "📦 Sunucuda arşiv açılıyor (Extract)..." -ForegroundColor Gray
$extractUrl = "https://$cpanelHost/json-api/cpanel?cpanel_jsonapi_user=$cpanelUser&cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=Fileman&cpanel_jsonapi_func=fileop&op=extract&sourcefiles=$targetDir/deploy.zip&destfiles=$targetDir&dir=$targetDir"
$extractRes = curl.exe -s -k -H "Authorization: $authHeader" $extractUrl

# 4. Sunucudaki geçici deploy.zip dosyasını temizle
$deleteUrl = "https://$cpanelHost/json-api/cpanel?cpanel_jsonapi_user=$cpanelUser&cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=Fileman&cpanel_jsonapi_func=fileop&op=unlink&sourcefiles=$targetDir/deploy.zip&dir=$targetDir"
curl.exe -s -k -H "Authorization: $authHeader" $deleteUrl | Out-Null

# 5. Yerel zip'i temizle
if (Test-Path $tempZip) { Remove-Item $tempZip -Force }

Write-Host "✅ $ProjectName başarıyla canlı sunucuya aktarıldı ve yayınlandı!" -ForegroundColor Green
