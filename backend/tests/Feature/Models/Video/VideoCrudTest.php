<?php

namespace Tests\Feature\Models\Video;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;

class VideoCrudTest extends BaseVideoTestCase
{

    private $fileFieldsData = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (Video::$fileFields as $field) {
            $fileFieldsData[$field] = "$field.test";
        }
    }


    public function testList()
    {
        \factory(Video::class)->create();
        $videos = Video::all();
        $this->assertCount(1, $videos);
        $videosKey =  \array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'video_file',
                'trailer_file',
                'banner_file',
                'thumb_file',
                'deleted_at',
                'created_at',
                'updated_at',
            ],
            $videosKey
        );
    }
    /** @test */
    public function testCreateWithBasicFields()
    {

        $video = Video::create($this->data + $this->fileFieldsData);
        $video->refresh();

        $this->assertEquals(36, \strlen($video->id));
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas(
            'videos',
            $this->data + $this->fileFieldsData + ['opened' => false]
        );

        $video = Video::create($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }

    /** @test */
    public function testCreateWithRelations()
    {
        $category = \factory(Category::class)->create();
        $genre = \factory(Genre::class)->create();
        $video = Video::create($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);
        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    protected function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryId
        ]);
    }

    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }
    /** @test */
    public function testUpdateWithBasicFields()
    {
        $video = \factory(Video::class)->create(['opened' => false]);
        $video->update($this->data + $this->fileFieldsData);
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas(
            'videos',
            $this->data + $this->fileFieldsData + ['opened' => false]
        );

        $video = \factory(Video::class)->create(['opened' => false]);
        $video->update($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas(
            'videos',
            $this->data + $this->fileFieldsData + ['opened' => true]
        );
    }
    public function testUpdateWithRelations()
    {
        $category = \factory(Category::class)->create();
        $genre = \factory(Genre::class)->create();
        $video =  \factory(Video::class)->create();

        $video->update($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }
    /** @test */
    public function testHandleRelations()
    {
        $video = \factory(Video::class)->create();
        Video::handleRelations($video, []);
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);

        $category = \factory(Category::class)->create();
        Video::handleRelations($video, [
            'categories_id' => [$category->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);

        $genre = \factory(Genre::class)->create();
        Video::handleRelations($video, [
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->genres);

        ///testar relacionamento
        $video->categories()->delete();
        $video->genres()->delete();

        Video::handleRelations($video, [
            'genres_id' => [$genre->id],
            'categories_id' => [$category->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);
        $this->assertCount(1, $video->genres);
    }

    /** @test */
    public function testSyncCategories()
    {
        $categoriesId = \factory(Category::class, 3)->create()->pluck('id')->toArray();
        $video = \factory(Video::class)->create();
        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[0]]
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ]);
        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);

        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[2],
            'video_id' => $video->id
        ]);
    }

    /** @test */
    public function testSyncGenres()
    {
        $genresId = \factory(Genre::class, 3)->create()->pluck('id')->toArray();
        $video = \factory(Video::class)->create();
        Video::handleRelations($video, [
            'genres_id' => [$genresId[0]]
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'genres_id' => [$genresId[1], $genresId[2]]
        ]);
        $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);

        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[2],
            'video_id' => $video->id
        ]);
    }

    public function testRollbackCreate()
    {
        $hasError = false;
        try {
            Video::create([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [1, 2, 3,]
            ]);
        } catch (QueryException $exception) {
            $this->assertCount(0, Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }
    public function testRollbackUpdate()
    {
        $video = \factory(Video::class)->create();
        $oldTitle = $video->title;
        try {
            $video->update([

                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [1, 2, 3,]
            ]);
        } catch (QueryException $exception) {
            $this->assertDatabaseHas('videos', [
                'title' => $oldTitle
            ]);
            $hasErro = true;
        }
        $this->assertTrue($hasErro);
    }
}
