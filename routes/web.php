<?php
use App\Http\RedirectResolver;
use App\Models\ExamType;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'web'
])
    ->group(function () {
        require __DIR__.'/exams_review.php';

        require __DIR__.'/exams_conduct.php';

        require __DIR__.'/exams_types.php';

        Route::get('exams/types', function () {
            return ExamType::cached();
        });
    });

require __DIR__.'/auth.php';
require __DIR__.'/exams_session.php';
require __DIR__.'/platform_management.php';

Route::redirect('', 'login');

Route::middleware([
    'auth:web,foreignNationals'
])->get('me', function (RedirectResolver $resolver) {
    return redirect($resolver->execute());
})->name('me');