<?php

namespace Tests\Unit\Rules;

use App\Rules\GenresHasCategoriesRule;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GenresHasCategoriesRuleUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIsInstanceOf()
    {
        $myClass = new GenresHasCategoriesRule([]);
        $this->assertInstanceOf(GenresHasCategoriesRule::class, $myClass);
    }
    /** @test */
    public function testCategoriesIdField()
    {
        $rules = new GenresHasCategoriesRule([1, 2, 1, 2]);
        $reflectionClass = new ReflectionClass(GenresHasCategoriesRule::class);
        $reflecitonProperty = $reflectionClass->getProperty('categoriesId');
        $reflecitonProperty->setAccessible(true);
        $categoriesId = $reflecitonProperty->getValue($rules);
        $this->assertEqualsCanonicalizing([1, 2], $categoriesId);
    }

    public function testGenresIdField()
    {
        $rules = $this->createRuleMock([]);
        $rules
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturnNull();
        // Using  the method "passes" for to passe  value, because implements
        // the  interface the first param named  'attribute  used to as string empty ' '
        $rules->passes('', [1, 2, 2, 1]);
        $reflectionClass = new ReflectionClass(GenresHasCategoriesRule::class);
        $reflecitonProperty = $reflectionClass->getProperty('genresId');
        $reflecitonProperty->setAccessible(true);
        $genresId = $reflecitonProperty->getValue($rules);
        $this->assertEqualsCanonicalizing([1, 2], $genresId);
    }

    /** @test */
    public function testPassesReturnFalseWhenCategoriesOrGenresIsArrayEmpty()
    {
        //Set value  $categoriesId by constructor class in mock, are testing genres is empty
        $rules = $this->createRuleMock([1]);
        $this->assertFalse($rules->passes('', []));

        //Set value  $categoriesId empty by constructor class in mock, are testing categories is empty
        $rules = $this->createRuleMock([]);
        $this->assertFalse($rules->passes('', [1]));
    }
    /** @test */
    public function testPassesReturnFalseWhenGetRowsIsEmpty()
    {
        $rules = $this->createRuleMock([1]);
        $rules
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());
        $this->assertFalse($rules->passes('', [1]));
    }
    /** @test */
    public function testPassesReturnFalseWhenHasCategoriesWhitOutGenres()
    {
        $rules = $this->createRuleMock([1, 2]);
        $rules
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));
        $this->assertFalse($rules->passes('', [1]));
    }

    /** @test */
    public function testPassesIsValid()
    {
        $rules = $this->createRuleMock([1, 2]);
        $rules
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2]
            ]));
        $this->assertTrue($rules->passes('', [1, 2]));

        $rules = $this->createRuleMock([1, 2]);
        $rules
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 2],
            ]));
        $this->assertTrue($rules->passes('', [1, 2]));
    }

    protected function createRuleMock(array $categoriesId): MockInterface
    {

        return \Mockery::mock(GenresHasCategoriesRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}
