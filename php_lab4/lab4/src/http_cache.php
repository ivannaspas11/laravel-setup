<?php
declare(strict_types=1);

// Віддає статичний файл із заголовками клієнтського кешування.
 
 // @param string $file        шлях до файлу
 // @param string $mime        MIME-тип (Content-Type)
 // @param int    $maxAge      час життя кешу в секундах (86400 = 1 доба)
 // @param bool   $validators  додати ETag / Last-Modified та відповідати 304 Not Modified
 
function serveCached(string $file, string $mime, int $maxAge = 86400, bool $validators = false): void
{
    if (!is_file($file)) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        exit('Файл не знайдено');
    }

    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=' . $maxAge);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');

    if ($validators) {
        $mtime = (int) filemtime($file);
        $etag  = '"' . md5_file($file) . '"';

        header('ETag: ' . $etag);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');

        $ifNoneMatch     = $_SERVER['HTTP_IF_NONE_MATCH'] ?? null;
        $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? null;

        if ($ifNoneMatch !== null) {
            // у заголовку може бути список ETag і префікс W
            $tags = array_map(
                static fn(string $t): string => preg_replace('/^W\//', '', trim($t)),
                explode(',', $ifNoneMatch)
            );
            $notModified = in_array($etag, $tags, true) || in_array('*', $tags, true);
        } else {
            $notModified = $ifModifiedSince !== null
                && ($since = strtotime($ifModifiedSince)) !== false
                && $since >= $mtime;
        }

        if ($notModified) {
            http_response_code(304); // тіло не передається — браузер бере копію зі свого кешу
            exit;
        }
    }

    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}
