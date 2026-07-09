<?php

namespace Tests\Feature\Enrollment;

use App\Enums\CounterKey;
use App\Models\Address;
use App\Models\Counter;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ForeignNational;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentCreateTest extends TestCase
{
    use RefreshDatabase;
    protected Employee $actor;
    protected ForeignNational $foreignNational;

    protected string $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->actor = Employee::factory()->operator()->create();

        $this->foreignNational = ForeignNational::factory()->create();

        $this->model = Enrollment::class;

        Counter::create([
            'key' => CounterKey::RegNum,
            'value' => 260000
        ]);
        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    protected function postEnrollment(int $examId)
    {
        return $this->actingAs($this->actor)
            ->postJson('/enrollments', [
                'examId' => $examId,
                'foreignNationalId' => $this->foreignNational->id,
                'hasPayment' => true,
            ]);
    }

    public function test_success(): void
    {
        $this->withoutExceptionHandling();

        $exam = Exam::factory()->create([
            'begin_time' => Carbon::now()->addMinutes(Enrollment::CLOSE_BEFORE_START_MINUTES)->addMinute(),
            'end_time' => Carbon::now()->addMinutes(100 + Enrollment::CLOSE_BEFORE_START_MINUTES)->addMinute()
        ]);

        $response = $this->postEnrollment($exam->id);

        $response->assertOk();
        $this->assertDatabaseCount($this->model, 1);
    }

    public function test_fail_exam_past(): void
    {
        $exam = Exam::factory()
            ->inPast()
            ->create();

        $response = $this->postEnrollment($exam->id);

        $response->assertBadRequest();
        $this->assertDatabaseEmpty($this->model);
    }

    public function test_fail_exam_cancelled(): void
    {

        $exam = Exam::factory()
            ->inFuture()
            ->cancelled()
            ->create([
                'begin_time' => Carbon::now()->addMinutes(Enrollment::CLOSE_BEFORE_START_MINUTES)->addMinute()
            ]);

        $response = $this->postEnrollment($exam->id);

        $response->assertBadRequest();
        $this->assertDatabaseEmpty($this->model);
    }

    public function test_fail_closed_enrollment(): void
    {

        $exam = Exam::factory()->cancelled()->create([
            'begin_time' => Carbon::now()->addMinutes(Enrollment::CLOSE_BEFORE_START_MINUTES)->subMinute(),
            'end_time' => Carbon::now()->addHour()
        ]);

        $response = $this->postEnrollment($exam->id);

        $response->assertBadRequest();
        $this->assertDatabaseEmpty($this->model);
    }

    public function test_fail_full_enrollment(): void
    {
        $capacity = 8;
        $address = Address::factory()
            ->withCapacity($capacity)
            ->create();

        $exam = Exam::factory()
            ->withCapacity($capacity)
            ->has(Enrollment::factory($capacity))
            ->create([
                'begin_time' => Carbon::now()->addMinutes(Enrollment::CLOSE_BEFORE_START_MINUTES)->addMinute(),
                'address_id' => $address->id
            ]);

        $response = $this->postEnrollment($exam->id);

        $response->assertBadRequest();
        $this->assertDatabaseCount($this->model, $capacity);
    }

    public function test_fail_more_than_one_enrollment(): void
    {
        $exam = Exam::factory()
            ->inFuture()
            ->create();

        $response = $this->postEnrollment($exam->id);
        $response->assertOk();
        $this->assertDatabaseCount($this->model, 1);

        $response = $this->postEnrollment($exam->id);
        $response->assertBadRequest();
        $this->assertDatabaseCount($this->model, 1);
    }
}
