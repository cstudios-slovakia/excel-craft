<?php

namespace Cstudios\ExcelExport\models;

use craft\base\Model;

/**
 * Class Settings
 * @package Cstudios\ExcelExport\models
 */
class Settings extends Model
{
    /**
     * @var bool
     */
    public bool $enabled = true;

    /**
     * @var bool
     */
    public bool $includeHeaderRow = true;

    /**
     * @var string
     */
    public string $defaultFileName = 'export.xlsx';

    /**
     * @var string
     */
    public string $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['enabled', 'includeHeaderRow'], 'boolean'],
            [['defaultFileName', 'contentType'], 'required'],
            [['defaultFileName', 'contentType'], 'string'],
            ['defaultFileName', 'match', 'pattern' => '/\.xlsx$/i', 'message' => 'The default file name should end with .xlsx'],
        ];
    }
}
