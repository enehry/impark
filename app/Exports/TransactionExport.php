<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements
  FromCollection,
  WithHeadings,
  WithMapping,
  WithStyles,
  WithEvents

{

  private $data;
  private $branch;
  private $start_date;
  private $end_date;

  public function __construct($query, $branch, $start_date, $end_date)
  {
    $this->data = $query;
    $this->branch = $branch;
    $this->start_date = $start_date;
    $this->end_date = $end_date;
  }
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    //
    return $this->data;
  }

  public function map($transaction): array
  {
    return [
      $transaction->id,
      $transaction->branch_name,
      $transaction->name,
      $transaction->type,
      $transaction->quantity,
      $transaction->formatted_delivered_at ?? 'Not Delivered',
      $transaction->formatted_received_at ?? 'Not Received',
    ];
  }

  public function headings(): array
  {
    return [
      'ID',
      'BRANCH NAME',
      'PRODUCT NAME',
      'TYPE',
      'QUANTITY(KG)',
      'DATE DELIVERED',
      'DATE RECEIVED'
    ];
  }



  public function styles(Worksheet $sheet)
  {
    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->getStyle('B')->getAlignment()->setWrapText(true);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getStyle('C')->getAlignment()->setWrapText(true);
    // wrap text column D
    $sheet->getColumnDimension('D')->setWidth(10);
    $sheet->getStyle('D')->getAlignment()->setWrapText(true);
    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->getColumnDimension('F')->setWidth(18);
    $sheet->getStyle('F')->getAlignment()->setWrapText(true);
    $sheet->getColumnDimension('G')->setWidth(18);
    $sheet->getStyle('G')->getAlignment()->setWrapText(true);
    // format e to currency
    $sheet->getStyle('A')
      ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E')
      ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($sheet->calculateWorksheetDimension())
      ->applyFromArray([
        'borders' => [
          'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
          ],
        ],
        'font' => [
          // set font name
          'name' => 'Helvetica',
          //color white


        ],
      ]);

    $sheet->getStyle('A1:G1')->applyFromArray([
      'font' => [
        'bold' => true,
        'size' => 11,
        'color' => [
          'rgb' => 'FFFFFF'
        ]
      ],
      'alignment' => [
        'horizontal' => 'center',
        'vertical' => 'center',
      ],
      'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
          'rgb' => 'F05252',
        ],
      ],
    ]);
  }
  public function registerEvents(): array
  {
    return [
      // Handle by a closure.
      AfterSheet::class => function (AfterSheet $event) {

        // last column as letter value (e.g., D)
        $last_column = Coordinate::stringFromColumnIndex(count($this->headings()));

        // calculate last row + 1 (total results + header rows + column headings row + new row)
        $last_row = count($this->data) + 2 + 1 + 1;

        // set up a style array for cell formatting
        $style_text_center = [
          'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
          ],
          'font' => [
            'bold' => true,
            'size' => 11,
            'name' => 'Helvetica',
            'color' => [
              'rgb' => 'FFFFFF'
            ]
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => [
              'rgb' => 'F05252',
            ],
          ],


        ];

        // at row 1, insert 2 rows
        $event->sheet->insertNewRowBefore(1, 2);

        // merge cells for full-width
        $event->sheet->mergeCells(sprintf('A1:%s1', $last_column));
        $event->sheet->mergeCells(sprintf('A2:%s2', $last_column));
        $event->sheet->mergeCells(sprintf('A%d:%s%d', $last_row, $last_column, $last_row));

        $branch = $this->branch ? strtoupper($this->branch)  : 'ALL';

        // start date to end date
        $header = $this->start_date ? ' TRANSACTION REPORT OF ' . $branch . ' BRANCH FROM ' . $this->start_date . ' TO ' . $this->end_date :
          ' TRANSACTION REPORT ' . $branch . ' BRANCH';


        // assign cell values
        $event->sheet->setCellValue('A1', 'IMPARK');
        $event->sheet->setCellValue('A2', $header);
        $event->sheet->setCellValue(sprintf('A%d', $last_row), 'DATE GENERATED : ' . now()->format('F j, Y h:i A'));

        // assign cell styles
        $event->sheet->getStyle('A1:A2')->applyFromArray($style_text_center);
        $event->sheet->getStyle(sprintf('A%d', $last_row))->applyFromArray($style_text_center);
        $event->sheet->getDefaultRowDimension()->setRowHeight(20, 'pt');
      },
    ];
  }
}
