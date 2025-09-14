<?php

namespace App\Exports;

use App\Models\Voter;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class VotersExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    /** @var 'student'|'staff'|'unified' */
    protected string $type;
    protected ?int $year;
    protected ?string $filterClass;
    protected ?string $filterMajor;

    public function __construct(string $type, ?int $year = null, ?string $filterClass = null, ?string $filterMajor = null)
    {
        $this->type = in_array($type, ['student', 'staff', 'unified'], true) ? $type : 'unified';
        $this->year = $year;
        $this->filterClass = $filterClass ? trim($filterClass) : null;
        $this->filterMajor = $filterMajor ? trim($filterMajor) : null;
    }

    public function query(): Builder
    {
        return Voter::query()
            ->when($this->year, fn($q) => $q->where('year', $this->year))
            ->when($this->type !== 'unified', fn($q) => $q->where('type', $this->type))
            ->when($this->filterClass, fn($q) => $q->where('class', 'like', '%'.$this->filterClass.'%'))
            ->when($this->filterMajor, fn($q) => $q->where('major', 'like', '%'.$this->filterMajor.'%'))
            ->leftJoin('voter_plain_tokens', 'voter_plain_tokens.voter_id', '=', 'voters.id')
            ->orderBy('voters.id')
            ->select('voters.*', 'voter_plain_tokens.token_encrypted');
    }

    public function headings(): array
    {
        return match ($this->type) {
            'student' => ['type','identifier','name','class','major','token'],
            'staff' => ['type','identifier','name','position','token'],
            default => ['type','identifier','name','class','major','position','token'],
        };
    }

    public function map($row): array
    {
        // Decrypt token if available
        $plainToken = '';
        if (!empty($row->token_encrypted)) {
            try {
                $plainToken = Crypt::decryptString($row->token_encrypted);
            } catch (\Throwable $e) {
                $plainToken = '';
            }
        }

        // Return row based on selected type
        if ($this->type === 'student') {
            return [
                $row->type,
                (string) $row->identifier,
                $row->name,
                $row->class,
                $row->major,
                $plainToken,
            ];
        } elseif ($this->type === 'staff') {
            return [
                $row->type,
                (string) $row->identifier,
                $row->name,
                $row->position,
                $plainToken,
            ];
        }

        return [
            $row->type,
            (string) $row->identifier,
            $row->name,
            $row->class,
            $row->major,
            $row->position,
            $plainToken,
        ];
    }

    public function columnFormats(): array
    {
        // Ensure token is treated as text in Excel
        return match ($this->type) {
            'student' => [
                'F' => NumberFormat::FORMAT_TEXT,
            ],
            'staff' => [
                'E' => NumberFormat::FORMAT_TEXT,
            ],
            default => [
                'G' => NumberFormat::FORMAT_TEXT,
            ],
        };
    }
}
