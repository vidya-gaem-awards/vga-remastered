<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperFile
 */
class File extends Model
{
    public function getUrl(): string
    {
        return Storage::url($this->getRelativePath());
    }

    public function getFullFilename(): string
    {
        return $this->filename . '.' . $this->extension;
    }

    public function getRelativePath(): string
    {
        return $this->subdirectory . '/' . $this->getFullFilename();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'fullFilename' => $this->getFullFilename(),
            'relativePath' => $this->getRelativePath(),
            'url' => $this->getUrl(),
        ];
    }
}
