<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
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
        if ($user) {
            $user->loadMissing(['role', 'position', 'organizationUnit']);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
                'just_logged_in' => fn () => $request->session()->get('just_logged_in'),
            ],
            'appName' => config('app.name', 'PLN Nusantara Power UP Sengkang'),
            'branding' => function () {
                $logoPath = public_path('assets/bumn/logo-pln.png');
                $bgPath = public_path('assets/bumn/bg-sengkang-hd.jpg');
                return [
                    'logo_url' => file_exists($logoPath) ? '/assets/bumn/logo-pln.png?v=' . filemtime($logoPath) : '/assets/bumn/logo-pln.png',
                    'login_bg_url' => file_exists($bgPath) ? '/assets/bumn/bg-sengkang-hd.jpg?v=' . filemtime($bgPath) : '/assets/bumn/bg-sengkang-hd.jpg',
                ];
            },
        ];
    }
}
