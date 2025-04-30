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
?>