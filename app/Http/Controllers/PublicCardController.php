<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\QrCodeService;
use App\Services\VCardService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicCardController extends Controller
{
    public function __construct(
        private readonly VCardService $vcard,
        private readonly QrCodeService $qr,
    ) {}

    public function show(string $slug): View|Response
    {
        $employee = Employee::where('slug', $slug)->firstOrFail();

        if (!$employee->is_active) {
            return response()->view('public.disabled', compact('employee'), 410);
        }

        return view('public.card', compact('employee'));
    }

    public function vcard(string $slug): Response
    {
        $employee = Employee::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $content = $this->vcard->generate($employee);

        return response($content, 200, [
            'Content-Type'        => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $employee->slug . '.vcf"',
        ]);
    }

    public function qrPng(string $slug): Response
    {
        $employee = Employee::where('slug', $slug)->firstOrFail();
        $png = $this->qr->generatePng($employee);

        return response($png, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'inline; filename="qr-' . $employee->slug . '.png"',
            'Cache-Control'       => 'public, max-age=86400',
        ]);
    }
}
