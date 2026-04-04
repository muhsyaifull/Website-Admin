<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\TourSession;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class BookingExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithEvents, WithCustomValueBinder
{
    protected $startDate;
    protected $endDate;
    protected $status;

    protected const MONTH_NAMES = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];

    public function __construct($startDate = null, $endDate = null, $status = null, $tourId = null)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $this->endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : null;
        $this->status = $status;
        $this->tourId = $tourId;
    }

    public function collection()
    {
        $bookings = Booking::with([
            'bookingSessions.educator',
        ])
            ->when($this->startDate, fn($q) => $q->where('visit_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->where('visit_date', '<=', $this->endDate))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->tourId, function ($q) {
                $q->whereHas('bookingSessions', function ($q2) {
                    $q2->where('booking_sessions.tour_id', $this->tourId);
                });
            })
            ->orderBy('visit_date')
            ->get();

        $rows = collect();

        $byDate = $bookings->groupBy(fn($b) => $b->visit_date?->format('Y-m-d'));

        foreach ($byDate as $dateStr => $dateBookings) {
            $bySession = $dateBookings->groupBy(function ($booking) {
                $session = $this->tourId
                    ? $booking->getSessionForTour($this->tourId)
                    : $booking->bookingSessions->first();
                return $session ? $session->id : 'no_session';
            });

            $bySession = $bySession->sortBy(function ($groupBookings) {
                $session = $groupBookings->first()->bookingSessions->first();
                return $session ? $session->start_time : '99:99';
            });

            foreach ($bySession as $sessionId => $groupBookings) {
                $session = $groupBookings->first()->bookingSessions->first();
                $educator = $session?->educator;
                $educatorName = $educator?->name ?? '';
                $tourTime = $session
                    ? Carbon::parse($session->start_time)->format('H:i')
                    : '';

                $isFirst = true;

                foreach ($groupBookings as $booking) {
                    $visitDate = $booking->visit_date;

                    $date = $visitDate ? $visitDate->format('j F Y') : '';
                    $dateNumber = $visitDate ? (int) $visitDate->format('j') : '';
                    $yearWeek = $visitDate ? (int) $visitDate->format('W') : '';
                    $monthNumber = $visitDate ? (int) $visitDate->format('n') : '';
                    $monthName = $visitDate ? self::MONTH_NAMES[$monthNumber] : '';
                    $year = $visitDate ? (int) $visitDate->format('Y') : '';

                    $totalPaxRevenue = $booking->adult_count + $booking->child_count;
                    $visitorType = '';
                    if ($booking->visitor_type === 'WI') {
                        $visitorType = $booking->visitor_type ?? '';
                    } else {
                        $visitorType = 'Reservation' . ' ' . ($booking->visitor_type ?? '');
                    }

                    $rawPhone = trim((string) ($booking->representative_phone ?? ''));
                    $phone = $rawPhone !== ''
                        ? preg_replace('/\D/', '', $rawPhone)
                        : '-';

                    $rows->push([
                        'date' => $date,
                        'date_number' => $dateNumber,
                        'year_week' => $yearWeek,
                        'month_number' => $monthNumber,
                        'month_name' => $monthName,
                        'year' => $year,

                        'educator_handle' => $isFirst ? $educatorName : '',
                        'tour_time' => $isFirst ? $tourTime : '',
                        'name_casual_intern' => $isFirst ? $educatorName : '',

                        'guest_name' => $booking->representative_name ?? '',
                        'geo_origin' => $booking->representative_address ?? '',
                        'contact' => $phone,
                        'dewasa' => $booking->adult_count,
                        'anak' => $booking->child_count,
                        'balita' => $booking->infant_count,
                        'total_pax_revenue' => $totalPaxRevenue,
                        'guest_classification' => $visitorType,
                        'guest_classification_2' => $visitorType,
                        'remarks' => '',
                    ]);

                    $isFirst = false;
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Date Number',
            'Year Week',
            'Month Number',
            'Month Name',
            'Year',
            'Educator Handle',
            'Tour Time',
            'Name Casual / Intern',
            'Guest Name',
            'Detail Geographic Origin',
            'Contact',
            'Dewasa',
            'Anak',
            'Balita',
            'Total Pax',
            'Guest Classification',
            "Guest Classification\n(untuk menghitung pax)",
            'Remarks',
        ];
    }

    public function title(): string
    {
        return 'Sheet1';
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'L' && is_string($value) && $value !== '-') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnWidths(): array
    {
        return array_fill_keys(
            ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'],
            13
        );
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFB0C4DE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(42.75);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                if ($lastRow < 2) {
                    return;
                }

                $sheet->getStyle("A2:S{$lastRow}")->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'] as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->getStyle("M2:M{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('@');

                $sheet->getStyle("L2:L{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('@');

                for ($r = 2; $r <= $lastRow; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(18);
                }

                $sheet->freezePane('A2');
            },
        ];
    }
}