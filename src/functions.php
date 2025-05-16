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

function capitalizeFirstLetter(string $str): string {
    return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
}

function veryfiyCaptcha(string $hCaptchaSecret, string $hCaptchaResponse): bool {
    $data = [
        "secret" => $hCaptchaSecret,
        "response" => $hCaptchaResponse
    ];

    $curl = curl_init("https://hcaptcha.com/siteverify");

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    curl_close($curl);
    if (!$response) {
        return false;
    }

    $responseData = json_decode($response);

    if (!$responseData) {
        return false;
    }

    if ($responseData->success) {
        return true;
    } else {
        return false;
    }
}
?>