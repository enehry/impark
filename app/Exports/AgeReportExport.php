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

class AgeReportExport implements
  FromCollection,
  WithHeadings,
  WithMapping,
  WithStyles,
  WithEvents

{

  private $data;
  private $branch;

  public function __construct($query, $branch)
  {
    $this->data = $query;
    $this->branch = $branch;
  }
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    //
    return $this->data;
  }

  public function map($stocks): array
  {
    return [
      $stocks->id,
      $stocks->branch_name,
      $stocks->name,
      $stocks->age,
      $stocks->quantity,
      $stocks->maximum_shelf_life,
      $stocks->status,
      $stocks->formatted_date,
    ];
  }

  public function headings(): array
  {
    return [
      'ID',
      'BRANCH',
      'PRODUCT NAME',
      'AGE OF MEAT IN THE INVENTORY',
      'QUANTITY OF MEAT IN THE INVENTORY',
      'MAXIMUM SHELF LIFE',
      'STATUS',
      'DATE RECEIVED',
    ];
  }



  public function styles(Worksheet $sheet)
  {
    $sheet->getColumnDimension('B')->setWidth(12);
    // text wrap all rows
    $sheet->getStyle('A1:H1')->getAlignment()->setWrapText(true);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(10);
    $sheet->getColumnDimension('E')->setWidth(10);
    $sheet->getColumnDimension('G')->setWidth(10);
    $sheet->getStyle('G')->getAlignment()->setWrapText(true);
    $sheet->getColumnDimension('H')->setWidth(20);
    $sheet->getStyle('A')
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

    $sheet->getStyle('A1:H1')->applyFromArray([
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

        $branch = $this->branch ? strtoupper($this->branch) : 'ALL';
        // assign cell values
        $event->sheet->setCellValue('A1', 'IMPARK');
        $event->sheet->setCellValue('A2', $branch . ' AGE REPORT ' .  now());
        $event->sheet->setCellValue(sprintf('A%d', $last_row), $branch . ' AGE REPORT ' . now());

        // assign cell styles
        $event->sheet->getStyle('A1:A2')->applyFromArray($style_text_center);
        $event->sheet->getStyle(sprintf('A%d', $last_row))->applyFromArray($style_text_center);
        $event->sheet->getDefaultRowDimension()->setRowHeight(20, 'pt');

        // color the row that are age > 3
        for ($i = 0; $i < count($this->data); $i++) {
          if ($this->data[$i]->age >= $this->data[$i]['maximum_shelf_life']) {
            $event->sheet->getStyle(sprintf('A%d:H%d', $i + 4, $i + 4))->applyFromArray([
              'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                  'rgb' => 'FFC3C3',
                ],
              ],
              // set font to dark red
              'font' => [
                'color' => [
                  'rgb' => '990000'
                ]
              ]

            ]);
          }
        }
      },
    ];
  }
}
