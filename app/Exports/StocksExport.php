<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class StocksExport implements
  FromCollection,
  WithMapping,
  WithHeadings,
  WithStyles,
  WithColumnFormatting,
  WithEvents
{
  private $request;
  private $result;
  // accept the request
  public function __construct(Request $request)
  {
    $this->request = $request;
  }
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {

    $this->request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'product_type' => 'in:chicken,pork,beef'
    ]);


    // get user branch
    $branch_id = auth()->user()->branch_id;

    $stocks = DB::table('stocks')
      ->join('products', 'stocks.product_id', '=', 'products.id')
      ->leftJoin('forecast_settings', 'stocks.id', '=', 'forecast_settings.stock_id')
      ->where('stocks.branch_id', $branch_id)
      ->when($this->request->has('product_type'), function ($query) {
        return $query->where('products.type', $this->request->product_type);
      })
      ->select(
        'stocks.quantity as quantity',
        'stocks.id as stock_id',
        'forecast_settings.reordering_point as users_rop',
        'forecast_settings.default_kg_per_day as users_kg_per_day',
        'products.*'
      );

    if ($this->request->has('search')) {
      $stocks->where('name', 'like', '%' . $this->request->search . '%');
    }

    if ($this->request->has(['field', 'direction'])) {
      $stocks->orderBy($this->request->field, $this->request->direction);
    }

    $this->result = $stocks->get();
    return $stocks->get();
  }

  public function map($stocks): array
  {
    return [
      'product id' => $stocks->id,
      'product name' => $stocks->name,
      'product type' => $stocks->type,
      'product price' => $stocks->price,
      'product quantity' => $stocks->quantity,
      'product reordering point' => $stocks->users_rop ? $stocks->users_rop : $stocks->reordering_point,
      'product default kg per day' => $stocks->users_kg_per_day ? $stocks->users_kg_per_day : $stocks->default_kg_per_day,
    ];
  }

  public function headings(): array
  {
    return [
      'ID',
      'PRODUCT NAME',
      'TYPE',
      'PRICE',
      'QUANTITY(KG)',
      'ROP(KG)',
      'KG/DAY',
    ];
  }

  public function styles(Worksheet $sheet)
  {
    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(10);
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
  public function columnFormats(): array
  {
    return [
      'A' => NumberFormat::FORMAT_TEXT,
      'D' => NumberFormat::FORMAT_NUMBER_00,
    ];
  }
  public function registerEvents(): array
  {
    return [
      // Handle by a closure.
      AfterSheet::class => function (AfterSheet $event) {

        // last column as letter value (e.g., D)
        $last_column = Coordinate::stringFromColumnIndex(count($this->headings()));

        // calculate last row + 1 (total results + header rows + column headings row + new row)
        $last_row = count($this->result) + 2 + 1 + 1;

        // set up a style array for cell formatting
        $style_text_center = [
          'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
          ],
          'font' => [
            'bold' => true,
            'size' => 11,
            'color' => [
              'rgb' => 'FFFFFF'
            ],
            'name' => 'Helvetica',
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => [
              'rgb' => 'F05252',
            ],
          ],
          // add padding


        ];

        // at row 1, insert 2 rows
        $event->sheet->insertNewRowBefore(1, 2);

        // merge cells for full-width
        $event->sheet->mergeCells(sprintf('A1:%s1', $last_column));
        $event->sheet->mergeCells(sprintf('A2:%s2', $last_column));
        $event->sheet->mergeCells(sprintf('A%d:%s%d', $last_row, $last_column, $last_row));

        // assign cell values
        $event->sheet->setCellValue('A1', 'IMPARK');
        $event->sheet->setCellValue('A2', Auth::user()->branch->name . ' STOCKS REPORT ' . now());
        $event->sheet->setCellValue(sprintf('A%d', $last_row), Auth::user()->branch->name . ' STOCKS REPORT ' . now());

        // assign cell styles
        $event->sheet->getStyle('A1:A2')->applyFromArray($style_text_center);
        $event->sheet->getStyle(sprintf('A%d', $last_row))->applyFromArray($style_text_center);
        $event->sheet->getDefaultRowDimension()->setRowHeight(20, 'pt');
      },
    ];
  }
}
