<?php

namespace Tests\Feature\Counter;

use App\Enums\CounterKey;
use App\Models\Counter;
use App\Modules\Counter\SessionNumberGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;
    protected SessionNumberGenerator $generator;
    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = app(SessionNumberGenerator::class);

        Carbon::setTestNow(Carbon::create(2025, 5, 1, 0, 0, 0));

        Counter::create([
            'key' => CounterKey::Session,
            'value' => CounterKey::Session->defaultValue()
        ]);

    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow(); 
    }

    public function test_session_number_generation(): void
    {
        $firstNumber = $this->generator->execute();
        $this->assertEquals($firstNumber, CounterKey::Session->defaultValue());

        $secondNumber = $this->generator->execute();
        $this->assertEquals($secondNumber, CounterKey::Session->defaultValue());
    }

    public function test_session_number_generation_change_day(): void
    {
        $number = $this->generator->execute();
        $this->assertEquals($number, CounterKey::Session->defaultValue());

        Carbon::setTestNow(Carbon::now()->addDay());

        $secondDay = $this->generator->execute();
        $this->assertEquals($secondDay, CounterKey::Session->defaultValue() + 1);

        Carbon::setTestNow(Carbon::now()->addDays(2));

        $thirdDay = $this->generator->execute();
        $this->assertEquals($thirdDay, CounterKey::Session->defaultValue() + 2);
    }

    public function test_session_number_generation_change_year(): void
    {
        $number = $this->generator->execute();
        $this->assertEquals($number, CounterKey::Session->defaultValue());

        Carbon::setTestNow(Carbon::now()->addYear());

        $newYearNumber = $this->generator->execute();
        $this->assertEquals($newYearNumber, CounterKey::Session->defaultValue());
    }
}
