<?php

namespace Tests\Feature\Counter;

use App\Modules\Counter\GroupNumberGenerator;
use App\Enums\CounterKey;
use App\Models\Center;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected GroupNumberGenerator $generator;
    protected Center $center;
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->center = Center::factory()->create();

        $this->generator = app(GroupNumberGenerator::class);

        Carbon::setTestNow(Carbon::create(2025, 5, 1, 0, 0, 0));

        Counter::create([
            'key' => CounterKey::Group,
            'value' => CounterKey::Group->defaultValue(),
            'center_id' => $this->center->id,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_group_number_generation(): void
    {
        $firstNumber = $this->generator->execute($this->center->id);

        $this->assertEquals($firstNumber, CounterKey::Group->defaultValue());

        $secondNumber = $this->generator->execute($this->center->id);

        $this->assertEquals($secondNumber, CounterKey::Group->defaultValue() + 1);

        $thirdNumber = $this->generator->execute($this->center->id);

        $this->assertEquals($thirdNumber, CounterKey::Group->defaultValue() + 2);
    }


    public function test_group_number_generation_change_day(): void
    {
        $number = $this->generator->execute($this->center->id);
        $this->assertEquals($number, CounterKey::Group->defaultValue());

        Carbon::setTestNow(Carbon::now()->addDay());

        $anotherDayNumber = $this->generator->execute($this->center->id);
        $this->assertEquals($anotherDayNumber, CounterKey::Group->defaultValue());
    }
}
