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
                'banner_file' => UploadedFile::fake()->image('banner.jpg'),
                'trailer_file' => UploadedFile::fake()->create('trailer.mp4'),
                'video_file' => UploadedFile::fake()->create('video.mp4'),
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
                    'banner_file' => UploadedFile::fake()->image('banner.png'),
                    'trailer_file' => UploadedFile::fake()->create('trailer.mp4'),
                ]
            );
            \Storage::assertExists("{$video->id}/{$video->thumb_file}");
            \Storage::assertExists("{$video->id}/{$video->video_file}");
            \Storage::assertExists("{$video->id}/{$video->banner_file}");
            \Storage::assertExists("{$video->id}/{$video->trailer_file}");
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
    public function testUpdateWithFile()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();
        $thumbFile = UploadedFile::fake()->image('thumb.jpg');
        $bannerFile = UploadedFile::fake()->image('banner.png');
        $videoFile = UploadedFile::fake()->create('video.mp4');
        $traillerFile = UploadedFile::fake()->create('trailler.mp4');

        $video->update(
            $this->data + [
                'video_file' => $videoFile,
                'trailer_file' => $traillerFile,
                'thumb_file' => $thumbFile,
                'banner_file' => $bannerFile
            ]
        );
        $video->refresh();

        $this->assertEquals($video->video_file, $videoFile->hashName());
        $this->assertEquals($video->trailer_file, $traillerFile->hashName());
        $this->assertEquals($video->thumb_file, $thumbFile->hashName());
        $this->assertEquals($video->banner_file, $bannerFile->hashName());

        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");

        $newVideoFile = UploadedFile::fake()->image('video.mp4');

        $video->update(
            $this->data + [
                'video_file' => $newVideoFile,
            ]
        );

        \Storage::assertMissing("{$video->id}/{$videoFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$thumbFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$traillerFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$bannerFile->hashName()}");
    }

    public function testUpdateIfRollbackFiles()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;

        try {
            $video->update(
                $this->data + [
                    'video_file' => UploadedFile::fake()->create('video.mp4'),
                    'trailer_file' => UploadedFile::fake()->create('trailler.mp4'),
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'banner_file' => UploadedFile::fake()->image('banner.png')
                ]
            );
        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }

        $this->assertTrue($hasError);
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
