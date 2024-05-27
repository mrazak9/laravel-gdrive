<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

function initializeGoogleDrive()
{
    $client = new Client();
    $client->setClientId('1039391466702-hq1a2ukf75pjt8ecmq5r4amujqcvhmrm.apps.googleusercontent.com');
    $client->setClientSecret('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');
    $client->refreshToken('1//04OWHqN6yOxLACgYIARAAGAQSNwF-L9IrYV8r-3XMW43txwME_rrBjyJ0mSBWZWVb_b4s31gau-qfylnDlsGHaPSziLAki_NTUvU');
    $client->addScope(Drive::DRIVE);

    return new Drive($client);
}

try {
    // Inisialisasi Google Drive Service
    $driveService = initializeGoogleDrive();

    // Dapatkan ID Folder dari environment variable
    $folderId = '1F7a-XrHYqQNpsrdTWMTv_lzZ9PJ060LL';

    // Dapatkan informasi folder
    $folder = $driveService->files->listFiles();

    // Cetak informasi folder
    echo "Folder Info:\n";
    print_r($folder);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
