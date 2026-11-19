<?php

namespace App\Http\Controllers\Web\CenterManagement;

use App\Http\Requests\Statistics\StatisticsRequest;
use App\Models\Attempt;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ForeignNational;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;

class StatisticsController
{
    #[Get('center-manage/statistics', 'statistics.index')]
    public function index(StatisticsRequest $request) 
    {
        $dateFrom = Carbon::parse($request->input('dateFrom'))->startOfDay();
        $dateTo = Carbon::parse($request->input('dateTo'))->endOfDay();

        $period = [$dateFrom, $dateTo];

        $examsCount = Exam::query()
            ->whereBetween('begin_time', $period)
            ->count();
        
        $foreignNationalsCount = ForeignNational::query()
            ->whereBetween('created_at', $period)
            ->count();

        $enrollmentsCount = Enrollment::query()  
            ->whereBetween('created_at', $period)
            ->count();

        $absentCount = Enrollment::query()  
            ->whereBetween('created_at', $period)
            ->whereDoesntHave('attempt')
            ->whereHas('exam', function(Builder $query){
                $query->notCancelled();
            })
            ->count();
        
        $cancelledEnrolmentsCount = Enrollment::query()  
            ->whereBetween('created_at', $period)
            ->whereNotNull('cancelled_at')
            ->count();

        $attemptsCount = Attempt::query()
            ->whereBetween('created_at', $period)
            ->count();

        $successAttemptsCount = Attempt::query()
            ->passed()
            ->whereBetween('created_at', $period)
            ->count();

        $failedAttemptsCount = Attempt::query()
            ->failed()
            ->whereBetween('created_at', $period)
            ->count();
        
        $attendanceCount = $enrollmentsCount - $absentCount;

        $passRate = $attemptsCount > 0
            ? round($successAttemptsCount / $attemptsCount * 100)
            : 0;

        $attendanceRate = $enrollmentsCount > 0
            ? round($attendanceCount / $enrollmentsCount * 100)
            : 0;


        return Inertia::render('CenterManage/Statistics', [
            'filters' => [
                'dateFrom' => $dateFrom->toDateString(),
                'dateTo' => $dateTo->toDateString(),
            ],

            'groups' => [
                [
                    'title' => 'Общие показатели',
                    'icon' => 'chart',
                    'cards' => [
                        [
                            'title' => 'Проведено экзаменов',
                            'value' => $examsCount,
                            'description' => 'За выбранный период',
                            'icon' => 'school',
                            'color' => 'primary',
                        ],
                        [
                            'title' => 'Всего записей',
                            'value' => $enrollmentsCount,
                            'description' => 'Записи на экзамены',
                            'icon' => 'clipboard',
                            'color' => 'info',
                        ], 
                        [ 
                            'title' => 'Отмененные записи',
                            'value' => $cancelledEnrolmentsCount,
                            'description' => 'Отказались сдавать',
                            'icon' => 'clipboard',
                            'color' => 'red',
                        ],
                        [
                            'title' => 'Иностранных граждан',
                            'value' => $foreignNationalsCount,
                            'description' => 'Добавлено за период',
                            'icon' => 'account',
                            'color' => 'secondary',
                        ],
                    ],
                ],
                [
                    'title' => 'Результаты экзаменов',
                    'icon' => 'exam',
                    'cards' => [
                        [
                            'title' => 'Всего попыток',
                            'value' => $attemptsCount,
                            'description' => 'Все результаты',
                            'icon' => 'chart',
                            'color' => 'primary',
                        ],
                        [
                            'title' => 'Успешно сдано',
                            'value' => $successAttemptsCount,
                            'description' => 'Успешные попытки',
                            'icon' => 'check',
                            'color' => 'success',
                        ],
                        [
                            'title' => 'Не сдано',
                            'value' => $failedAttemptsCount,
                            'description' => 'Неуспешные попытки',
                            'icon' => 'close',
                            'color' => 'error',
                        ],
                        [
                            'title' => 'Не явились',
                            'value' => $absentCount,
                            'description' => 'Без попытки',
                            'icon' => 'absent',
                            'color' => 'warning',
                        ],
                    ],
                ],
            ],

            'analytics' => [
                [
                    'title' => 'Успешность попыток',
                    'description' => 'Доля успешно сданных попыток',
                    'value' => $passRate,
                    'unit' => '%',
                    'color' => 'success',
                    'details' => [
                        [
                            'label' => 'Успешно',
                            'value' => $successAttemptsCount,
                        ],
                        [
                            'label' => 'Не сдано',
                            'value' => $failedAttemptsCount,
                        ],
                    ],
                ],
                [
                    'title' => 'Явка на экзамены',
                    'description' => 'Доля записанных без пропуска',
                    'value' => $attendanceRate,
                    'unit' => '%',
                    'color' => 'info',
                    'details' => [
                        [
                            'label' => 'Явились',
                            'value' => $attendanceCount,
                        ],
                        [
                            'label' => 'Не явились',
                            'value' => $absentCount,
                        ],
                    ],
                ],
            ],
        ]);
    }
}