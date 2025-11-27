<?php

namespace App\Services;

use App\Models\File;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public const int FILESIZE_LIMIT = 1024 * 1024 * 10;

    private const array EXTENSION_MAPPING = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'audio/ogg' => 'ogg',
        'video/ogg' => 'ogg',
        'video/webm' => 'webm',
        'application/ogg' => 'ogg',
        'application/x-zip-compressed' => 'zip',
        'font/ttf' => 'ttf',
        'font/otf' => 'otf',
        'application/font-woff' => 'woff',
        'application/font-woff2' => 'woff2',
        'font/woff' => 'woff',
        'font/woff2' => 'woff2',
    ];

    public function validateUploadedFile(?UploadedFile $file, ?bool $allowMimeBypass = false): void
    {
        $checkMime = !$allowMimeBypass || !Gate::denies('bypass_mime_checks');

        if ($file === null) {
            throw new Exception('No file was uploaded');
        } elseif (!$file->isValid()) {
            throw new Exception($file->getErrorMessage());
        } elseif ($checkMime && !in_array($file->getClientMimeType(), array_keys(self::EXTENSION_MAPPING), true)) {
            throw new Exception('Invalid MIME type (' . $file->getClientMimeType() . ')');
        } elseif ($file->getSize() > self::FILESIZE_LIMIT) {
            throw new Exception('Filesize of ' . self::humanFilesize($file->getSize()) . ' exceeds limit of ' . self::humanFilesize(self::FILESIZE_LIMIT));
        }
    }

    public function handleUploadedFile(
        UploadedFile $file,
        string $entityType,
        string $directory,
        ?string $filename,
        ?bool $allowMimeBypass = false
    ): File {
        $this->validateUploadedFile($file, $allowMimeBypass);

        if ($filename === null) {
            $token = hash('sha1', random_bytes(64));
            $filename = substr($token, 0, 8);
        }

        $fileEntity = new File();
        $fileEntity->subdirectory = $directory;
        $fileEntity->filename = $filename . '-' . time();

        $extension = self::EXTENSION_MAPPING[$file->getClientMimeType()]
            ?? $file->getClientOriginalExtension();

        $fileEntity->extension = $extension;
        $fileEntity->entity = $entityType;
        $fileEntity->save();

        Storage::putFileAs($fileEntity->subdirectory, $file, $fileEntity->getFullFilename());

        return $fileEntity;
    }

    public function deleteFile(File $file): void
    {
        Storage::delete($file->getRelativePath());
        $file->delete();
    }

    /**
     * Converts a number of bytes into a human-readable filesize.
     * This implementation is efficient, but will sometimes return a value that's less than one due
     * to the differences between 1000 and 1024 (for example, 0.98 GB)
     *
     * @param int $bytes File size in bytes.
     *
     * @return string The human-readable string, to two decimal places.
     */
    public static function humanFilesize(int $bytes): string
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        // Determine the order of magnitude of the size from the length of the string.
        // Use the last element of the size array as the upper bound.
        $factor = min(floor((strlen($bytes) - 1) / 3), count($size) - 1);
        return sprintf("%.2f", $bytes / (1024 ** $factor)) . $size[$factor];
    }
}
