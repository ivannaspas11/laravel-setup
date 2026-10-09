<?php
declare(strict_types=1);

// Просте файл. кеш. Ключ -> ім'я файлу, 
//значення -> вміст файлу, 
// TTL -> перевірка часу зміни файлу (filemtime)

final class FileCache
{
    public function __construct(
        private string $dir,
        private string $extension = 'cache'
    ) {
        if (!is_dir($this->dir) && !mkdir($this->dir, 0775, true) && !is_dir($this->dir)) {
            throw new RuntimeException('Не вдалося створити каталог кешу: ' . $this->dir);
        }
    }

    // Шлях до файлу кешу для ключа (небезпечні символи замінюються на "_")
    public function path(string $key): string
    {
        $safe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $key);
        return rtrim($this->dir, '/\\') . DIRECTORY_SEPARATOR . $safe . '.' . $this->extension;
    }

    // Вік кешу в секундах або null, якщо файлу немає
    public function age(string $key): ?int
    {
        $path = $this->path($key);
        clearstatcache(true, $path);
        $mtime = is_file($path) ? filemtime($path) : false;

        return $mtime === false ? null : max(0, time() - $mtime);
    }

    // Cache Hit: файл існує і TTL ще не минув
    public function isFresh(string $key, int $ttl): bool
    {
        $age = $this->age($key);
        return $age !== null && $age < $ttl;
    }

    // Повертає вміст кешу або null  
    public function get(string $key, int $ttl): ?string
    {
        if (!$this->isFresh($key, $ttl)) {
            return null;
        }

        $content = file_get_contents($this->path($key));
        return $content === false ? null : $content;
    }

    // Зберігає значення (LOCK_EX — щоб два запити не писали у файл одночасно). 
    public function set(string $key, string $content): void
    {
        if (file_put_contents($this->path($key), $content, LOCK_EX) === false) {
            throw new RuntimeException('Не вдалося записати кеш: ' . $this->path($key));
        }
    }

    // Інвалідація кешу
    public function delete(string $key): bool
    {
        $path = $this->path($key);
        return is_file($path) && unlink($path);
    }
}
