<?php

namespace Tests\Feature\Attempt;

use App\Modules\Enrollment\VerifyCode;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VerifyCodeTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected $exception;

    protected string $code = '123456';

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(VerifyCode::class);
        $this->exception = ValidationException::class;

        Carbon::setTestNow('2026-01-01 10:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success(): void
    {
        $this->withoutExceptionHandling();
        Enrollment::factory()
            ->examCode($this->code)
            ->examCodeExpiredAt(Carbon::now()->addMinutes(10))
            ->create(['has_payment' => true]);
        $result = $this->service->execute($this->code);
        $this->assertInstanceOf(Enrollment::class, $result);
    }

    public function test_fail_no_payment(): void
    {
        $this->expectException($this->exception);
        Enrollment::factory()
            ->examCode($this->code)
            ->examCodeExpiredAt(now()->addMinutes(10))
            ->create(['has_payment' => false]);

        $this->service->execute($this->code);
    }

    public function test_fail_expired(): void
    {
        $this->expectException($this->exception);
        $this->withoutExceptionHandling();
        Enrollment::factory()
            ->examCode($this->code)
            ->examCodeExpiredAt(now()->subMinutes(10))
            ->create(['has_payment' => true]);
        $this->service->execute($this->code);
    }

    public function test_fail_used_code(): void
    {
        $this->expectException($this->exception);
        Enrollment::factory()
            ->examCode($this->code)
            ->examCodeExpiredAt(now()->addMinutes(10))
            ->create([
                'exam_code_used_at' => Carbon::now(),
                'has_payment' => true,
            ]);

        $this->service->execute($this->code);
        Log::spy();
        Log::shouldHaveReceived('warning')
            ->once();
    }
}
