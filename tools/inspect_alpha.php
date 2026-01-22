<?php

$projectRoot = dirname(__DIR__);
$original = $projectRoot . '/public/assets/img/main_who_triangle.png';
$cacheDir = $projectRoot . '/storage/app/image-cache';

function rgbaAt(string $file, int $x, int $y): ?array {
    if (!is_file($file)) return null;
    $info = @getimagesize($file);
    if (!$info) return null;
    $type = $info[2] ?? null;

    $img = match ($type) {
        IMAGETYPE_PNG => @imagecreatefrompng($file),
        IMAGETYPE_JPEG => @imagecreatefromjpeg($file),
        IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file) : false,
        default => false,
    };
    if (!$img) return null;

    $w = imagesx($img);
    $h = imagesy($img);
    $x = max(0, min($w - 1, $x));
    $y = max(0, min($h - 1, $y));

    $px = imagecolorat($img, $x, $y);
    if (function_exists('imageistruecolor') && !imageistruecolor($img)) {
        $c = imagecolorsforindex($img, $px);
        $r = (int) ($c['red'] ?? 0);
        $g = (int) ($c['green'] ?? 0);
        $b = (int) ($c['blue'] ?? 0);
        $a = (int) ($c['alpha'] ?? 0);
    } else {
        // Truecolor: alpha is 0..127 (0 opaque, 127 transparent)
        $a = ($px & 0x7F000000) >> 24;
        $r = ($px >> 16) & 0xFF;
        $g = ($px >> 8) & 0xFF;
        $b = $px & 0xFF;
    }

    imagedestroy($img);
    return [$r, $g, $b, $a];
}

function loadImageForScan(string $file): ?array {
    if (!is_file($file)) return null;
    $info = @getimagesize($file);
    if (!$info) return null;
    $type = $info[2] ?? null;

    $img = match ($type) {
        IMAGETYPE_PNG => @imagecreatefrompng($file),
        IMAGETYPE_JPEG => @imagecreatefromjpeg($file),
        IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file) : false,
        default => false,
    };

    if (!$img) return null;

    return [$img, (int) $info[0], (int) $info[1], $type];
}

function findNonTransparentPixel(string $file): ?array {
    $info = @getimagesize($file);
    if (!$info) return null;
    $type = $info[2] ?? null;

    $img = match ($type) {
        IMAGETYPE_PNG => @imagecreatefrompng($file),
        IMAGETYPE_JPEG => @imagecreatefromjpeg($file),
        IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file) : false,
        default => false,
    };
    if (!$img) return null;

    $w = imagesx($img);
    $h = imagesy($img);

    $best = null;
    // Scan a coarse grid to find the first pixel that isn't fully transparent.
    $step = max(1, (int) floor(min($w, $h) / 200));
    for ($y = 0; $y < $h; $y += $step) {
        for ($x = 0; $x < $w; $x += $step) {
            $rgba = rgbaAt($file, $x, $y);
            if (!$rgba) continue;
            [$r, $g, $b, $a] = $rgba;
            if ($a < 127) {
                $best = [$x, $y, $r, $g, $b, $a];
                break 2;
            }
        }
    }

    imagedestroy($img);
    return $best;
}

