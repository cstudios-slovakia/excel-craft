<?php

namespace Cstudios\ExcelExport\controllers;

use Craft;
use craft\errors\InvalidPluginException;
use craft\web\Controller;
use yii\web\Response;
use Cstudios\ExcelExport\Plugin;

/**
 * Class SettingsController
 * @package Cstudios\ExcelExport\controllers
 */
class SettingsController extends Controller
{
    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        /** @var \Cstudios\ExcelExport\Plugin $plugin */
        $plugin = Plugin::$plugin;
        $settings = $plugin->getSettings();

        return $this->renderTemplate('excelexport/settings/index', [
            'settings' => $settings ?? null
        ]);
    }

    /**
     * @return Response|null
     * @throws InvalidPluginException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveSettings(): ?Response
    {
        $this->requirePostRequest();
        $settings = Craft::$app->getRequest()->getBodyParam('settings');
        $plugin = Craft::$app->getPlugins()->getPlugin('excelexport');

        if (!$plugin) {
            throw new InvalidPluginException('excelexport');
        }

        if (Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            Craft::$app->getSession()->setNotice(Craft::t('excelexport', 'Plugin settings saved.'));

            // Redirect
            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setError(Craft::t('excelexport', 'Couldn’t save plugin settings.'));

        // Send the plugin back to the template
        Craft::$app->getUrlManager()->setRouteParams([
            'plugin' => $plugin
        ]);

        return null;
    }
}