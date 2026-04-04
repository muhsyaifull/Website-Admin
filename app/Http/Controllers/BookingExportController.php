<?php

namespace App\Http\Controllers;

use App\Exports\BookingExport;
use App\Models\Tour;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class BookingExportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:pending,confirmed,completed,cancelled'],
            'tour_id' => ['nullable', 'exists:tours,id'],
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tourId = $request->input('tour_id');

        $tourName = null;
        if ($tourId) {
            $tourName = Tour::find($tourId)?->name;
        }

        $filename = $this->buildFilename($startDate, $endDate, $tourName);

        return Excel::download(
            new BookingExport($startDate, $endDate, $request->input('status'), $tourId),
            $filename
        );
    }

    protected function buildFilename(?string $startDate, ?string $endDate, ?string $tourName): string
    {
        $parts = ['reservation'];

        if ($tourName) {
            $parts[] = strtolower(str_replace(' ', '-', $tourName));
        } else {
            $parts[] = 'all-tour';
        }

        $parts[] = $startDate
            ? Carbon::parse($startDate)->format('dmY')
            : 'start';

        $parts[] = $endDate
            ? Carbon::parse($endDate)->format('dmY')
            : 'end';
        return implode('-', $parts) . '.xlsx';
    }
}