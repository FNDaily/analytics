<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace dukt\analytics\widgets;

use Craft;
use craft\helpers\Json;
use dukt\analytics\Plugin as Analytics;
use dukt\analytics\web\assets\realtimereportwidget\RealtimeReportWidgetAsset;

class RealtimeWidget extends \craft\base\Widget
{
    // Properties
    // =========================================================================

    /**
     * Whether users should be able to select more than one of this widget type.
     *
     * @var bool
     */
    protected $multi = false;

    // Public Methods
    // =========================================================================

    public static function isSelectable(): bool
    {
        $plugin = Craft::$app->getPlugins()->getPlugin('analytics');
        $settings = $plugin->getSettings();

        if(empty($settings['enableRealtime']))
        {
            return false;
        }

        return parent::isSelectable();
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('analytics', 'Analytics Real-time');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias('@dukt/analytics/icons/realtime-report.svg');
    }

    /**
     * @inheritDoc IWidget::getBodyHtml()
     *
     * @return string|false
     */
    public function getBodyHtml()
    {
        if(Analytics::$plugin->getAnalytics()->checkPluginRequirements()) {
            if (Craft::$app->getConfig()->get('enableWidgets', 'analytics')) {
                $profileId = Analytics::$plugin->getAnalytics()->getProfileId();

                if ($profileId) {
                    $plugin = Craft::$app->getPlugins()->getPlugin('analytics');
                    $settings = $plugin->getSettings();

                    if (!empty($settings['enableRealtime'])) {
                        $realtimeRefreshInterval = Analytics::$plugin->getAnalytics()->getRealtimeRefreshInterval();

                        $widgetId = $this->id;
                        $widgetOptions = [
                            'refreshInterval' => $realtimeRefreshInterval,
                        ];

                        Craft::$app->getView()->registerAssetBundle(RealtimeReportWidgetAsset::class);

                        Craft::$app->getView()->registerJs('var AnalyticsChartLanguage = "'.Craft::$app->language.'";', true);

                        Craft::$app->getView()->registerJs('new Analytics.Realtime("widget'.$widgetId.'", '.Json::encode($widgetOptions).');');

                        return Craft::$app->getView()->renderTemplate('analytics/_components/widgets/Realtime/body');
                    } else {
                        return Craft::$app->getView()->renderTemplate('analytics/_components/widgets/Realtime/disabled');
                    }
                } else {
                    return Craft::$app->getView()->renderTemplate('analytics/_special/plugin-not-configured');
                }
            } else {
                return Craft::$app->getView()->renderTemplate('analytics/_components/widgets/Realtime/disabled');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function maxColspan()
    {
        return 1;
    }
}
