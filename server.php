<?php

$publicPath = is_dir(__DIR__ . '/public') ? __DIR__ . '/public' : getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// If the file exists in public/
if ($uri !== '/' && file_exists($publicPath . $uri)) {
    // If it's a static hashed asset from Vite build, optimize delivery
    if (str_starts_with($uri, '/build/assets/')) {
        $filePath = $publicPath . $uri;
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mime = match ($ext) {
            'js' => 'application/javascript; charset=utf-8',
            'css' => 'text/css; charset=utf-8',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'woff2' => 'font/woff2',
            'woff' => 'font/woff',
            default => mime_content_type($filePath) ?: 'application/octet-stream',
        };

        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=31536000, immutable');

        // Serve pre-compressed Gzip asset directly if accepted by client
        $encodings = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
        if (str_contains($encodings, 'gzip')) {
            $gzPath = $filePath . '.gz';
            if (file_exists($gzPath)) {
                header('Content-Encoding: gzip');
                header('Vary: Accept-Encoding');
                header('Content-Length: ' . filesize($gzPath));
                readfile($gzPath);
                exit;
            } elseif (in_array($ext, ['js', 'css', 'svg'])) {
                $content = file_get_contents($filePath);
                header('Content-Encoding: gzip');
                header('Vary: Accept-Encoding');
                echo gzencode($content, 6);
                exit;
            }
        }

        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    return false;
}

require_once $publicPath . '/index.php';
