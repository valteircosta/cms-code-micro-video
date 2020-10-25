<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;


use App\Http\Controllers\Api\VideoController;
use App\Http\Resources\VideoResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Arr;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;


class VideoControllerCrudTest extends BaseVideoControllerTestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    private $serializedFields = [
        "id",
        "title",
        "description",
        "year_launched",
        "opened",
        "rating",
        "duration",
        "video_file_url",
        "thumb_file_url",
        "banner_file_url",
        "trailer_file_url",
        "deleted_at",
        "created_at",
        "updated_at",
        "categories" => [
            '*' => [
                "id",
                "name",
                "description",
                "is_active",
                "created_at",
                "updated_at",
            ]
        ],
        "genres" => [
            "*" => [
                "id",
                "name",
                "is_active",
                "deleted_at",
                "created_at",
                "updated_at"
            ]
        ]
    ];
    public function testIndex()
    {
        $response = $this->get(route('videos.index'));
        $response
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => $this->serializedFields
                    ],
                    'links' => [],
                    'meta' => [],
                ]
            );
        $resource = VideoResource::collection(collect([$this->video]));
        $this->assertResource($response, $resource);
        $this->assertIfFilesUrlExists($this->video, $response);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
        $this->assertResource(
            $response,
            new VideoResource(Video::find($response->json('data.id')))
        );
        $this->assertIfFilesUrlExists($this->video, $response);
    }
    public function testInvalidationRequired()
    {

        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => '',
        ];

        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }
    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a', 266),
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }
    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's',
        ];
        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }
    public function testInvalidationYearLaunchedField()
    {
        $data = [
            'year_launched' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
    }
    public function testInvalidationOpenedField()
    {
        $data = [
            'opened' => 's',
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100],
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id],
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'genres_id' => [100],
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
        $genre = factory(Genre::class)->create();
        $genre->delete();
        $data = [
            'genres_id' => [$genre->id],
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 0,
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    //Not use prefix test in helper methods
    private function assertInvalidationRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']) // Field is_active não está presente
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ]);
    }
    private function assertInvalidationMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    private function assertInvalidationBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                //O char undescore _ volta # is_active => is active
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public  function testSaves()
    {
        // $category = factory(Category::class)->create();
        // $genre = factory(Genre::class)->create();
        // $genre->categories()->sync($category->id);
        $testData = Arr::except($this->sendData, ['categories_id', 'genres_id']);

        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $testData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + ['opened' => true],
                'test_data' => $testData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + [
                    'rating' => Video::RATING_LIST[1],
                ],
                'test_data' => $testData + ['rating' => Video::RATING_LIST[1]]
            ],

        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
            $this->assertResource(
                $response,
                new VideoResource(Video::find($response->json('data.id')))
            );
            $this->assertIfHasCategory(
                $response->json('data.id'),
                $value['send_data']['categories_id'][0]
            );

            $this->assertIfHasGenre(
                $response->json('data.id'),
                $value['send_data']['genres_id'][0]
            );
            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
            $this->assertResource(
                $response,
                new VideoResource(Video::find($response->json('data.id')))
            );
            $this->assertIfHasCategory(
                $response->json('data.id'),
                $value['send_data']['categories_id'][0]
            );

            $this->assertIfHasGenre(
                $response->json('data.id'),
                $value['send_data']['genres_id'][0]
            );
        }
    }

    protected  function assertIfHasCategory($videoId, $categoryiId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryiId
        ]);
    }
    protected  function assertIfHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }


    public function testDestroy()
    {
        $response = $this->json(
            'DELETE',
            route('videos.destroy', ['video' => $this->video->id])
        );
        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }
    // public function testSyncCategories()
    // {
    //     //Cria com helper, gera array de ids com pluck => tipo  ['1','2','2']
    //     $categoriesId = Factory(Category::class, 3)->create()->pluck('id')->toArray();
    //     $genre = Factory(Genre::class)->create();
    //     $genre->categories()->sync($categoriesId);
    //     $genreId = $genre->id;
    //     //Inclui com a video
    //     $response = $this->json(
    //         'POST',
    //         $this->routeStore(),
    //         $this->sendData + [
    //             'genres_id' => [$genreId],
    //             'categories_id' => [$categoriesId[0]]
    //         ]
    //     );
    //     $this->assertDatabaseHas('category_video', [
    //         'category_id' => $categoriesId[0],
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $response = $this->json(
    //         'PUT',
    //         route('videos.update', ['video' => $response->json('data.id')]),
    //         $this->sendData + [
    //             'genres_id' => [$genreId],
    //             'categories_id' => [$categoriesId[1], $categoriesId[2]]
    //         ]
    //     );
    //     //Valida não existencia
    //     $this->assertDatabaseMissing('category_video', [
    //         'category_id' => $categoriesId[0],
    //         'video_id' => $response->json('data.id')
    //     ]);
    //     //Valida a existência
    //     $this->assertDatabaseHas('category_video', [
    //         'category_id' => $categoriesId[1],
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $this->assertDatabaseHas('category_video', [
    //         'category_id' => $categoriesId[2],
    //         'video_id' => $response->json('data.id')
    //     ]);
    // }

    // public function testSyncGenres()
    // {
    //     $genre = Factory(Genre::class, 3)->create();
    //     $genreId = $genre->pluck('id')->toArray();
    //     $categoryId = Factory(Category::class)->create()->id;
    //     $genre->each(function ($genre) use ($categoryId) {
    //         $genre->categories()->sync($categoryId);
    //     });
    //     //Inclui com a video
    //     $response = $this->json(
    //         'POST',
    //         $this->routeStore(),
    //         $this->sendData + [
    //             'genres_id' => [$genreId[0]],
    //             'categories_id' => [$categoryId]
    //         ]
    //     );
    //     $this->assertDatabaseHas('genre_video', [
    //         'genre_id' => [$genreId[0]],
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $response = $this->json(
    //         'PUT',
    //         route('videos.update', ['video' => $response->json('data.id')]),
    //         $this->sendData + [
    //             'genres_id' => [$genreId[1], $genreId[2]],
    //             'categories_id' => [$categoryId]
    //         ]
    //     );
    //     //Valida não existencia
    //     $this->assertDatabaseMissing('genre_video', [
    //         'genre_id' => $genreId[0],
    //         'video_id' => $response->json('data.id')
    //     ]);
    //     //Valida a existência
    //     $this->assertDatabaseHas('genre_video', [
    //         'genre_id' => $genreId[1],
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $this->assertDatabaseHas('genre_video', [
    //         'genre_id' => $genreId[2],
    //         'video_id' => $response->json('data.id')
    //     ]);
    // }


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
