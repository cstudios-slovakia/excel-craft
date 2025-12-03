<?php

namespace Cstudios\ExcelExport\plugin;

/**
 * Class Services
 * @package Cstudios\ExcelExport\plugin
 */
trait Services
{
    /**
     * @return void
     */
    public function _registerComponents(): void
    {
        $this->setComponents([
            // 'api' => ['class' => ApiService::class],
        ]);
    }
}
