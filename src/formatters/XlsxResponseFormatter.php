<?php

namespace Cstudios\ExcelExport\formatters;

use craft\helpers\Json;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;

/**
 * Class XlsxResponseFormatter
 *
 * @package Cstudios\ExcelExport\formatters
 */
class XlsxResponseFormatter extends Component implements ResponseFormatterInterface
{
    /**
     * @var string the Content-Type header for the response
     */
    public string $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * @var bool whether the response data should include a header row
     */
    public bool $includeHeaderRow = true;

    /**
     * @var string|null default filename to use when none is provided
     */
    public ?string $defaultFileName = 'export.xlsx';

    /**
     * Formats the specified response.
     */
    public function format($response): void
    {
        if (stripos($this->contentType, 'charset') === false) {
            $this->contentType .= '; charset=' . $response->charset;
        }
        $response->getHeaders()->set('Content-Type', $this->contentType);

        if ($this->defaultFileName && !$response->getHeaders()->get('Content-Disposition')) {
            $response->getHeaders()->set('Content-Disposition', 'attachment; filename="' . $this->defaultFileName . '"');
        }

        $data = is_iterable($response->data) ? $response->data : [];
        if (empty($data)) {
            $response->content = '';
            return;
        }

        $headers = $this->extractHeaders($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $rowIndex = 1;
        if ($this->includeHeaderRow) {
            $sheet->fromArray($headers, null, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach ($data as $row) {
            $normalizedRow = [];
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                if (!is_scalar($value)) {
                    $value = Json::encode($value);
                }
                $normalizedRow[] = $value;
            }
            $sheet->fromArray($normalizedRow, null, 'A' . $rowIndex);
            $rowIndex++;
        }

        // auto-size columns
        for ($col = 1; $col <= count($headers); $col++) {
            $column = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $response->content = (string)ob_get_clean();

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @param iterable $data
     * @return array<string>
     */
    private function extractHeaders(iterable $data): array
    {
        $headers = [];
        foreach ($data as $row) {
            foreach (array_keys($row) as $key) {
                $headers[$key] = true;
            }
        }

        return array_keys($headers);
    }
}
