<?php

$fileuploads = ini_get('file_uploads');
$apcenabled = ini_get('apc.enabled');
$memorylimit = ini_get('memory_limit');

$allokay = true;
$message = [];
if (!empty($fileuploads)) {
    $allokay = false;
    $message[] = "Uploads are enabled and should be disabled.";
    $message[] = var_export($fileuploads, true);
}

if (!empty($apcenabled)) {
    $allokay = false;
    $message[] = "apc.enabled is not Off (0): ({$apcenabled})";
}

if ($memorylimit !== '256M') {
    $allokay = false;
    $message[] = "Memory limit not set to 256M: ({$memorylimit})";
}

if (php_sapi_name() === 'cli') {
    if ($allokay) {
        echo "OK\n";
        exit(0);
    }

    echo implode("\n", $message) . "\n";
    exit(1);
} else {
    if ($allokay) {
        header('HTTP/1.1 200 - OK');
        exit(0);
    }

    header('HTTP/1.1 500 - ' . implode(", ", $message));
    echo implode("<br>", $message);
    exit(1);
}