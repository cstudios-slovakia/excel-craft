<?php

namespace Cstudios\ExcelExport;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\helpers\UrlHelper;
use Cstudios\ExcelExport\models\Settings;
use Cstudios\ExcelExport\assetbundles\ExcelExportAsset;
use Cstudios\ExcelExport\formatters\XlsxResponseFormatter;
use Cstudios\ExcelExport\plugin\Routes;
use Cstudios\ExcelExport\plugin\Services;

/**
 * Class Plugin
 * @package Cstudios\ExcelExport
 */
class Plugin extends BasePlugin
{
    use Routes;
    use Services;

    /** @var string */
    const PLUGIN_HANDLE = 'excelexport';

    /**
     * @var bool
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public bool $hasCpSection = false;

    /**
     * @inheritDoc
     */
    public string $schemaVersion = '1.0.0';

    /**
     * @inheritdoc
     */
    public string $minVersionRequired = '4.0.0';

    /**
     * @var \Cstudios\ExcelExport\Plugin
     */
    public static $plugin;

    /**
     * @inheritdoc
     */
    public static function config(): array
    {
        return [
            'components' => []
        ];
    }

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        Craft::$app->onInit(function() {
            $request = Craft::$app->getRequest();
            $settings = $this->getSettings();

            $this->_registerComponents();

            if ($request->getIsCpRequest()) {
                $this->_registerCpRoutes();
            }

            if (!$settings->enabled) {
                return;
            }

            $this->registerXlsxFormatter($settings);

            if ($request->getIsCpRequest()) {
                Craft::$app->getView()->registerAssetBundle(ExcelExportAsset::class);
            } elseif (!$request->getIsConsoleRequest()) {
                $this->_registerSiteRoutes();
            }
        });
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('excelexport/settings'));
    }

    /**
     * @param Settings $settings
     * @return void
     */
    private function registerXlsxFormatter(Settings $settings): void
    {
        $response = Craft::$app->getResponse();
        if (!isset($response->formatters['xlsx'])) {
            $response->formatters['xlsx'] = Craft::createObject([
                'class' => XlsxResponseFormatter::class,
                'includeHeaderRow' => $settings->includeHeaderRow,
                'contentType' => $settings->contentType,
                'defaultFileName' => $settings->defaultFileName,
            ]);
        }
    }
}