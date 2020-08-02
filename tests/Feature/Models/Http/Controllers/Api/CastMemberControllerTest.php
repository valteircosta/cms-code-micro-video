<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\Types\This;
use Route;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase

{
    //Sempre usar esta trait em teste com banco de dados
    use DatabaseMigrations, TestValidations, TestSaves;

    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->castMember->toArray()]);
    }
    public function testShow()
    {
        $response = $this->get(route('cast_members.show', ['castMember' => $this->castMember->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->castMember->toArray());
    }
    /** @test */
    public function testInvalidationData()
    {

        $data = [
            'name' => '',
            'type' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'type' => 's',
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
            ['name' => 'test', 'type' => CastMember::TYPE_ACTOR],
            ['name' => 'test', 'type' => CastMember::TYPE_DIRECTOR]
        ];
        foreach ($data as $key => $value) {
            $response = $this->assertStore($value, $value + ['delete_at' => null]);
            $response->assertJsonStructure(['updated_at', 'created_at']);
        }
    }
    public function testUpdate()
    {
        $data =    [
            'name' => 'name',
            'type' => CastMember::TYPE_ACTOR
        ];

        $data['name'] = 'test';


        $testDatabase = array_merge($data, [
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $testJsonData = array_merge($data, [
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
            route('cast_members.destroy', ['cast_member' => $this->castMember->id])
        );
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castMember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));
    }

    protected  function routeStore()
    {
        return route('cast_members.store');
    }
    protected  function routeUpdate()
    {
        return route('cast_members.update', ['cast_member' => $this->castMember]);
    }
    protected function model()
    {
        return CastMember::class;
    }
}
