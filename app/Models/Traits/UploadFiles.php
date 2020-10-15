<?php

namespace App\Models\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

trait UploadFiles
{

    public $oldFiles = [];

    protected abstract function uploadDir();

    public static function bootUploadFiles()
    {
        static::updating(function (Model $model) {
            $fieldsUpdated = \array_keys($model->getDirty());
            $filesUpdated = \array_intersect($fieldsUpdated, self::$fileFields);
            $filesFiltered = Arr::where($filesUpdated, function ($fileField) use ($model) {
                return $model->getOriginal($fileField);
            });
            //Method statica is the model, not can uses $this
            $model->oldFiles = \array_map(function ($filesFiltered) use ($model) {
                return $model->getOriginal($filesFiltered);
            }, $filesFiltered);
        });
    }

    public function relativeFilePath($value)
    {
        return "{$this->uploadDir()}/{$value}";
    }

    /**
     * @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    /**
     * @param UploadedFile $file
     */
    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }
    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
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
        $fileName = $file instanceof UploadedFile ? $file->hashName() : $file;
        \Storage::delete("{$this->uploadDir()}/{$fileName}");
    }

    /**
     * Method static has access in create
     * using params by referencs &$attributes
     */
    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach (self::$fileFields as $file) {
            if (isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile) {
                $files[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }
        return $files;
    }
    protected function getFileUrl($filename)
    {
        return \Storage::url($this->relativeFilePath($filename));
    }
}
