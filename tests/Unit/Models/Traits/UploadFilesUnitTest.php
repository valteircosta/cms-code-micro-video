<?php

namespace Tests\Unit\Models\Traits;

use Illuminate\Http\UploadedFile;
use phpDocumentor\Reflection\Types\This;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;

/**
 * Teste de itens e propriedades da unit
 * Não testa nada que vem de fora da classe
 * Esta mudanças serão testadas em feature
 */

class UploadFilesUnitTest extends TestCase
{
    /**
     *
     * @var UploadFilesStub
     */
    private $obj;


    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFilesStub();
    }
    /** @test */
    public function testUploadFile()
    {
        \Storage::fake(); //Make in testing folder
        $file = UploadedFile::fake()->create('video.mp4'); //Fake of file generate by laravel
        $this->obj->uploadFile($file);
        \Storage::assertExists("1/{$file->hashName()}");
    }
    /** @test */
    public function testUploadFiles()
    {
        \Storage::fake(); //Make in testing folder
        $file1 = UploadedFile::fake()->create('video1.mp4'); //Fake of file generate by laravel
        $file2 = UploadedFile::fake()->create('video2.mp4'); //Fake of file generate by laravel
        $this->obj->uploadFiles([$file1, $file2]);
        \Storage::assertExists("1/{$file1->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");
    }

    /** @test */
    public function testDeleteFile()
    {
        \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $fileName = $file->hashName();
        $this->obj->deleteFile($fileName);
        \Storage::assertMissing("1/{$fileName}");

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file->hashName());
        \Storage::assertMissing("1/{$file->hashName()}");
    }

    /** @test */
    public function testDeleteFiles()
    {
        \Storage::fake(); //Make in testing folder
        $file1 = UploadedFile::fake()->create('video1.mp4'); //Fake of file generate by laravel
        $file2 = UploadedFile::fake()->create('video2.mp4'); //Fake of file generate by laravel
        $this->obj->uploadFiles([$file1, $file2]);
        $this->obj->deleteFiles([$file1->hashName(), $file2]);
        \Storage::assertMissing("1/{$file1->hashName()}");
        \Storage::assertMissing("1/{$file2->hashName()}");
    }
}
