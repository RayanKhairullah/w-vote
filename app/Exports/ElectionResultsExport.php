<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class ElectionResultsExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new ElectionSummarySheet($this->data),
            new VoteDetailsSheet($this->data),
            new CandidateResultsSheet($this->data),
            new NonParticipatingVotersSheet($this->data),
        ];
    }
}

class ElectionSummarySheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths, WithCustomStartCell
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $election = $this->data['election'];
        $stats = $this->data['statistics'];

        return collect([
            ['Informasi Pemilihan', ''],
            ['Nama Pemilihan', $election['name']],
            ['Tahun', $election['year']],
            ['Waktu Mulai', $election['start_at'] ?? '-'],
            ['Waktu Selesai', $election['end_at'] ?? '-'],
            ['Status', $election['status']],
            ['Laporan Dibuat', $election['generated_at']],
            ['', ''],
            ['Statistik Partisipasi', ''],
            ['Total Pemilih Terdaftar', number_format($stats['total_voters'])],
            ['Pemilih yang Berpartisipasi', number_format($stats['participating_voters'])],
            ['Pemilih yang Tidak Berpartisipasi', number_format($stats['non_participating_voters'])],
            ['Tingkat Partisipasi', $stats['participation_rate']],
            ['Total Suara Masuk', number_format($stats['total_votes'])],
            ['', ''],
            ['Komposisi Pemilih', ''],
            ['Total Siswa', number_format($stats['student_voters'])],
            ['Total Staff', number_format($stats['staff_voters'])],
            ['Siswa yang Memilih', number_format($stats['student_participation'])],
            ['Staff yang Memilih', number_format($stats['staff_participation'])],
        ]);
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function headings(): array
    {
        return ['Kategori', 'Nilai'];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:A' => ['font' => ['bold' => true]],
            'A1:B1' => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4F46E5']],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }
}

class VoteDetailsSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['vote_details']);
    }

    public function title(): string
    {
        return 'Detail Voting';
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu Voting',
            'Nama Pemilih',
            'Tipe Pemilih',
            'Identifier',
            'Kelas/Posisi',
            'Jurusan',
            'Kandidat Dipilih',
            'Nomor Urut',
            'Nama Ketua',
            'Nama Wakil',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 18,
            'C' => 25,
            'D' => 12,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 35,
            'I' => 10,
            'J' => 20,
            'K' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '10B981']],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

class CandidateResultsSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['candidate_summary']);
    }

    public function title(): string
    {
        return 'Hasil Kandidat';
    }

    public function headings(): array
    {
        return [
            'Nomor Urut',
            'Nama Ketua',
            'Nama Wakil',
            'Jumlah Suara',
            'Persentase',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 25,
            'C' => 25,
            'D' => 15,
            'E' => 12,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F59E0B']],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

class NonParticipatingVotersSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['non_participating_voters']);
    }

    public function title(): string
    {
        return 'Pemilih Belum Voting';
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tipe',
            'Identifier',
            'Kelas/Posisi',
            'Jurusan',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 12,
            'C' => 15,
            'D' => 15,
            'E' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'EF4444']],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
