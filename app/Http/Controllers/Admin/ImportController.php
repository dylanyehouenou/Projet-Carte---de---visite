<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportCsvRequest;
use App\Models\ImportRun;
use App\Services\AuditService;
use App\Services\CsvImportService;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function __construct(
        private readonly CsvImportService $importer,
        private readonly AuditService $audit,
    ) {}

    public function index(): View
    {
        $imports = ImportRun::with('importedBy')->latest('created_at')->paginate(20);
        return view('admin.imports.index', compact('imports'));
    }

    public function create(): View
    {
        return view('admin.imports.create');
    }

    public function store(ImportCsvRequest $request): \Illuminate\Http\RedirectResponse
    {
        $run = $this->importer->import($request->file('csv_file'), $request->user());
        $this->audit->log($request->user(), 'csv.imported', $run, ['filename' => $run->filename]);
        return redirect()->route('admin.imports.show', $run)->with('success', 'Import terminé.');
    }

    public function show(ImportRun $import): View
    {
        return view('admin.imports.show', compact('import'));
    }
}
