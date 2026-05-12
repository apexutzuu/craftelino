# Deploy script - uploads files to cPanel via FTP
# Usage: 
#   .\deploy.ps1              - deploy only changed files (git tracked)
#   .\deploy.ps1 -All         - deploy ALL files
#   .\deploy.ps1 -Files "catalog/view/theme/promobrand/template/common/header.twig","config.php"

param(
    [string]$FtpHost = "ftp.20s.ro",
    [string]$FtpUser = "ssh@craftelino.ro",
    [string]$FtpPass = 'Master@xxl',
    [string]$RemotePath = "/public_html/craftelino.ro",
    [string]$LocalPath = "d:\proiecte\site produse promotionale",
    [switch]$All,
    [string[]]$Files
)

$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

Write-Host "=== Craftelino Deploy ===" -ForegroundColor Cyan
Write-Host "Host: $FtpHost | Remote: $RemotePath" -ForegroundColor Gray
Write-Host ""

function Upload-FtpFile {
    param([string]$LocalFile, [string]$RemoteFile)
    
    $remoteDir = Split-Path $RemoteFile -Parent
    $remoteDirFtp = "ftp://${FtpHost}${remoteDir}/" -replace '\\', '/'
    
    try {
        $mkdirRequest = [System.Net.FtpWebRequest]::Create($remoteDirFtp)
        $mkdirRequest.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $mkdirRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
        $mkdirResponse = $mkdirRequest.GetResponse()
        $mkdirResponse.Close()
    } catch {}

    $remoteFtp = "ftp://${FtpHost}${RemoteFile}" -replace '\\', '/'
    
    try {
        $ftpRequest = [System.Net.FtpWebRequest]::Create($remoteFtp)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true

        $fileContent = [System.IO.File]::ReadAllBytes($LocalFile)
        $ftpRequest.ContentLength = $fileContent.Length
        
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()

        $response = $ftpRequest.GetResponse()
        Write-Host "  OK: $($RemoteFile.Replace($RemotePath + '/', ''))" -ForegroundColor Green
        $response.Close()
        return $true
    }
    catch {
        Write-Host "  FAIL: $($RemoteFile.Replace($RemotePath + '/', '')) - $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

# Determine which files to upload
$filesToDeploy = @()

if ($Files) {
    $filesToDeploy = $Files
    Write-Host "Deploying specified files:" -ForegroundColor Yellow
} elseif ($All) {
    Set-Location $LocalPath
    $filesToDeploy = git ls-files --cached --others --exclude-standard 2>$null
    if (-not $filesToDeploy) {
        $filesToDeploy = Get-ChildItem -Recurse -File | Where-Object { 
            $_.FullName -notmatch '\\\.git\\' -and
            $_.FullName -notmatch '\\system\\storage\\(cache|logs|session)\\' -and
            $_.FullName -notmatch '\\assets\\' -and
            $_.FullName -notmatch '\\terminals\\' -and
            $_.FullName -notmatch '\\agent-transcripts\\' -and
            $_.Name -notmatch '^(fix_|debug_|setup_fix|reset_admin|unlock_admin|delete_install|set_logo)' 
        } | ForEach-Object { $_.FullName.Replace("$LocalPath\", "").Replace("\", "/") }
    }
    Write-Host "Deploying ALL files ($($filesToDeploy.Count)):" -ForegroundColor Yellow
} else {
    Set-Location $LocalPath
    $filesToDeploy = @()
    $staged = git diff --cached --name-only 2>$null
    $modified = git diff --name-only 2>$null
    $untracked = git ls-files --others --exclude-standard 2>$null
    if ($staged) { $filesToDeploy += $staged }
    if ($modified) { $filesToDeploy += $modified }
    if ($untracked) { $filesToDeploy += $untracked }
    $filesToDeploy = $filesToDeploy | Select-Object -Unique
    
    if (-not $filesToDeploy -or $filesToDeploy.Count -eq 0) {
        Write-Host "No changed files. Use -All to deploy everything or -Files to specify files." -ForegroundColor Yellow
        exit 0
    }
    Write-Host "Deploying changed files ($($filesToDeploy.Count)):" -ForegroundColor Yellow
}

$ok = 0; $fail = 0

foreach ($file in $filesToDeploy) {
    $localFile = Join-Path $LocalPath $file
    if (-not (Test-Path $localFile)) { continue }
    
    $remoteFile = "$RemotePath/$($file -replace '\\', '/')"
    
    if (Upload-FtpFile -LocalFile $localFile -RemoteFile $remoteFile) {
        $ok++
    } else {
        $fail++
    }
}

Write-Host ""
Write-Host "Done! Uploaded: $ok | Failed: $fail" -ForegroundColor Cyan
