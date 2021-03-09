<?php

namespace Src;

function removerArquivosAntigos()
{

    $pasta = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Uploads' . DIRECTORY_SEPARATOR;
    $dias = 2;

    if (!file_exists($pasta)) {

        return;
    }

    $intervalo = strtotime("-{$dias} days");
    try {
        /** cycle through all files in the directory **/
        foreach (glob($pasta . '*') as $arquivo) {
            chmod($arquivo, 0777);
            /** if file is 24 hours (86400 seconds) old then delete it **/
            if (filemtime($arquivo) <= $intervalo  && is_file($arquivo)) {
                unlink($arquivo);
            } elseif (is_dir($arquivo)) {
                removerArquivosAntigos($arquivo . DIRECTORY_SEPARATOR, $dias);
            }
        }
    } catch (\Throwable $th) {
        if (php_sapi_name() === 'cli') {
            var_dump($th);
        }
    }
}
