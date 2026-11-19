<?php

namespace App\Http\Controllers\Web\PlatformManage;

use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;

#[Middleware('auth:web')]
#[Authorize('platform-manage')]
class HealthCheckController
{
    #[Get('admin/health-check', 'platform-admin.health-check')]
    public function check(): \Inertia\Response
    {
        $disk = Storage::disk('public');

        $directoryHasFiles = fn (string $path): bool =>
            \count($disk->allFiles($path)) > 0;

        return Inertia::render('PlatformAdmin/HealthCheck', [
            'groups' => [
                [
                    'title' => 'ФРДО',
                    'icon' => 'file',
                    'items' => [
                        [
                            'title' => 'Шаблон реестра',
                            'description' => 'templates/references_frdo.xlsx',
                            'status' => $disk->exists('templates/references_frdo.xlsx'),
                            'icon' => 'file',
                        ],
                        [
                            'title' => 'Шаблон сертификатов',
                            'description' => 'templates/certificates_frdo.xlsx',
                            'status' => $disk->exists('templates/certificates_frdo.xlsx'),
                            'icon' => 'file',
                        ],
                    ],
                ],
                [
                    'title' => 'Основные ресурсы',
                    'icon' => 'folder',
                    'items' => [
                        [
                            'title' => 'Страны',
                            'description' => 'countries.json',
                            'status' => $disk->exists('./countries.json'),
                            'icon' => 'earth',
                        ],
                        [
                            'title' => 'Аудиофайлы',
                            'description' => 'audios/',
                            'status' => $directoryHasFiles('audios'),
                            'icon' => 'audio',
                        ],
                        [
                            'title' => 'Изображения',
                            'description' => 'images/',
                            'status' => $directoryHasFiles('images'),
                            'icon' => 'image',
                        ],
                    ],
                ],
            ],
        ]);
    }
}