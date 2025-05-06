<?php
    if (preg_match('/\.(?:png|jpg|svg|webp|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
        return false;
    }

    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);

    $path = trim($path, '/');
    $pathElements = explode('/', $path);

    for ($i = 0; $i < count($pathElements); $i++) {
        $loopPath = "/public/pages/";
        $iCopy = $i;
        
        while ($iCopy >= 0) {
            if ($pathElements[$iCopy] == '') {
                break;
            }
            if (($iCopy - 1) < 0) {
                $loopPath .= $pathElements[(count($pathElements) - 1) - $iCopy];
            } else {
                $loopPath .= $pathElements[(count($pathElements) - 1) - $iCopy] . '/';
            }
            $iCopy--;
        }
        
        if (!is_dir(__DIR__ . $loopPath)) {
            if (!str_ends_with($loopPath, '.php')) {
                $loopPath = trim($loopPath, '/');
                $loopPath .= '.php';
            }
            
            if (file_exists(__DIR__ . '/' . $loopPath)) {
                require __DIR__ . '/' . $loopPath;
                return true;
            }
        } else {
            require __DIR__ . $loopPath . "/index.php";
            return true;
        }
    }


    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "Страница не найдена";
    exit();
?>
