<?php

namespace Tests\Feature\Counter;

use App\Modules\Center\CenterContext;
use App\Modules\Counter\GroupNumberGenerator;
use App\Enums\CounterKey;
use App\Models\Center;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GroupNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected GroupNumberGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        

        $center = Center::factory()->create();
        $mock = Mockery::mock(CenterContext::class);
        $mock->shouldReceive('id')->andReturn($center->id);

        $this->app->instance(CenterContext::class, $mock);

        $this->generator = app(GroupNumberGenerator::class);

        Carbon::setTestNow(Carbon::create(2025, 5, 1, 0, 0, 0));

        Counter::create([
            'key' => CounterKey::Group,
            'value' => CounterKey::Group->defaultValue() - 1,
            'center_id' => $center->id,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_group_number_generation(): void
    {
        $firstNumber = $this->generator->execute();

        $this->assertEquals($firstNumber, CounterKey::Group->defaultValue());

        $secondNumber = $this->generator->execute();

        $this->assertEquals($secondNumber, CounterKey::Group->defaultValue() + 1);
    }


    public function test_group_number_generation_change_day(): void
    {
        $number = $this->generator->execute();
        $this->assertEquals($number, CounterKey::Group->defaultValue());

        Carbon::setTestNow(Carbon::now()->addDay());

        $anotherDayNumber = $this->generator->execute();
        $this->assertEquals($anotherDayNumber, CounterKey::Group->defaultValue());
    }
}
