<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    private const CACHE_VERSION = 3;

    public function show(Request $request, string $path): BinaryFileResponse
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        // Restrict to public/assets/img only.
        $baseDir = public_path('assets/img');
        $realBase = realpath($baseDir);
        if ($realBase === false) {
            abort(404);
        }

        $requestedFile = public_path($path);
        $realFile = realpath($requestedFile);
        if ($realFile === false || !str_starts_with($realFile, $realBase . DIRECTORY_SEPARATOR)) {
            abort(404);
        }

        $ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            abort(404);
        }

        $maxDim = 2400;
        $w = (int) $request->query('w', 0);
        $h = (int) $request->query('h', 0);
        $w = $w > 0 ? min($w, $maxDim) : 0;
        $h = $h > 0 ? min($h, $maxDim) : 0;

        $q = (int) $request->query('q', 70);
        $q = max(40, min(90, $q));

        $accept = strtolower((string) $request->header('Accept', ''));
        $wantWebp = str_contains($accept, 'image/webp');
        $canWebp = function_exists('imagewebp');

        $format = strtolower((string) $request->query('fm', 'auto'));
        if (!in_array($format, ['auto', 'jpg', 'jpeg', 'png', 'webp'], true)) {
            $format = 'auto';
        }

        $outExt = match ($format) {
            'jpg', 'jpeg' => 'jpg',
            'png' => 'png',
            'webp' => 'webp',
            // Safer default: keep PNGs as PNG (preserve transparency reliably).
            default => ($ext !== 'png' && $wantWebp && $canWebp) ? 'webp' : (($ext === 'png') ? 'png' : 'jpg'),
        };

        $fileVersion = (string) @filemtime($realFile);
        $cacheKey = sha1($realFile . '|' . $fileVersion . '|v=' . self::CACHE_VERSION . "|w=$w|h=$h|q=$q|out=$outExt");
        $cacheDir = storage_path('app/image-cache');
        $cachePath = $cacheDir . DIRECTORY_SEPARATOR . $cacheKey . '.' . $outExt;

        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }

        if (!is_file($cachePath)) {
            // If GD isn't available, fall back to original file.
            if (!function_exists('imagecreatetruecolor')) {
                return $this->fileResponse($realFile);
            }

            $src = match ($ext) {
                'png' => @imagecreatefrompng($realFile),
                'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($realFile) : false,
                default => @imagecreatefromjpeg($realFile),
            };

            if (!$src) {
                return $this->fileResponse($realFile);
            }

            // Fix EXIF orientation for JPEGs when possible.
            if (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('exif_read_data')) {
                try {
                    $exif = @exif_read_data($realFile);
                    $orientation = (int) ($exif['Orientation'] ?? 1);
                    $src = $this->applyExifOrientation($src, $orientation);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            $srcW = imagesx($src);
            $srcH = imagesy($src);

            // Preserve alpha for palette PNGs by converting to truecolor.
            if ($ext === 'png') {
                if (function_exists('imageistruecolor') && !imageistruecolor($src)) {
                    $tmp = imagecreatetruecolor($srcW, $srcH);
                    imagealphablending($tmp, false);
                    imagesavealpha($tmp, true);
                    $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
                    imagefilledrectangle($tmp, 0, 0, $srcW, $srcH, $transparent);
                    imagecopy($tmp, $src, 0, 0, 0, 0, $srcW, $srcH);
                    imagedestroy($src);
                    $src = $tmp;
                }
                imagealphablending($src, true);
                imagesavealpha($src, true);
            }

            // If no resizing requested, still allow format conversion.
            if ($w === 0 && $h === 0) {
                $w = $srcW;
                $h = $srcH;
            } else {
                // Keep aspect ratio, fit within w/h.
                if ($w === 0) {
                    $w = (int) round($srcW * ($h / max(1, $srcH)));
                } elseif ($h === 0) {
                    $h = (int) round($srcH * ($w / max(1, $srcW)));
                } else {
                    $scale = min($w / max(1, $srcW), $h / max(1, $srcH));
                    $w = (int) max(1, floor($srcW * $scale));
                    $h = (int) max(1, floor($srcH * $scale));
                }
            }

            $dst = imagecreatetruecolor($w, $h);
            if (in_array($outExt, ['png', 'webp'], true)) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefilledrectangle($dst, 0, 0, $w, $h, $transparent);
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $srcW, $srcH);

            // Ensure cache file is written.
            $ok = match ($outExt) {
                'webp' => $canWebp ? @imagewebp($dst, $cachePath, $q) : false,
                'png' => @imagepng($dst, $cachePath, (int) round((100 - $q) / 10)),
                default => @imagejpeg($dst, $cachePath, $q),
            };

            imagedestroy($dst);
            imagedestroy($src);

            if (!$ok || !is_file($cachePath)) {
                @unlink($cachePath);
                return $this->fileResponse($realFile);
            }
        }

        $response = $this->fileResponse($cachePath);

        // Cache aggressively; URLs vary by w/h/q and file mtime (via cacheKey).
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        $response->headers->set('Vary', 'Accept');

        return $response;
    }

    private function fileResponse(string $file): BinaryFileResponse
    {
        return response()->file($file);
    }

    private function applyExifOrientation($img, int $orientation)
    {
        return match ($orientation) {
            3 => imagerotate($img, 180, 0),
            6 => imagerotate($img, -90, 0),
            8 => imagerotate($img, 90, 0),
            default => $img,
        };
    }
}
