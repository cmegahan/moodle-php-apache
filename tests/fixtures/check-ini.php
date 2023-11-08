<?php

$allokay = true;
$message = [];

$fileuploads = ini_get('file_uploads');
if (!empty($fileuploads)) {
    $allokay = false;
    $message[] = "Uploads are enabled and should be disabled.";
    $message[] = var_export($fileuploads, true);
}

$memorylimit = ini_get('memory_limit');
if ($memorylimit !== '256M') {
    $allokay = false;
    $message[] = "Memory limit not set to 256M: ({$memorylimit})";
}

$maxuploadsize = ini_get('upload_max_filesize');
if ($maxuploadsize !== '20M') {
    $allokay = false;
    $message[] = "Maximum upload size not set to 20M: ({$maxuploadsize})";
}

$postmaxsize = ini_get('post_max_size');
if ($postmaxsize !== '206M') {
    $allokay = false;
    $message[] = "Maximum post size not set to 206M: ({$postmaxsize})";
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
