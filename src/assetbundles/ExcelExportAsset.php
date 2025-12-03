<?php

namespace Cstudios\ExcelExport\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Class ExcelExportAsset
 *
 * @package Cstudios\ExcelExport\assetbundles
 */
class ExcelExportAsset extends AssetBundle
{
    /**
     * @return void
     */
    public function init(): void
    {
        $this->sourcePath = '@Cstudios/ExcelExport/web/assets/excelexport';
        $this->depends = [
            CpAsset::class,
        ];
        $this->js = [
            'exporter-format.js',
        ];

        parent::init();
    }
}
