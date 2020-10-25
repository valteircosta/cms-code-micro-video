<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;


use App\Models\Video;
use App\Http\Controllers\Api\VideoController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;


class VideoControllerUploadsTest extends BaseVideoControllerTestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use  TestValidations, TestUploads;

    public function testInvalidationThumbField()
    {
        $this->assertInvalidationFile(
            'thumb_file',
            'jpg',
            Video::THUMB_FILE_MAX_SIZE,
            'image'
        );
    }

    public function testInvalidationBannerField()
    {
        $this->assertInvalidationFile(
            'banner_file',
            'jpg',
            Video::BANNER_FILE_MAX_SIZE,
            'image'
        );
    }

    public function testInvalidationTrailerField()
    {
        $this->assertInvalidationFile(
            'trailer_file',
            'mp4',
            Video::TRAILER_FILE_MAX_SIZE,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testInvalidationVideoField()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            Video::VIDEO_FILE_MAX_SIZE,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testStoreWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'POST',
            $this->routeStore(),
            $this->sendData + $files
        );
        $response->assertStatus(201);
        $this->assertFilesOnPersist($response, $files);
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendData + $files
        );

        $response->assertStatus(200);
        $this->assertFilesOnPersist($response, $files);

        $new_files = [
            'thumb_file' => UploadedFile::fake()->create('thumb_file.jpg'),
            'video_file' => UploadedFile::fake()->create('video_file.mp4'),
        ];
        $response = $this->json('PUT', $this->routeUpdate(), $this->sendData + $new_files);
        $response->assertStatus(200);
        $this->assertFilesOnPersist(
            $response,
            Arr::except($files, ['thumb_file', 'video_file']) + $new_files
        );
        $id = $response->json('data.id');
        $video = Video::find($id);
        \Storage::assertMissing($video->relativeFilePath($files['thumb_file']->hashName()));
        \Storage::assertMissing($video->relativeFilePath($files['video_file']->hashName()));
    }
    protected function assertFilesOnPersist($response, $files)
    {
        $id = $response->json('id') ?? $response->json('data.id');
        $video = Video::find($id);
        $this->assertFilesExistsInStorage($video, $files);
    }
    protected function getFiles()
    {
        return [
            'thumb_file' => UploadedFile::fake()->create("thumb_file.jpg"),
            'banner_file' => UploadedFile::fake()->create("banner_file.jpg"),
            'trailer_file' => UploadedFile::fake()->create("trailer_file.mp4"),
            'video_file' => UploadedFile::fake()->create("video_file.mp4"),
        ];
    }

    protected  function routeStore()
    {
        return route('videos.store');
    }
    protected  function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }
    protected function model()
    {
        return Video::class;
    }
}
