<?php

namespace App\Services;

class ProductionReadinessService
{
    /** Solo resultados booleanos: nunca devolver valores de configuración. */
    public function checks(): array
    {
        $https = fn ($url) => is_string($url) && filter_var($url, FILTER_VALIDATE_URL)
            && parse_url($url, PHP_URL_SCHEME) === 'https'
            && ! parse_url($url, PHP_URL_USER) && ! parse_url($url, PHP_URL_PASS);
        $queue = config('queue.connections.prometeo', []);

        return [
            'APP_ENV=production' => app()->environment('production'),
            'APP_DEBUG desactivado' => config('app.debug') === false,
            'APP_KEY configurada' => filled(config('app.key')),
            'APP_URL con HTTPS' => (bool) $https(config('app.url')),
            'Cookie de sesión segura y HttpOnly' => config('session.secure') === true && config('session.http_only') === true,
            'SameSite compatible' => in_array(config('session.same_site'), ['lax', 'strict'], true),
            'Sesiones persistentes' => in_array(config('session.driver'), ['database', 'redis', 'file'], true),
            'Caché persistente para bloqueos y reinicio de trabajadores' => in_array(config('cache.default'), ['database', 'redis', 'file'], true),
            'Cola IA en la misma base y reserva mayor que timeout' => ($queue['driver'] ?? null) === 'database'
                && ($queue['connection'] ?? null) === null && ($queue['queue'] ?? null) === 'prometeo-ia'
                && (int) ($queue['retry_after'] ?? 0) > 75,
            'API IA configurada con HTTPS' => (bool) $https(config('services.prometeo_ia.url')),
            'Correo con transporte operativo' => ! in_array(config('mail.default'), [null, 'log', 'array'], true),
            'GD disponible para fotos de perfil' => extension_loaded('gd'),
            'Frontend compilado' => $this->assetsReady(),
            'Servidor Vite de desarrollo desactivado' => ! is_file(public_path('hot')),
            'Directorios de ejecución escribibles' => is_writable(storage_path()) && is_writable(base_path('bootstrap/cache')),
            'Extensiones PHP para trabajador' => extension_loaded('pdo') && extension_loaded('mbstring')
                && extension_loaded('openssl') && extension_loaded('pcntl'),
        ];
    }

    private function assetsReady(): bool
    {
        $path = public_path('build/manifest.json');
        $manifest = is_file($path) ? json_decode(file_get_contents($path), true) : null;
        foreach (['resources/css/app.css', 'resources/js/app.js'] as $entry) {
            $file = $manifest[$entry]['file'] ?? null;
            if (! is_string($file) || str_contains($file, '..') || ! is_file(public_path('build/'.$file))) {
                return false;
            }
        }

        return true;
    }
}
