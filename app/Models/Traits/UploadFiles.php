<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as FileUploadedFile;

trait UploadFiles
{

    protected abstract function uploadDir();

    /**
     * @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }
    /**
     * @param string|UploadedFile $file
     */
    public function deleteFile($file)
    {
        $fileName = $file instanceof FileUploadedFile ? $file->hashName() : $file;
        \Storage::delete("{$this->uploadDir()}/{$fileName}");
    }
}
