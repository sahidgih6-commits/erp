<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    protected $disk;

    public function __construct()
    {
        $this->disk = env('CDN_ENABLED', false) ? 'images' : 'public';
    }

    /**
     * Upload and optimize an image
     */
    public function upload(UploadedFile $file, string $path = 'uploads', array $options = []): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $path . '/' . $filename;

        // Store file to configured disk
        $file->storeAs($path, $filename, $this->disk);

        return $this->getUrl($fullPath);
    }

    /**
     * Store an image and return the path
     */
    public function store(UploadedFile $file, string $path = 'uploads'): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Store file to configured disk
        $file->storeAs($path, $filename, $this->disk);

        return $path . '/' . $filename;
    }

    /**
     * Upload thumbnail version
     * Note: Install intervention/image package for advanced image manipulation
     */
    public function uploadWithThumbnail(UploadedFile $file, string $path = 'uploads'): array
    {
        // For now, just upload the original
        // To enable thumbnails: composer require intervention/image
        $original = $this->upload($file, $path);

        return [
            'original' => $original,
            'thumbnail' => $original, // Same as original until intervention/image is installed
        ];
    }

    /**
     * Delete an image
     */
    public function delete(string $url): bool
    {
        $path = $this->urlToPath($url);
        
        if ($path && Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    /**
     * Upload and compress image without losing quality
     * Supports: jpg, jpeg, png, webp, gif
     */
    public function uploadCompressed(UploadedFile $file, string $path = 'vouchers', int $quality = 85): string
    {
        // Generate unique filename
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $fullPath = $path . '/' . $filename;

        // Get image contents
        $imageContent = file_get_contents($file->getRealPath());
        
        // Create image resource based on type
        $image = null;
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file->getRealPath());
                break;
            case 'png':
                $image = imagecreatefrompng($file->getRealPath());
                imagealphablending($image, false);
                imagesavealpha($image, true);
                break;
            case 'gif':
                $image = imagecreatefromgif($file->getRealPath());
                break;
            case 'webp':
                $image = imagecreatefromwebp($file->getRealPath());
                break;
            default:
                // If not supported, just store without compression
                $file->storeAs($path, $filename, $this->disk);
                return $path . '/' . $filename;
        }

        if (!$image) {
            // Fallback: store without compression
            $file->storeAs($path, $filename, $this->disk);
            return $path . '/' . $filename;
        }

        // Create temp path for compressed image
        $tempPath = sys_get_temp_dir() . '/' . $filename;

        // Save compressed image
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($image, $tempPath, $quality);
                break;
            case 'png':
                // PNG quality is 0-9 (0 = no compression, 9 = max compression)
                $pngQuality = (int)((100 - $quality) / 10);
                imagepng($image, $tempPath, $pngQuality);
                break;
            case 'gif':
                imagegif($image, $tempPath);
                break;
            case 'webp':
                imagewebp($image, $tempPath, $quality);
                break;
        }

        // Free memory
        imagedestroy($image);

        // Store compressed image
        Storage::disk($this->disk)->put($fullPath, file_get_contents($tempPath));

        // Delete temp file
        @unlink($tempPath);

        return $path . '/' . $filename;
    }

    /**
     * Get full URL for a path
     */
    public function getUrl(string $path): string
    {
        if (env('CDN_ENABLED', false)) {
            return env('CDN_URL') . '/' . $path;
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Convert URL to storage path
     */
    protected function urlToPath(string $url): ?string
    {
        $cdnUrl = env('CDN_URL', '');
        $appUrl = env('APP_URL', '');

        if ($cdnUrl && str_starts_with($url, $cdnUrl)) {
            return str_replace($cdnUrl . '/', '', $url);
        }

        if (str_starts_with($url, $appUrl)) {
            return str_replace($appUrl . '/storage/', '', $url);
        }

        return null;
    }
}
