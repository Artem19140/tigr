<?php

namespace Tests\Feature\Exam;

use App\Models\Address;
use App\Models\Employee;
use App\Models\ExamType;
use App\Modules\Shared\CenterData;
use App\Modules\Shared\ExamSettings;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamCreateTest extends TestCase
{
    use RefreshDatabase;
    protected Employee $actor;
    protected ExamType $examType;
    protected Address $address;
    protected Employee $examiner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);

        $this->examiner = Employee::factory()
            ->examiner()->create();

        $this->actor = Employee::factory()
            ->scheduler()
            ->create();

        $this->examType = ExamType::factory()->create();

        $this->address = Address::factory()->create();

        Carbon::setTestNow(
            Carbon::parse('2026-01-01 10:00:00')
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    protected function postExam(array $overrides = [])
    {
        return $this->actingAs($this->actor)
            ->postJson('/exams', $this->examBody($overrides));
    }

    protected function examBody(array $overrides = [])
    {
        $minTimeBeforeCreating = ExamSettings::minTimeBeforeCreateMinutes();
        return array_merge([
            'date' => Carbon::now()->setTimezone(CenterData::timeZome())->addMinutes($minTimeBeforeCreating )->format('Y-m-d'),
            'time' => Carbon::now()->setTimezone(CenterData::timeZome())->addMinutes($minTimeBeforeCreating )->addMinute()->format('H:i'),
            'examTypeId' => $this->examType->id,
            'addressId' => $this->address->id,
            'capacity' => $this->address->capacity,
            'examiners' => [$this->examiner->id],
            'comment' => '',
        ], $overrides);
    }

    public function test_success_exam_creation(): void
    {

        $this->withoutExceptionHandling();
        $response = $this->postExam();

        $response->assertOk();

        $this->assertDatabaseCount('exams', 1);
    }

    public function test_success_different_addresses(): void
    {
        $response = $this->postExam();

        $response->assertOk();

        $examiner = Employee::factory()->examiner()->create();

        $address = Address::factory()->create();

        $response = $this->postExam([
            'addressId' => $address->id,
            'examiners' => [$examiner->id],
            'capacity' => $address->capacity,
        ]);
        $response->assertOk();
        $this->assertDatabaseCount('exams', 2);
    }

    public function test_fail_in_past(): void
    {
        $response = $this->postExam([
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => Carbon::now()->addMinutes(ExamSettings::minTimeBeforeCreateMinutes())->subHour()->format('H:i'),
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseEmpty('exams');
    }

    public function test_fail_with_busy_tester(): void
    {
        $response = $this->postExam();
        $response->assertOk();

        $address = Address::factory()->create();

        $response = $this->postExam([
            'addressId' => $address->id,
            'capacity' => $address->capacity,
        ]);
        $response->assertBadRequest();

        $this->assertDatabaseCount('exams', 1);
    }

    public function test_fail_with_no_role_examiner(): void
    {
        $response = $this->postExam([
            'examiners' => [$this->actor->id],
        ]);

        $response->assertBadRequest();

        $this->assertDatabaseEmpty('exams');
    }

    public function test_fail_more_than_max_capacity(): void
    {
        $response = $this->postExam([
            'capacity' => $this->address->capacity + 1,
        ]);
        $response->assertUnprocessable();
        $this->assertDatabaseEmpty('exams');
    }

    public function test_fail_not_active_address(): void
    {
        $address = Address::factory()->create([
            'is_active' => false
        ]);
        $response = $this->postExam([
            'addressId' => $address->id,
        ]);
        $response->assertUnprocessable();
        $this->assertDatabaseEmpty('exams');
    }

    public function test_fail_not_active_examiner(): void
    {
        $employee = Employee::factory()
            ->examiner()
            ->notActive()
            ->create();

        $response = $this->postExam([
            'examiners' => [$employee->id],
        ]);

        $response->assertBadRequest();
        $this->assertDatabaseEmpty('exams');
    }
}
