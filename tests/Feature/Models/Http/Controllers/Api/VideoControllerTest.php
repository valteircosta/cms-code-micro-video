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
        $response = $this->get(route('video.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }
    public function testShow()
    {
        $response = $this->get(route('video.show', ['video' => $this->video->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }
    /** @test */
    public function testInvalidationData()
    {

        $data = [
            'name' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
        $data = [
            'name' => str_repeat('a', 266),
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
        $data = [
            'is_active' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
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
    /** @test */
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
