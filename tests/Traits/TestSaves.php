<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{
    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected  function assertStore(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {
        /** @var TestResponse $response */

        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()} :\n {$response->content()}");
        }
        $this->assertInDataBase($response, $testDatabase);
        $this->assertJsonResponseContext($response, $testDatabase, $testJsonData);
        return $response;
    }
    protected function assertUpdate(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {

        /** @var TestResponse $response */

        $response = $this->json('PUT', $this->routeUpdate(), $sendData);
        if ($response->status() !== 200) {
            throw new \Exception("Response status must be 200, given {$response->status()} :\n {$response->content()}");
        }
        $this->assertInDataBase($response, $testDatabase);
        $this->assertJsonResponseContext($response, $testDatabase, $testJsonData);
        return $response;
    }
    private function assertInDataBase(TestResponse $response, array $testDatabase)
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas(
            $table,
            $testDatabase + ['id' => $this->getIdFromResponse($response)]
        );
    }
    private  function assertJsonResponseContext(TestResponse $response, array $testDatabase, array $testJsonData = null)
    {

        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment(
            $testResponse + ['id' => $this->getIdFromResponse($response)]
        );
    }

    private function getIdFromResponse(TestResponse $response)
    {
        //Using operator null coalesc PHP
        return $response->json('id') ?? $response->json('data.id');
    }
}
