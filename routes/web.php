<?php
use App\Http\RedirectResolver;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'meta',
    'auth',
    'employee.active'
])
    ->group(function () {
        require __DIR__.'/foreign_nationals.php';

        require __DIR__.'/enrollments.php';

        require __DIR__.'/reports.php';

        require __DIR__.'/center_management.php';

        require __DIR__.'/exams.php';

        require __DIR__.'/exams_review.php';

        require __DIR__.'/exams_conduct.php';
     
    });

require __DIR__.'/auth.php';
require __DIR__.'/exams_session.php';
require __DIR__.'/platform_management.php';

Route::get('/', function(){
    return redirect('login');
});

Route::middleware([
    'meta',
    'auth:web,foreignNationals'
])->get('me', function (RedirectResolver $resolver) {
    return redirect($resolver->execute());
})->name('me');