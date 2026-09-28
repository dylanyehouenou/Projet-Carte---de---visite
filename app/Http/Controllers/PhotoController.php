<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function show(string $slug): Response
    {
        $employee = Employee::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if (!$employee->photo_path || !Storage::disk('local')->exists($employee->photo_path)) {
            abort(404);
        }

        $content = Storage::disk('local')->get($employee->photo_path);
        $mime = mime_content_type(Storage::disk('local')->path($employee->photo_path)) ?: 'image/jpeg';

        return response($content, 200, [
            'Content-Type'  => $mime,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
