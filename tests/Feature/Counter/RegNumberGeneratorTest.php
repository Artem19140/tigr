<?php

namespace Tests\Feature\Counter;

use App\Modules\Counter\RegNumberGenerator;
use App\Enums\CounterKey;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;
    protected RegNumberGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = app(RegNumberGenerator::class);

        Carbon::setTestNow(Carbon::create(2025, 5, 1, 0, 0, 0));

        Counter::create([
            'key' => CounterKey::RegNum,
            'value' => CounterKey::RegNum->defaultValue()
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_reg_num_generation(): void
    {
        $firstNumber = $this->generator->execute();
        $this->assertEquals($firstNumber, CounterKey::RegNum->defaultValue());

        $seecondNumber = $this->generator->execute();
        $this->assertEquals($seecondNumber, CounterKey::RegNum->defaultValue() + 1);
    }

    public function test_reg_num_change_year(): void
    {
        $regNumber = $this->generator->execute();
        $this->assertEquals($regNumber, CounterKey::RegNum->defaultValue());

        Carbon::setTestNow(Carbon::now()->addYear());

        $regNumber = $this->generator->execute();
        $this->assertEquals($regNumber, CounterKey::RegNum->defaultValue());

    }

}
