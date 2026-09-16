<?php
/**
 * Router สำหรับ PHP built-in server:
 * php -S localhost:8080 router.php
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$root = __DIR__;

if ($uri === '/' || $uri === '') {
    require $root . '/public/index.php';
    return true;
}

$file = $root . $uri;
if ($uri !== '/' && is_file($file)) {
    return false;
}

if (preg_match('#^/public/#', $uri) && is_file($file)) {
    return false;
}

if (is_file($root . '/public' . $uri)) {
    require $root . '/public' . $uri;
    return true;
}

http_response_code(404);
echo 'Not Found';
return true;
