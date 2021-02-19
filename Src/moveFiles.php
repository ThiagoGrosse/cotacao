<?php

namespace Src;

use Slim\Http\UploadedFile;


/**
 * Move arquivo do pasta TEMP para o diretório /Uploads
 *
 * @param [type] $directory
 * @param UploadedFile $uploadedFile
 * @return void
 */

function moveUploadedFile($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8));
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}
