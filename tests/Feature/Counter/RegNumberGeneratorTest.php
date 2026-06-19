<?php

namespace Tests\Feature\Counter;

use App\Modules\Center\CenterContext;
use App\Modules\Counter\RegNumberGenerator;
use App\Enums\CounterKey;
use App\Models\Center;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class RegNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;
    protected RegNumberGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $center = Center::factory()->create();

        $mock = Mockery::mock(CenterContext::class);
        $mock->shouldReceive('id')->andReturn($center->id);

        $this->app->instance(CenterContext::class, $mock);

        $this->generator = app(RegNumberGenerator::class);

        Carbon::setTestNow(Carbon::now());

        Counter::create([
            'key' => CounterKey::RegNum,
            'value' => CounterKey::RegNum->defaultValue() - 1,
            'center_id' => $center->id,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_reg_num_generation(): void
    {
        $regNumber = $this->generator->execute();
        $this->assertEquals($regNumber, CounterKey::RegNum->defaultValue());
    }

    public function test_success_reg_num_change_year(): void
    {
        $regNumber = $this->generator->execute();
        $this->assertEquals($regNumber, CounterKey::RegNum->defaultValue());

        Carbon::setTestNow(Carbon::now()->addYear());

        $regNumber = $this->generator->execute();
        $this->assertEquals($regNumber, CounterKey::RegNum->defaultValue());

        $regNumber = $this->generator->execute();
        $this->assertEquals($regNumber, CounterKey::RegNum->defaultValue() + 1);
        Carbon::setTestNow(Carbon::now());
    }

}
