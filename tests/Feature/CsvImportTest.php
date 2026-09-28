<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use App\Services\CsvImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private CsvImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin   = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->service = app(CsvImportService::class);
    }

    private function makeCsv(string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'csv_test_') . '.csv';
        file_put_contents($path, $content);
        return new UploadedFile($path, 'test.csv', 'text/csv', null, true);
    }

    public function test_import_creates_new_employees(): void
    {
        $csv = "prenom;nom;email\nAlice;Martin;alice@mmi-e.fr\n";
        $run = $this->service->import($this->makeCsv($csv), $this->admin);
        $this->assertEquals(1, $run->rows_created);
        $this->assertDatabaseHas('employees', ['email' => 'alice@mmi-e.fr']);
    }

    public function test_import_updates_existing_employee(): void
    {
        Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'email' => 'alice@mmi-e.fr']);
        $csv = "prenom;nom;email;fonction\nAlice;Martin;alice@mmi-e.fr;Directrice\n";
        $run = $this->service->import($this->makeCsv($csv), $this->admin);
        $this->assertEquals(1, $run->rows_updated);
        $this->assertDatabaseHas('employees', ['email' => 'alice@mmi-e.fr', 'job_title' => 'Directrice']);
    }

    public function test_import_detects_unchanged(): void
    {
        Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'email' => 'alice@mmi-e.fr']);
        $csv = "prenom;nom;email\nAlice;Martin;alice@mmi-e.fr\n";
        $run = $this->service->import($this->makeCsv($csv), $this->admin);
        $this->assertEquals(1, $run->rows_unchanged);
    }

    public function test_import_detects_missing_employees(): void
    {
        Employee::create(['first_name' => 'Bob', 'last_name' => 'Absent', 'email' => 'bob@mmi-e.fr']);
        $csv = "prenom;nom;email\nAlice;Martin;alice@mmi-e.fr\n";
        $run = $this->service->import($this->makeCsv($csv), $this->admin);
        $this->assertEquals(1, $run->rows_missing);
        // Bob must NOT be deleted
        $this->assertDatabaseHas('employees', ['email' => 'bob@mmi-e.fr']);
    }

    public function test_import_reports_row_failures(): void
    {
        $csv = "prenom;nom\n;;\n"; // missing required columns
        $run = $this->service->import($this->makeCsv($csv), $this->admin);
        $this->assertGreaterThan(0, $run->rows_failed);
    }

    public function test_import_never_changes_slug_or_qr(): void
    {
        $emp          = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'email' => 'alice@mmi-e.fr']);
        $originalSlug = $emp->slug;
        $originalQr   = $emp->qr_token;

        $csv = "prenom;nom;email;fonction\nAlice;Martin;alice@mmi-e.fr;Directrice\n";
        $this->service->import($this->makeCsv($csv), $this->admin);

        $this->assertEquals($originalSlug, $emp->fresh()->slug);
        $this->assertEquals($originalQr, $emp->fresh()->qr_token);
    }

    public function test_admin_can_upload_csv_via_http(): void
    {
        $csv  = "prenom;nom;email\nAlice;Martin;alice@mmi-e.fr\n";
        $file = $this->makeCsv($csv);
        $this->actingAs($this->admin)
            ->post(route('admin.imports.store'), ['csv_file' => $file])
            ->assertRedirect();
        $this->assertDatabaseHas('employees', ['email' => 'alice@mmi-e.fr']);
    }
}
