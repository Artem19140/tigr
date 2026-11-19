<?php
use App\Http\RedirectResolver;
use App\Models\ExamType;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:web'
])
->group(function () {
    require __DIR__.'/exams_types.php';

    Route::get('exams/types', fn () => ExamType::cached());
});

require __DIR__.'/auth.php';
require __DIR__.'/exams_session.php';
require __DIR__.'/platform_management.php';

Route::redirect('', 'login');

Route::middleware([
    'auth:web,foreignNationals'
])
->get('me', fn (RedirectResolver $resolver) => redirect($resolver->execute()))
->name('me');