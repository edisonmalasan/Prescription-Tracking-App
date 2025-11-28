<?php
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

$rootDir = __DIR__;

$requestPath = trim($requestPath, '/');


function serveStaticFile($filePath) {
    if (!file_exists($filePath) || !is_file($filePath)) {
        return false;
    }

    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    $contentTypes = [
        'html' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];
    
    $contentType = $contentTypes[$ext] ?? 'text/plain';
    header('Content-Type: ' . $contentType . '; charset=utf-8');
  
    readfile($filePath);
    return true;
}


// serve login.html 
if ($requestPath === '' || $requestPath === false) {
    $file = $rootDir . '/public/login.html';
    if (serveStaticFile($file)) {
        exit;
    }
}

// public routes
$cleanRoutes = [
    'login' => '/public/login.html',
    'register' => '/public/register.html',
    'home' => '/public/index.html',
];

if (isset($cleanRoutes[$requestPath])) {
    $file = $rootDir . $cleanRoutes[$requestPath];
    if (serveStaticFile($file)) {
        exit;
    }
}

// staticc assets
if (strpos($requestPath, 'assets/') === 0) {
    $assetPath = substr($requestPath, 7);
    $file = $rootDir . '/public/assets/' . $assetPath;
    if (serveStaticFile($file)) {
        exit;
    }
}

// serve public files
if (strpos($requestPath, 'public/') === 0) {
    $publicPath = substr($requestPath, 7);
    $file = $rootDir . '/public/' . $publicPath;
    if (serveStaticFile($file)) {
        exit;
    }
}

// serve admin frontend files: /admin/frontend/* → admin/frontend/*
if (strpos($requestPath, 'admin/frontend/') === 0) {
    $adminPath = substr($requestPath, 15); 
    $file = $rootDir . '/admin/frontend/' . $adminPath;
    if (serveStaticFile($file)) {
        exit;
    }
}

// serve admin frontend routes: /admin/frontend → admin/frontend/index.html
if ($requestPath === 'admin/frontend' || $requestPath === 'admin/frontend/') {
    $file = $rootDir . '/admin/frontend/index.html';
    if (serveStaticFile($file)) {
        exit;
    }
}

// api routes: /api/* → src/api/*
if (strpos($requestPath, 'api/') === 0) {
    $apiPath = substr($requestPath, 4);
    $file = $rootDir . '/src/api/' . $apiPath;

    if (!pathinfo($file, PATHINFO_EXTENSION)) {
        $file = $file . '.php';
    }
    
    if (file_exists($file) && is_file($file)) {
        return false;
    }
}

//view roots: /views/* → src/views/*
if (strpos($requestPath, 'views/') === 0) {
    $viewPath = substr($requestPath, 6);
    $file = $rootDir . '/src/views/' . $viewPath;
    
    if (!pathinfo($file, PATHINFO_EXTENSION)) {
        $file = $file . '.php';
    }
    
    if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $_SERVER['SCRIPT_NAME'] = $file;
        $_SERVER['PHP_SELF'] = $file;
        chdir(dirname($file));
        include $file;
        exit;
    }
}

// serve src directory files (with src/ prefix)
if (strpos($requestPath, 'src/') === 0) {
    $srcPath = substr($requestPath, 4);
    $file = $rootDir . '/src/' . $srcPath;
    
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        if (file_exists($file) && is_file($file)) {
            $_SERVER['SCRIPT_NAME'] = $file;
            $_SERVER['PHP_SELF'] = $file;
            chdir(dirname($file));
            include $file;
            exit;
        }
    } else {
        if (serveStaticFile($file)) {
            exit;
        }
    }
}

$publicFile = $rootDir . '/public/' . $requestPath;
if (file_exists($publicFile) && is_file($publicFile)) {
    if (serveStaticFile($publicFile)) {
        exit;
    }
}

$srcFile = $rootDir . '/src/' . $requestPath;
if (file_exists($srcFile) && is_file($srcFile)) {
    if (pathinfo($srcFile, PATHINFO_EXTENSION) === 'php') {
        $_SERVER['SCRIPT_NAME'] = $srcFile;
        $_SERVER['PHP_SELF'] = $srcFile;
        chdir(dirname($srcFile));
        include $srcFile;
        exit;
    } else {
        if (serveStaticFile($srcFile)) {
            exit;
        }
    }
}

// file not found
http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }
        .error-container {
            text-align: center;
            padding: 40px;
        }
        h1 {
            font-size: 72px;
            margin: 0;
            font-weight: 700;
        }
        h2 {
            font-size: 24px;
            margin: 20px 0;
            font-weight: 400;
        }
        a {
            color: #fff;
            text-decoration: underline;
        }
        a:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The requested page could not be found.</p>
        <p><a href="/">Return to Home</a></p>
    </div>
</body>
</html>
<?php
return true;
