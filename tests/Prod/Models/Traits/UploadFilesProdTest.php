<?php

namespace Tests\Prod\Models\Traits;

use Illuminate\Http\UploadedFile;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;
use Tests\Traits\TestProd;
use Tests\Traits\TestStorage;

/**
 * Teste de itens e propriedades da unit
 * Não testa nada que vem de fora da classe
 * Esta mudanças serão testadas em feature
 */

class UploadFilesProdTest extends TestCase
{
    use TestStorage, TestProd;
    /**
     *
     * @var UploadFilesStub
     */
    private $obj;


    protected function setUp(): void
    {
        parent::setUp();
        $this->skipTestIfNotProd(); //The first method executed in the class is this.
        $this->obj = new UploadFilesStub();
        \Config::set('filesystems.default', 'gcs');
        $this->deleteAllFiles();
    }
    /** @test */
    public function testUploadFile()
    {
        $file = UploadedFile::fake()->create('video.mp4'); //Fake of file generate by laravel
        $this->obj->uploadFile($file);
        \Storage::assertExists("1/{$file->hashName()}");
    }
    /** @test */
    public function testUploadFiles()
    {

        $file1 = UploadedFile::fake()->create('video1.mp4'); //Fake of file generate by laravel
        $file2 = UploadedFile::fake()->create('video2.mp4'); //Fake of file generate by laravel
        $this->obj->uploadFiles([$file1, $file2]);
        \Storage::assertExists("1/{$file1->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");
    }

    /** @test */
    public function testDeleteOldFiles()
    {

        $file1 = UploadedFile::fake()->create('video1.mp4')->size(1); //Fake of file generate by laravel
        $file2 = UploadedFile::fake()->create('video2.mp4')->size(1); //Fake of file generate by laravel
        $this->obj->uploadFiles([$file1, $file2]);
        $this->obj->deleteOldFiles([$file1, $file2]);
        $this->assertCount(2, \Storage::allFiles());

        $this->obj->oldFiles = [$file1->hashName()];
        $this->obj->deleteOldFiles();
        \Storage::assertMissing("1/{$file1->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");
    }
    /** @test */
    public function testDeleteFile()
    {

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

        $file1 = UploadedFile::fake()->create('video1.mp4'); //Fake of file generate by laravel
        $file2 = UploadedFile::fake()->create('video2.mp4'); //Fake of file generate by laravel
        $this->obj->uploadFiles([$file1, $file2]);
        $this->obj->deleteFiles([$file1->hashName(), $file2]);
        \Storage::assertMissing("1/{$file1->hashName()}");
        \Storage::assertMissing("1/{$file2->hashName()}");
    }
}
