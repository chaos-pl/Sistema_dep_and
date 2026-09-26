<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfilePhotoController extends Controller
{
    private function ownedPath(?string $path): bool
    {
        return is_string($path) && preg_match('/\Aavatars\/[a-f0-9-]+\.jpg\z/', $path) === 1;
    }

    public function show(Request $request)
    {
        $path = $request->user()->persona()->value('foto_perfil');
        abort_unless($this->ownedPath($path) && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=3000,max_height=3000'],
            'position_x' => ['nullable', 'numeric', 'between:0,100'],
            'position_y' => ['nullable', 'numeric', 'between:0,100'],
        ]);
        if (! $request->user()->persona) {
            throw ValidationException::withMessages(['photo' => 'Completa primero tus datos personales para añadir una foto.']);
        }
        if (! extension_loaded('gd')) {
            throw ValidationException::withMessages(['photo' => 'El servidor necesita habilitar GD para procesar fotografías.']);
        }
        $source = @imagecreatefromstring(file_get_contents($request->file('photo')->getRealPath()));
        if (! $source) {
            throw ValidationException::withMessages(['photo' => 'No se pudo procesar la imagen.']);
        }
        // Apply camera orientation before cropping; output JPEG contains no EXIF.
        if ($request->file('photo')->getMimeType() === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($request->file('photo')->getRealPath());
            $orientation = (int) ($exif['Orientation'] ?? 1);
            if (in_array($orientation, [2, 4, 5, 7], true)) {
                imageflip($source, IMG_FLIP_HORIZONTAL);
            }
            $angle = match ($orientation) {
                3, 4 => 180,
                5, 6 => -90,
                7, 8 => 90,
                default => 0,
            };
            if ($angle) {
                $rotated = imagerotate($source, $angle, 0);
                imagedestroy($source);
                $source = $rotated;
            }
        }
        $width = imagesx($source);
        $height = imagesy($source);
        $side = min($width, $height);
        $x = (int) round(($width - $side) * (float) $request->input('position_x', 50) / 100);
        $y = (int) round(($height - $side) * (float) $request->input('position_y', 50) / 100);
        $target = imagecreatetruecolor(512, 512);
        imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255));
        imagecopyresampled($target, $source, 0, 0, $x, $y, 512, 512, $side, $side);
        ob_start();
        imagejpeg($target, null, 88);
        $bytes = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);
        $path = 'avatars/'.Str::uuid().'.jpg';
        abort_unless(Storage::disk('local')->put($path, $bytes), 503);
        try {
            $old = DB::transaction(function () use ($request, $path) {
                $persona = $request->user()->persona()->lockForUpdate()->firstOrFail();
                $old = $persona->foto_perfil;
                $persona->forceFill(['foto_perfil' => $path])->save();

                return $old;
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }
        if ($this->ownedPath($old)) {
            Storage::disk('local')->delete($old);
        }

        return back()->with('photo_status', 'Tu foto de perfil se actualizó.');
    }

    public function destroy(Request $request)
    {
        $old = DB::transaction(function () use ($request) {
            $persona = $request->user()->persona()->lockForUpdate()->first();
            $old = $persona?->foto_perfil;
            $persona?->forceFill(['foto_perfil' => null])->save();

            return $old;
        });
        if ($this->ownedPath($old)) {
            Storage::disk('local')->delete($old);
        }

        return back()->with('photo_status', 'Foto eliminada. Ahora se muestran tus iniciales.');
    }
}
