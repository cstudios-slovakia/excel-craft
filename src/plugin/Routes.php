<?php

namespace Cstudios\ExcelExport\plugin;

use Craft;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;
use craft\services\UserPermissions;
use craft\events\RegisterUserPermissionsEvent;

/**
 * Class Routes
 * @package Cstudios\ExcelExport\plugin
 */
trait Routes
{
    /**
     * @return void
     */
    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['excelexport/settings'] = 'excelexport/settings';
        });
    }

    /**
     * Registers the Site routes.
     *
     * @since 3.0
     */
    private function _registerSiteRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function(RegisterUrlRulesEvent $event) {
            // Register site routes when needed
        });
    }
}