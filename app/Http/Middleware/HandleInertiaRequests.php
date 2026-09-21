<?php

namespace App\Http\Middleware;

use App\Models\ForeignNational;
use App\Navigation\CenterManageNavigation;
use App\Navigation\ExamNavigation;
use App\Navigation\MainMenuNavigation;
use App\Models\Employee;
use App\Navigation\ReportNavigation;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        private MainMenuNavigation $menuNavigation,
        private ReportNavigation $reportNavigation,
        protected CenterManageNavigation $centerManageNavigation,
        private ExamNavigation $examNavigation
    ){}
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        
        return array_merge(parent::share($request), [
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],

            'auth.user' => fn () => $user ?  $this->resolveUser($user) : null,

            'auth.navigation' => fn () => $user ? $this->resolveNavigation($user) : null
        ]);
    }

    protected function resolveUser(Employee |  ForeignNational $user): array | null
    {
        if ($user instanceof ForeignNational){
            return null;
        }
        return $user->only('id', 'surname', 'name', 'email');
    }

    protected function resolveNavigation(Employee |  ForeignNational $user): array | null
    {
        if ($user instanceof ForeignNational){
            return null;
        }
        
        $navigation = [];

        $navigation['menu'] = $this->menuNavigation->resolve($user);

        $navigation['auth'] = [
            'logout' => [
                'url' => route('logout', [], false)
            ],
            'logoutAll' => [
                'url' => route('logout.all', [], false)
            ]
        ];

        if(request()->routeIs('reports*')){
            $navigation['reports'] = $this->reportNavigation->resolve($user);
        }

        if(request()->is('center-manage*')){
            $navigation['centerManage'] = $this->centerManageNavigation->resolve($user);
        }

        if(request()->routeIs(
            'exams.show',
            'exams.conduct',
            'exams.review'
        )){
            $navigation['exam'] = $this->examNavigation->resolve(
                $user, 
                request()->route('exam')
            );
        }

        return $navigation; 
    }
}
