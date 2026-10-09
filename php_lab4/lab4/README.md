# Лабораторна робота №4 — Робота з кешем 

## Запуск

1. Посилання: <http://localhost:8000/index.html>.

## Структура

| Файл | Призначення |
|---|---|
| `index.html` | Тестова сторінка + «клієнтський код» (fetch, вимірювання часу, журнал) |
| `client/style.php` | Клієнтське кешування №1: CSS (`Cache-Control`, `Expires`, `Content-Type`) |
| `client/image.php` | Клієнтське кешування №2: зображення + `ETag`, `Last-Modified`, відповідь `304` |
| `src/http_cache.php` | Функція `serveCached()` — формує заголовки кешування |
| `src/FileCache.php` | Клас файлового кешу (get / set / delete / age, TTL через `filemtime`) |
| `src/StaticCache.php` | Кеш у статичній властивості класу (`remember`, `forget`, лічильники) |
| `generate-report.php` | Файловий кеш: `sleep(3)`, 1000 рядків, `cache/report.html`, TTL 10 хв |
| `session-cache.php` | Кеш у `$_SESSION`: `sleep(2)`, TTL 60 с |
| `static-cache.php` | Демонстрація кешу в статичній властивості |
| `clear-cache.php` | Очищення файлового кешу / кешу сесії |

## Як працює кожен підхід

**Клієнтське кешування.** Скрипт відправляє `Cache-Control: public, max-age=86400`, `Expires` (поточний час + 1 доба)
та `Content-Type`. Браузер зберігає ресурс і 24 години не звертається до сервера. Для зображення додатково
надсилаються `ETag` та `Last-Modified`: коли кеш «протух» або натиснуто F5, браузер надсилає
`If-None-Match`, а сервер відповідає `304 Not Modified` без тіла.

**Файловий кеш.** Ключ `report` → файл `cache/report.html`. Якщо файл існує і `time() - filemtime() < 600`,
повертається його вміст (Cache Hit), інакше виконується `sleep(3)` + генерація таблиці, файл перезаписується
(Cache Miss). Відповідь містить заголовки `X-Cache: HIT|MISS` і `X-Generation-Time-Ms`.

**Кеш у сесії.** Результат (`sleep(2)`) зберігається в `$_SESSION['rates_cache']` разом із часом створення.
Кеш індивідуальний для кожного користувача.

**Статична властивість.** `StaticCache::$storage` — масив у пам'яті процесу. Він живе лише протягом одного запиту,
тому демонстрація виконує 4 виклики в одному запиті: MISS → HIT → HIT → (`forget`) → MISS.
Між запитами такий кеш не зберігається — для цього потрібні файли, сесії, Redis/Memcached/APCu.


## Результати виконання

### Крок 1. Клієнтське кешування

![Ресурси беруться з кешу браузера](screenshots/01-client-memory-cache.png)
*Рис. 1 — CSS та зображення завантажуються з `(memory cache)`, запитів до сервера немає.*

![Заголовки style.php](screenshots/02-style-headers.png)
*Рис. 2 — Заголовки відповіді `style.php`: Cache-Control, Expires, Content-Type.*

![Заголовки image.php](screenshots/03-image-headers.png)
*Рис. 3 — Заголовки відповіді `image.php`: додатково ETag і Last-Modified.*

![Відповідь 304](screenshots/04-image-304.png)
*Рис. 4 — Повторна перевірка `image.php`: сервер відповідає 304 Not Modified.*

![Заголовки запиту](screenshots/05-image-request-headers.png)
*Рис. 5 — Браузер надсилає If-None-Match та If-Modified-Since.*

### Крок 2. Кеш у файл

![Звіт: Cache Miss](screenshots/06-report-miss.png)
*Рис. 6 — Перший запит: кеш відсутній, звіт згенеровано за ≈ 3 с.*

![Звіт: Cache Hit](screenshots/07-report-hit.png)
*Рис. 7 — Повторний запит: звіт узято з `cache/report.html`, там 1000 записів.*

![Файл кешу](screenshots/08-cache-file.png)
*Рис. 8 — Файл `cache/report.html` у проєкті.*

### Крок 3. Кеш у сесії

![Сесія: Miss](screenshots/09-session-miss.png)
*Рис. 9 — Перший запит: курси обчислено за ≈ 2 с.*

![Сесія: Hit](screenshots/10-session-hit.png)
*Рис. 10 — Повторний запит: дані взято з `$_SESSION`.*

![Сесія після очищення](screenshots/11-session-after-clear.png)
*Рис. 11 — Після очищення кешу курси обчислено заново, значення змінилися.*

### Кеш у статичній властивості класу

![Статичний кеш](screenshots/12-static-cache.png)
*Рис. 12 — MISS → HIT → HIT → (forget) → MISS у межах одного запиту.*

### Крок 4. Тестування

![Тестова сторінка](screenshots/13-index-page.png)
*Рис. 13 — Тестова сторінка `index.html`, яка викликає PHP-скрипти.*

![Автотест](screenshots/14-autotest.png)
*Рис. 14 — Автотест: файл 3045 → 21 мс (×145), сесія 2027 → 15 мс.*

![Звіт до очищення](screenshots/15-report-after-clear-1.png)
![Звіт після очищення](screenshots/16-report-after-clear-2.png)
*Рис. 15–16 — Після очищення кешу звіт згенеровано заново, дані в таблиці змінилися.*
