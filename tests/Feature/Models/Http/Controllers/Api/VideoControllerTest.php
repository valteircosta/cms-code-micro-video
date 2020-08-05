<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\Types\This;
use Route;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }
    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }
    public function testInvalidationData()
    {

        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'duration' => '',
            'rating' => ''
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
    /** @testStore */
    public function testStore()
    {
        $data =    [
            'name' => 'test'
        ];

        $testDatabase = $data + [
            'is_active' => true,
            'deleted_at' => null,
        ];

        $testJsonData = $data + [
            'is_active' => true,
            'deleted_at' => null,
        ];
        $response = $this->assertStore($data, $testDatabase, $testJsonData);
        $response->assertJsonStructure(['deleted_at', 'created_at']);
        $data =    [
            'name' => 'test',
            'is_active' => false,
        ];
        $testDatabase = $data;
        $this->assertStore($data, $testDatabase);
    }

    public function testUpdate()
    {
        $data =    [
            'name' => 'name',
        ];
        // $this->category = factory(Video::class)->create(
        //     $data
        // );

        $data['name'] = 'test';
        $data['is_active'] = false;

        $testDatabase = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $testJsonData = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $response = $this->assertUpdate($data, $testDatabase, $testJsonData);
        $response->assertJsonStructure(
            ['deleted_at', 'created_at']
        );
        $data['is_active'] = false;
        $testDatabase = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $testJsonData = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);
        $response = $this->assertUpdate($data, $testDatabase, $testJsonData);
        $response->assertJsonFragment(
            $testJsonData
        );
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
