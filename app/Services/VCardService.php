<?php

namespace App\Services;

use App\Models\Employee;

class VCardService
{
    public function generate(Employee $employee): string
    {
        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:' . $this->escape($employee->fullName()),
            'N:' . $this->escape(strtoupper($employee->last_name)) . ';' . $this->escape($employee->first_name) . ';;;',
        ];

        if ($employee->job_title) {
            $lines[] = 'TITLE:' . $this->escape($employee->job_title);
        }

        if ($employee->department) {
            $lines[] = 'ORG:MMI\'e;' . $this->escape($employee->department);
        }

        if ($employee->email) {
            $lines[] = 'EMAIL;TYPE=WORK:' . $employee->email;
        }

        if ($employee->phone) {
            $lines[] = 'TEL;TYPE=WORK,VOICE:' . $employee->phone;
        }

        if ($employee->postal_address) {
            $lines[] = 'ADR;TYPE=WORK:;;' . $this->escape($employee->postal_address) . ';;;;';
        }

        if ($employee->website) {
            $lines[] = 'URL;TYPE=WORK:' . $employee->website;
        }

        if ($employee->linkedin_url) {
            $lines[] = 'URL;TYPE=LinkedIn:' . $employee->linkedin_url;
        }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines) . "\r\n";
    }

    private function escape(string $value): string
    {
        return str_replace([',', ';', "\n", "\\"], ['\\,', '\\;', '\\n', '\\\\'], $value);
    }
}
