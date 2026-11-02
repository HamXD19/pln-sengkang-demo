<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateSubmissions
{
    /**
     * Handle an incoming request.
     * Mencegah double-click atau multi-submit bersamaan pada seluruh operasi POST/PUT/PATCH/DELETE
     * untuk mencegah tabrakan data (collision), duplikasi record, atau race condition pada database.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya berlaku untuk request mutasi state database (POST, PUT, PATCH, DELETE)
        $method = strtoupper($request->method());
        if (! in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        // Pengecualian untuk endpoint polling atau otentikasi login/logout
        if ($request->is('rapat/*/ping') || $request->is('logout') || $request->is('login')) {
            return $next($request);
        }

        $userId = $request->user()?->id ?? $request->ip();
        $path = $request->path();

        // Kumpulkan parameter data di luar CSRF token dan password
        $params = $request->except(['_token', 'password', 'password_confirmation', '_method']);

        // Tambahkan metadata berkas unggahan jika ada
        $fileInfo = [];
        foreach ($request->allFiles() as $key => $file) {
            if (is_array($file)) {
                foreach ($file as $f) {
                    $fileInfo[] = $f ? ($f->getClientOriginalName() . ':' . $f->getSize()) : '';
                }
            } else {
                $fileInfo[] = $file ? ($file->getClientOriginalName() . ':' . $file->getSize()) : '';
            }
        }

        $payloadFingerprint = md5(json_encode($params) . '|' . implode('|', $fileInfo));
        $lockKey = "req_lock:{$userId}:{$method}:{$path}:{$payloadFingerprint}";

        // Simpan kunci gembok selama 2 detik. Cache::add mengembalikan false jika kunci sudah ada.
        if (! Cache::add($lockKey, true, 2)) {
            // Duplikat terdeteksi dalam jendela 2 detik!
            if ($request->header('X-Inertia') || $request->wantsJson()) {
                return back()->with('warning', 'Permintaan Anda sedang diproses. Mohon tidak mengklik tombol berulang kali.');
            }

            return back();
        }

        return $next($request);
    }
}
