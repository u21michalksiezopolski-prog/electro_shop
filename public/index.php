<?php

require_once __DIR__ . '/../config.php';

$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

$request_uri = strtok($request_uri, '?');

$base_path = str_replace('\\', '/', dirname($script_name));
if ($base_path !== '/' && $base_path !== '\\') {
    if (strpos($request_uri, $base_path) === 0) {
        $request_uri = substr($request_uri, strlen($base_path));
    }
}

$static_extensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];
$path_parts = pathinfo($request_uri);
if (isset($path_parts['extension']) && in_array(strtolower($path_parts['extension']), $static_extensions)) {
    $file_path = __DIR__ . $request_uri;
    if (file_exists($file_path) && is_file($file_path)) {
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];
        $ext = strtolower($path_parts['extension']);
        $mime = $mime_types[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $mime);
        readfile($file_path);
        exit;
    }
}

$request_uri = rtrim($request_uri, '/') ?: '/';

$routes = require __DIR__ . '/../routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$route_key = $method . ':' . $request_uri;

if (isset($routes[$route_key])) {
    $handler = $routes[$route_key];
    call_user_func($handler);
    exit;
}

if (defined('APP_DEBUG') && APP_DEBUG) {
    error_log("Request URI: " . $request_uri);
    error_log("Method: " . $method);
    error_log("Route key: " . $route_key);
}

foreach ($routes as $pattern => $handler) {
    if (strpos($pattern, '{') === false) continue;
    
    list($route_method, $route_path) = explode(':', $pattern, 2);
    
    if ($route_method !== $method) continue;
    
    $regex = '#^' . preg_replace('#\{[^}]+\}#', '([^/]+)', $route_path) . '$#';
    
    if (preg_match($regex, $request_uri, $matches)) {
        array_shift($matches);
        call_user_func_array($handler, $matches);
        exit;
    }
}

http_response_code(404);
include __DIR__ . '/../views/404.php';
