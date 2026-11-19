<?php

namespace App\Http\Controllers\Web\ExamConduct;

use App\Enums\TaskType;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptResource;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use App\Modules\ExamConduct\AttemptSpeakingRules;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Middleware('auth:web')]
#[Authorize('attempts.employee-access', 'attempt')]
#[Prefix('attempts')]
class SpeakingController
{
    public function __construct(
        protected AttemptSpeakingRules $attemptSpeakingRules
    ){}

    #[Get('{attempt}/speaking', 'attempts.speaking.show')]
    public function show(
        Attempt $attempt
    ) {
        $result = $this->attemptSpeakingRules->get($attempt);

        if($result->isNotAvailable()){
            throw new BusinessException($result->code());
        }

        if($attempt->speaking_started_at === null){
           
            return Inertia::render('ExamConduct/SpeakingStart', [
                'backUrl' => route('exams.conduct', [
                    'exam' => $attempt->exam_id
                ], false),
                'startUrl' => route('attempts.speaking.start', [
                    'attempt' => $attempt
                ], false),
                'foreignNationalName' => $attempt->foreignNational->fullName
            ]);
        }

        $attemptWithSpeaking = $this->loadSpeaking($attempt);

        if($attempt->speaking_finished_at !== null){
            return Inertia::render('ExamConduct/SpeakingReview', [
                'attempt' => new AttemptResource($attemptWithSpeaking),
                'backUrl' => route('exams.conduct', [
                    'exam' => $attempt->exam_id
                ], false)
            ]);
        }

        return Inertia::render('ExamConduct/Speaking', [
            'attempt' => new AttemptResource($attemptWithSpeaking),
            'backUrl' => route('exams.conduct', [
                'exam' => $attempt->exam_id
            ], false),
            'finishUrl' => route('attempts.speaking.finish', [
                'attempt' => $attempt
            ], false)
        ]);
    }

    #[Post('{attempt}/speaking/start', 'attempts.speaking.start')]
    public function start(Attempt $attempt)
    {
        $result = $this->attemptSpeakingRules->start($attempt);

        if($result->isNotAvailable()){
            throw new BusinessException($result->code());
        }
        
        $attempt->update([
            'speaking_started_at' => Carbon::now()
        ]);

        return redirect()->route('attempts.speaking.show', [
            'attempt' => $attempt
        ]);

    }

    #[Post('{attempt}/speaking/finish', 'attempts.speaking.finish')]
    public function finish(Attempt $attempt): RedirectResponse
    {
        $result = $this->attemptSpeakingRules->finish($attempt);

        if($result->isNotAvailable()){
            throw new BusinessException($result->code());
        }
        
        $attempt->update([
            'speaking_finished_at' => Carbon::now()
        ]);

        return redirect()->route('attempts.speaking.show', [
            'attempt' => $attempt
        ]);
    }

    protected function loadSpeaking(Attempt $attempt)
    {
        $attempt->load([
            'taskVariants' => function (BelongsToMany $query) use ($attempt) {
                $query->whereHas('task', function (Builder $q) {
                    $q->where('type', TaskType::Speaking);
                })->with([
                    'task',
                    'answers',
                    'attemptAnswers' => function ($query) use ($attempt) {
                        $query->where('attempt_id', $attempt->id);
                    },
                ]);
            },
        ]);

        $attempt->taskVariants = $attempt->taskVariants
            ->sortBy('task.order');

        return $attempt;
    }
}