function scanStats(string $file): ?array {
    $loaded = loadImageForScan($file);
    if (!$loaded) return null;
    [$img, $w, $h] = $loaded;

    $step = max(1, (int) floor(min($w, $h) / 250));
    $minAlpha = 127;
    $minAlphaHit = null;
    $blackHit = null;

    $isTrueColor = function_exists('imageistruecolor') ? imageistruecolor($img) : true;

    for ($y = 0; $y < $h; $y += $step) {
        for ($x = 0; $x < $w; $x += $step) {
            $px = imagecolorat($img, $x, $y);
            if (!$isTrueColor) {
                $c = imagecolorsforindex($img, $px);
                $r = (int) ($c['red'] ?? 0);
                $g = (int) ($c['green'] ?? 0);
                $b = (int) ($c['blue'] ?? 0);
                $a = (int) ($c['alpha'] ?? 0);
            } else {
                $a = ($px & 0x7F000000) >> 24;
                $r = ($px >> 16) & 0xFF;
                $g = ($px >> 8) & 0xFF;
                $b = $px & 0xFF;
            }

            if ($a < $minAlpha) {
                $minAlpha = $a;
                $minAlphaHit = [$x, $y, $r, $g, $b, $a];
            }

            if ($blackHit === null && $a < 127 && $r < 5 && $g < 5 && $b < 5) {
                $blackHit = [$x, $y, $r, $g, $b, $a];
            }
        }
    }

    imagedestroy($img);

    return [
        'step' => $step,
        'minAlphaHit' => $minAlphaHit,
        'blackHit' => $blackHit,
    ];
}

function latestPngInCache(string $cacheDir): ?string {
    if (!is_dir($cacheDir)) return null;
    $files = glob($cacheDir . '/*.png');
    if (!$files) return null;
    usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));
    return $files[0] ?? null;
}

$latest = latestPngInCache($cacheDir);

$targets = [
    'original' => $original,
    'latest_cache_png' => $latest,
];

foreach ($targets as $label => $file) {
    echo "\n== $label ==\n";
    echo "file: " . ($file ?: 'N/A') . "\n";
    if (!$file || !is_file($file)) {
        echo "missing\n";
        continue;
    }

    $info = getimagesize($file);
    echo "type: " . image_type_to_mime_type($info[2]) . "\n";
    echo "size: {$info[0]}x{$info[1]}\n";
    echo "bytes: " . filesize($file) . "\n";

    $points = [
        [0, 0, 'top-left'],
        [$info[0]-1, 0, 'top-right'],
        [0, $info[1]-1, 'bottom-left'],
        [$info[0]-1, $info[1]-1, 'bottom-right'],
        [(int) floor($info[0] / 2), (int) floor($info[1] / 2), 'center'],
        [10, (int) floor($info[1] / 2), 'mid-left'],
        [$info[0]-11, (int) floor($info[1] / 2), 'mid-right'],
        [(int) floor($info[0] / 2), 10, 'mid-top'],
        [(int) floor($info[0] / 2), $info[1]-11, 'mid-bottom'],
    ];

    foreach ($points as [$x, $y, $name]) {
        $rgba = rgbaAt($file, $x, $y);
        if ($rgba === null) {
            echo sprintf("rgba  %-12s: N/A\n", $name);
            continue;
        }
        [$r, $g, $b, $a] = $rgba;
        echo sprintf("rgba  %-12s: r=%3d g=%3d b=%3d a=%3d (a:0 opaque,127 transparent)\n", $name, $r, $g, $b, $a);
    }

    $hit = findNonTransparentPixel($file);
    if ($hit) {
        [$x, $y, $r, $g, $b, $a] = $hit;
        echo sprintf("first non-transparent sample: x=%d y=%d r=%d g=%d b=%d a=%d\n", $x, $y, $r, $g, $b, $a);
    } else {
        echo "first non-transparent sample: none found (maybe fully transparent or scan missed)\n";
    }

    $stats = scanStats($file);
    if ($stats) {
        echo "scan step: {$stats['step']}px\n";
        if ($stats['minAlphaHit']) {
            [$x, $y, $r, $g, $b, $a] = $stats['minAlphaHit'];
            echo sprintf("most opaque sample: x=%d y=%d r=%d g=%d b=%d a=%d\n", $x, $y, $r, $g, $b, $a);
        }
        if ($stats['blackHit']) {
            [$x, $y, $r, $g, $b, $a] = $stats['blackHit'];
            echo sprintf("found near-black pixel: x=%d y=%d r=%d g=%d b=%d a=%d\n", $x, $y, $r, $g, $b, $a);
        } else {
            echo "found near-black pixel: none in scan\n";
        }
    }
}
