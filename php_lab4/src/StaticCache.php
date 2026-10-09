<?php
declare(strict_types=1);

/**
 * Кеш у статичній властивості класу (in-memory).
 *
 * ВАЖЛИВО: статична властивість живе лише в межах ОДНОГО PHP-процесу/запиту.
 * Коли запит завершується, пам'ять очищується. Тому такий кеш корисний,
 * щоб не виконувати той самий важкий виклик кілька разів за один запит
 * (наприклад, в різних частинах шаблону). Між запитами дані зберігають
 * файли, сесії, Redis/Memcached, APCu.
 */
final class StaticCache
{
    /** @var array<string, array{value: mixed, expires: int}> */
    private static array $storage = [];
    private static int $hits = 0;
    private static int $misses = 0;

    public static function has(string $key): bool
    {
        return isset(self::$storage[$key]) && self::$storage[$key]['expires'] > time();
    }

    public static function get(string $key): mixed
    {
        return self::has($key) ? self::$storage[$key]['value'] : null;
    }

    public static function set(string $key, mixed $value, int $ttl): void
    {
        self::$storage[$key] = ['value' => $value, 'expires' => time() + $ttl];
    }

    /**
     * Повертає значення з кешу, а якщо його немає — викликає $producer
     * і зберігає результат на $ttl секунд.
     */
    public static function remember(string $key, int $ttl, callable $producer): mixed
    {
        if (self::has($key)) {
            self::$hits++;
            return self::$storage[$key]['value'];
        }

        self::$misses++;
        $value = $producer();
        self::set($key, $value, $ttl);

        return $value;
    }

    public static function forget(string $key): void
    {
        unset(self::$storage[$key]);
    }

    public static function flush(): void
    {
        self::$storage = [];
    }

    public static function hits(): int   { return self::$hits; }
    public static function misses(): int { return self::$misses; }
}
