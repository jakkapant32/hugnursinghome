<?php

function hug_supported_langs(): array
{
    return ['th', 'en'];
}

function hug_current_lang(): string
{
    $lang = $_COOKIE['hug_lang'] ?? 'th';

    return in_array($lang, hug_supported_langs(), true) ? $lang : 'th';
}

/** @return array<string,string> */
function hug_translations(): array
{
    static $loaded = null;
    if (is_array($loaded)) {
        return $loaded;
    }
    $lang = hug_current_lang();
    $path = __DIR__ . '/lang/' . $lang . '.php';
    $loaded = is_readable($path) ? require $path : require __DIR__ . '/lang/th.php';

    return $loaded;
}

function hug_t(string $key): string
{
    $all = hug_translations();

    return $all[$key] ?? $key;
}

function hug_handle_lang_switch(): void
{
    if (!isset($_GET['set_lang'])) {
        return;
    }
    $lang = $_GET['set_lang'];
    if (!in_array($lang, hug_supported_langs(), true)) {
        return;
    }
    setcookie('hug_lang', $lang, [
        'expires' => time() + 365 * 86400,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
    $_COOKIE['hug_lang'] = $lang;

    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = preg_replace('/([?&])set_lang=[^&]*&?/', '$1', $uri);
    $uri = rtrim($uri, '?&');
    if ($uri === '') {
        $uri = '/';
    }
    header('Location: ' . $uri);
    exit;
}

function hug_lang_url(string $lang): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/public/index.php';
    $uri = preg_replace('/([?&])set_lang=[^&]*&?/', '$1', $uri);
    $uri = rtrim($uri, '?&');
    $sep = str_contains($uri, '?') ? '&' : '?';

    return $uri . $sep . 'set_lang=' . urlencode($lang);
}

hug_handle_lang_switch();
