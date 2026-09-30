<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $media = $query->paginate(24)->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'data' => $media->map(fn($m) => [
                    'id'       => $m->id,
                    'name'     => $m->name,
                    'url'      => route('admin.media.thumb', $m),
                    'category' => $m->category,
                    'size'     => $m->formattedSize(),
                ]),
                'meta' => [
                    'current_page' => $media->currentPage(),
                    'last_page'    => $media->lastPage(),
                    'total'        => $media->total(),
                ],
            ]);
        }

        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file'     => 'required|file|mimes:png,jpg,jpeg,webp|max:10240',
            'category' => 'required|in:logo,banner,photo,image,other',
            'name'     => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $storedName, 'local');

        [$width, $height] = $this->getImageDimensions($file->getRealPath(), $file->getMimeType());

        $this->generateThumbnail($file->getRealPath(), $storedName, $file->getMimeType());

        $media = Media::create([
            'name'          => $request->name ?: $file->getClientOriginalName(),
            'original_name' => $file->getClientOriginalName(),
            'stored_name'   => $storedName,
            'disk'          => 'local',
            'path'          => $path,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'width'         => $width,
            'height'        => $height,
            'category'      => $request->category,
            'uploaded_by'   => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id'           => $media->id,
                'name'         => $media->name,
                'url'          => $media->thumbUrl(),
                'original_url' => $media->originalUrl(),
                'width'        => $media->width,
                'height'       => $media->height,
                'mime_type'    => $media->mime_type,
                'size'         => $media->size,
                'category'     => $media->category,
            ], 201);
        }

        return redirect()->route('admin.media.index')->with('success', 'Média uploadé avec succès.');
    }

    public function show(Media $medium)
    {
        $path = Storage::disk('local')->path($medium->path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, ['Content-Type' => $medium->mime_type]);
    }

    public function thumb(Media $medium)
    {
        $thumbPath = storage_path('app/private/thumbs/' . $medium->stored_name);

        if (!file_exists($thumbPath)) {
            $origPath = Storage::disk('local')->path($medium->path);
            if (!file_exists($origPath)) {
                abort(404);
            }
            $this->generateThumbnail($origPath, $medium->stored_name, $medium->mime_type);
        }

        if (!file_exists($thumbPath)) {
            return $this->show($medium);
        }

        return response()->file($thumbPath, ['Content-Type' => $medium->mime_type]);
    }

    public function destroy(Media $medium)
    {
        $usedInCards = \App\Models\Card::whereJsonContains('config->elements', ['data' => ['media_id' => $medium->id]])->exists()
            || \App\Models\Organization::where('logo_media_id', $medium->id)->orWhere('secondary_logo_media_id', $medium->id)->exists()
            || \App\Models\Group::where('logo_media_id', $medium->id)->exists();

        if ($usedInCards) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Ce média est utilisé par une ou plusieurs cartes.'], 409);
            }
            return back()->with('error', 'Ce média est utilisé et ne peut pas être supprimé.');
        }

        Storage::disk('local')->delete($medium->path);
        @unlink(storage_path('app/private/thumbs/' . $medium->stored_name));

        $medium->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.media.index')->with('success', 'Média supprimé.');
    }

    private function getImageDimensions(string $path, string $mime): array
    {
        try {
            [$w, $h] = getimagesize($path);
            return [$w, $h];
        } catch (\Throwable) {
            return [null, null];
        }
    }

    private function generateThumbnail(string $sourcePath, string $storedName, string $mime): void
    {
        try {
            $thumbDir = storage_path('app/private/thumbs');
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }

            $src = match ($mime) {
                'image/png'  => imagecreatefrompng($sourcePath),
                'image/webp' => imagecreatefromwebp($sourcePath),
                default      => imagecreatefromjpeg($sourcePath),
            };

            if (!$src) return;

            $origW = imagesx($src);
            $origH = imagesy($src);
            $size = 300;

            $ratio = min($size / $origW, $size / $origH);
            $newW = (int) ($origW * $ratio);
            $newH = (int) ($origH * $ratio);

            $thumb = imagecreatetruecolor($newW, $newH);

            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
            }

            imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

            $destPath = $thumbDir . '/' . $storedName;

            match ($mime) {
                'image/png'  => imagepng($thumb, $destPath),
                'image/webp' => imagewebp($thumb, $destPath),
                default      => imagejpeg($thumb, $destPath, 85),
            };

            imagedestroy($src);
            imagedestroy($thumb);
        } catch (\Throwable) {
            // Thumbnail generation is best-effort
        }
    }
}
