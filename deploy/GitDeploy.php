<?php

date_default_timezone_set('Europe/Paris');

set_include_path(get_include_path() . PATH_SEPARATOR . 'E:\Dev\wamp2.0\www\cherbouquin\deploy\phpseclib');

// $host = "37.59.62.193";
// $login = "root";
// $password = "1gGvzHYN";
// $remoteFolder = "/home/www.cherbouquin.fr/";
// $env = "kim";

$host = "46.105.8.132";
$login = "root";
$password = "7UqbxwEA";
$remoteFolder = "/var/www/html/beta.cherbouquin.fr/";
$env = "beta";

// $host = "46.105.8.132";
// $login = "root";
// $password = "7UqbxwEA";
// $remoteFolder = "/var/www/html/www.cherbouquin.fr/";
// $env = "prod";

$localFolder = "E:\\Dev\\wamp2.0\\www\\cherbouquin\\";
$localBackupFolder = "E:\\Temp\\CherBouquin\\BackupDeploy\\";

$revisionHash = "315a0c4e";
$filesString = "application/modules/member/controllers/ProfileController.php
application/modules/member/views/scripts/profile/delete.phtml
application/modules/member/views/scripts/profile/gravatar.phtml
library/Sb/Entity/Urls.php";

include('Net/SFTP.php');

$sftp = new Net_SFTP($host);
if (!$sftp->login($login, $password)) {
    exit('Login Failed');
}

$files = explode("\n", $filesString);

// Backup all files
foreach ($files as $file) {

    $today = new DateTime();

    // Get local backup folder
    $fullFileName = $localBackupFolder . $today->format("Ymd") . "-" . $revisionHash . "-" . $env . "\\" . $file;
    $fullFileName = str_replace("/", "\\", $fullFileName);

    // Test if file has not already been backuped
    if (!file_exists($fullFileName)) {

        // Build backup folder
        $folderParts = explode("\\", $fullFileName);
        $folderParts = array_slice($folderParts, 0, count($folderParts) - 1);
        $backupFolder = implode("\\", $folderParts);

        // Test if backup folder exits and create it if not
        if (!file_exists($backupFolder)) {
            mkdir($backupFolder, 0, true);
        }

        echo "Backuping " . $remoteFolder . $file . " to " . $fullFileName . "\n";

        // Backup the file
        $sftp->get($remoteFolder . $file, $fullFileName);
    }

}

// Pushing files to remote server
echo "Pushing files to remote server \n";
foreach ($files as $file) {

    // remote path
    $remoteFile = $remoteFolder . $file;

    // local path
    $localFile = $localFolder . $file;

    echo "Pushing " . $file . " to " . $remoteFile . "\n";

    $sftp->put($remoteFile, $localFile, NET_SFTP_LOCAL_FILE);
}
