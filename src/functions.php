<?php
namespace App;

function getSvgIcon(string $iconName): string {
    $filename = __DIR__ . "/../public/assets/images/svgs/{$iconName}.svg";

    if (file_exists($filename)) {
        $svg = file_get_contents($filename);
        return $svg;
    } else {
        return '';
    }
}

function redirect(string $url): void {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $httpHost = $_SERVER['HTTP_HOST'];
    header("Location: {$protocol}{$httpHost}{$url}");
    exit();
}

function customGetEnv(string $varname, array|false $parsedIni): string|null {
    if (!isset($_ENV[$varname])) {
        if ($parsedIni === false) {
            return '';
        } else {
            return $parsedIni[$varname];
        }
    } else {
        return $_ENV[$varname];
    }
}
?>