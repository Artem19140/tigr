<?php

namespace App\Modules\ForeignNational\Query;

use App\Models\Document;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\ForeignNational;
use App\Modules\Enrollment\Rules\EnrollmentPaymentRules;
use App\Modules\Exam\Resolver\ExamResultResolver;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ForeignNationalViewBuilder
{
    public function __construct(
        protected EnrollmentPaymentRules $enrollmentPaymentRules,
        protected ExamResultResolver $resolver
    ){}
    public function build(
        ForeignNational $foreignNational,
        Employee $employee
    ): ForeignNational
    {
        $this->loadAllowedRelations($foreignNational, $employee);

        $foreignNational->enrollments = $foreignNational
            ->enrollments
            ->sortByDesc('exam.begin_time');

        $this->setEnrollmentsAttributes($foreignNational);

        return $foreignNational;
    }

    protected function loadAllowedRelations(
        ForeignNational $foreignNational,
        Employee $employee
    )
    {
        $relations = [];

        if($employee->can('viewAny', Document::class)){
            $relations = [...$relations, 
                ...$this->documentsRelations()
            ];
        }

        if($employee->can('viewAny', Enrollment::class)){
            $relations = [...$relations,
                ...$this->enrollmentsRelations($employee)
            ];
        }

        $foreignNational->load(['creator', ...$relations]);
    }

    protected function documentsRelations(): array
    {
        return [
            'documents' => function(MorphMany $query){
                    return $query->whereNull('deleted_at');
                },
            'documents.creator', 'documents.documentable'
        ];
    }

    protected function enrollmentsRelations(Employee $employee)
    {
        return [
            'enrollments' => function ($query) use ($employee) {
                    $query->visibleFor($employee)
                        ->with([
                            'exam' => ['type', 'center'],
                            'attempt.center',
                        ]);
                }
        ];
    }

    protected function setEnrollmentsAttributes(ForeignNational $foreignNational)
    {
        $foreignNational->enrollments
            ->each(function(Enrollment $enrollment){

                $enrollment->setAttribute('payment_available', 
                    $this->enrollmentPaymentRules
                        ->check($enrollment, $enrollment->exam)->available
                );
                
                $enrollment->setAttribute('exam_result', 
                    $this->resolver->execute(
                        $enrollment,
                        $enrollment->exam,
                        $enrollment->attempt
                    )
                );
        });
    }
}