<?php

namespace Tests\Unit\Models\Traits;

use Illuminate\Http\UploadedFile;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;

class UploadFilesTest extends TestCase
{
    /**
     *
     * @var UploadFilesStub
     */
    private $obj;


    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFilesStub();
        UploadFilesStub::dropTable();
        UploadFilesStub::makeTable();
    }

    /** @test */
    public function testMakeOldFieldsOnSaving()
    {
        //If not exists
        $this->obj->fill([
            'name' => 'teste',
            'file1' => 'teste1.mp4',
            'file2' => 'teste2.mp4',
        ]);
        $this->obj->save();
        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update([
            'name' => 'test_name',
            'file2' => 'teste3.mp4'
        ]);

        $this->assertEqualsCanonicalizing(['teste2.mp4'], $this->obj->oldFiles);
    }

    public function testMakeOldFieldsNullOnSaving()
    {
        //If make fields null
        $this->obj->fill([
            'name' => 'teste',
        ]);
        $this->obj->save();
        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update([
            'name' => 'test_name',
            'file2' => 'teste3.mp4'
        ]);

        $this->assertEqualsCanonicalizing([], $this->obj->oldFiles);
    }
}
