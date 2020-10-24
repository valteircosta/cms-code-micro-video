<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;
use Tests\Traits\TestProd;

class VideoUploadTest extends BaseVideoTestCase
{
    use TestProd;
    /** @test */
    public function testCreateWithFiles()
    {
        \Storage::fake();
        $video = Video::create(
            $this->data + [
                'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                'banner_file' => UploadedFile::fake()->image('banner.png'),
                'video_file' => UploadedFile::fake()->create('video.mp4'),
                'trailer_file' => UploadedFile::fake()->create('trailer.mp4'),
            ]
        );
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
    }
    /** @test */
    public function testCreateIfRollbackFiles()
    {
        \Storage::fake();
        /**
         * Event happen before  commit, know handled these events is very util
         */
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;
        try {
            $video = Video::create(
                $this->data + [
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'video_file' => UploadedFile::fake()->image('video.mp4'),
                ]
            );
            \Storage::assertExists("{$video->id}/{$video->thumb_file}");
            \Storage::assertExists("{$video->id}/{$video->video_file}");
        } catch (\Tests\Exceptions\TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = \true;
        }
    }

    public function testFileUrlsWithLocalDriver()
    {
        $fileFields = [];
        foreach (Video::$fileFields as $field) {
            $fileFields[$field] = "$field.test";
        }
        $video = \factory(Video::class)->create($fileFields);
        $localDriver = \config('filesystems.default');
        $baseUrl = \config('filesystems.disks.' . $localDriver)['url'];
        foreach ($fileFields as $field => $value) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/$video->id/$value", $fileUrl);
        }
    }
    public function testFileUrlsWithGcsDriver()
    {
        $fileFields = [];
        foreach (Video::$fileFields as $field) {
            $fileFields[$field] = "$field.test";
        }
        $video = \factory(Video::class)->create($fileFields);
        $baseUrl = \config('filesystems.disks.gcs.storage_api_uri');
        \Config::set('filesystems.default', 'gcs');
        foreach ($fileFields as $field => $value) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/$video->id/$value", $fileUrl);
        }
    }
    public function testFileUrlsIfNullWhenFieldAreNull()
    {
        $video = \factory(Video::class)->create();
        foreach (Video::$fileFields as $field) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertNull($fileUrl);
        }
    }
}
