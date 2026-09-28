<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\ImportRun;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class CsvImportService
{
    public function import(UploadedFile $file, User $admin): ImportRun
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        $headers = null;
        $report = [
            'created'   => [],
            'updated'   => [],
            'unchanged' => [],
            'missing'   => [],
            'failed'    => [],
        ];

        $processedIds = [];
        $rowIndex = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if ($headers === null) {
                    $headers = array_map('trim', $row);
                    continue;
                }

                $rowIndex++;
                if (count($row) !== count($headers)) {
                    $report['failed'][] = ['row' => $rowIndex, 'reason' => 'Nombre de colonnes incorrect'];
                    continue;
                }

                $data = array_combine($headers, array_map('trim', $row));
                $mapped = $this->mapRow($data);

                if (empty($mapped['first_name']) || empty($mapped['last_name'])) {
                    $report['failed'][] = ['row' => $rowIndex, 'reason' => 'Prénom ou nom manquant', 'data' => $data];
                    continue;
                }

                try {
                    $employee = $this->findEmployee($mapped);

                    if ($employee === null) {
                        $employee = Employee::create($mapped);
                        $report['created'][] = ['id' => $employee->id, 'name' => $employee->fullName()];
                    } else {
                        $processedIds[] = $employee->id;
                        $fillable = array_diff_key($mapped, array_flip(['slug', 'qr_token']));
                        $changed = false;
                        foreach ($fillable as $key => $value) {
                            if ($employee->$key !== $value) {
                                $changed = true;
                                break;
                            }
                        }

                        if ($changed) {
                            $employee->update($fillable);
                            $report['updated'][] = ['id' => $employee->id, 'name' => $employee->fullName()];
                        } else {
                            $report['unchanged'][] = ['id' => $employee->id, 'name' => $employee->fullName()];
                        }
                    }

                    $processedIds[] = $employee->id;
                } catch (Throwable $e) {
                    $report['failed'][] = ['row' => $rowIndex, 'reason' => $e->getMessage()];
                }
            }

            // Detect missing employees (in DB but not in CSV)
            $missing = Employee::whereNotIn('id', array_unique($processedIds))->get();
            foreach ($missing as $emp) {
                $report['missing'][] = ['id' => $emp->id, 'name' => $emp->fullName()];
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        } finally {
            fclose($handle);
        }

        return ImportRun::create([
            'filename'     => $file->getClientOriginalName(),
            'imported_by'  => $admin->id,
            'rows_total'   => $rowIndex,
            'rows_created' => count($report['created']),
            'rows_updated' => count($report['updated']),
            'rows_unchanged' => count($report['unchanged']),
            'rows_missing' => count($report['missing']),
            'rows_failed'  => count($report['failed']),
            'report_json'  => $report,
        ]);
    }

    private function findEmployee(array $mapped): ?Employee
    {
        if (!empty($mapped['external_key'])) {
            $emp = Employee::where('external_key', $mapped['external_key'])->first();
            if ($emp) return $emp;
        }

        if (!empty($mapped['email'])) {
            return Employee::where('email', $mapped['email'])->first();
        }

        return null;
    }

    private function mapRow(array $data): array
    {
        // Flexible column mapping — handles Signitic CSV headers
        $aliases = [
            'first_name'     => ['first_name', 'prenom', 'prénom', 'firstname'],
            'last_name'      => ['last_name', 'nom', 'lastname'],
            'job_title'      => ['job_title', 'fonction', 'poste', 'title'],
            'department'     => ['department', 'service', 'dept', 'département'],
            'email'          => ['email', 'mail', 'e-mail'],
            'phone'          => ['phone', 'telephone', 'téléphone', 'tel', 'tél'],
            'postal_address' => ['postal_address', 'adresse', 'address'],
            'website'        => ['website', 'site', 'url', 'site_web'],
            'linkedin_url'   => ['linkedin_url', 'linkedin'],
            'calendly_url'   => ['calendly_url', 'calendly'],
            'external_key'   => ['external_key', 'id', 'signitic_id', 'identifiant'],
        ];

        $lower = array_change_key_case($data, CASE_LOWER);
        $result = [];

        foreach ($aliases as $field => $candidates) {
            foreach ($candidates as $candidate) {
                if (isset($lower[$candidate]) && $lower[$candidate] !== '') {
                    $result[$field] = $lower[$candidate];
                    break;
                }
            }
        }

        return $result;
    }
}
