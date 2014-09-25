<?php

/**
 * Craft Analytics by Dukt
 *
 * @package   Craft Analytics
 * @author    Benjamin David
 * @copyright Copyright (c) 2014, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 * @link      https://dukt.net/craft/analytics/
 */

namespace Craft;

class Analytics_ExplorerWidget extends BaseWidget
{
    public function getName()
    {
        return Craft::t('Analytics Explorer');
    }

    protected function defineSettings()
    {
        return array(
           'menu' => array(AttributeType::String),
           'dimension' => array(AttributeType::String),
           'metric' => array(AttributeType::String),
           'chart' => array(AttributeType::String),
           'chart' => array(AttributeType::String),
           'period' => array(AttributeType::String),
           'pinned' => array(AttributeType::Bool)
        );
    }

    public function getBodyHtml()
    {
        $plugin = craft()->plugins->getPlugin('analytics');

        // settings
        $pluginSettings = $plugin->getSettings();

        // widget
        $widget = $this->model;

        // browser sections
        $browserSectionsJson = file_get_contents(CRAFT_PLUGINS_PATH.'analytics/data/browser.json');
        $browserSections = json_decode($browserSectionsJson, true);

        // browser data
        $browserDataJson = file_get_contents(CRAFT_PLUGINS_PATH.'analytics/data/browserData.json');

        // browserSelect

        $browserSelectJson = file_get_contents(CRAFT_PLUGINS_PATH.'analytics/data/browserSelect.json');
        $browserSelect = json_decode($browserSelectJson, true);

        // js
        craft()->templates->includeJs('var AnalyticsBrowserSections = '.$browserSectionsJson.';');
        craft()->templates->includeJs('var AnalyticsBrowserData = '.$browserDataJson.';');
        craft()->templates->includeJs('new AnalyticsExplorer("widget'.$widget->id.'", '.json_encode($widget->settings).');');

        // render
        $variables['browserSections'] = $browserSections;
        $variables['browserSelect'] = $browserSelect;
        $variables['widget'] = $widget;
        $variables['pluginSettings'] = $pluginSettings;

        return craft()->templates->render('analytics/widgets/explorer', $variables);
    }

    public function getColspan()
    {
        return 2;
    }
}