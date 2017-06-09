<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace dukt\analytics\fields;

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;
use craft\helpers\Json;
use dukt\analytics\web\assets\reportfield\ReportFieldAsset;
use dukt\analytics\Plugin as Analytics;

class Report extends Field
{
    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('analytics', 'Analytics Report');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $name = $this->handle;

        if (Analytics::$plugin->getAnalytics()->checkPluginRequirements()) {
            if (Analytics::$plugin->getSettings()->enableFieldtype) {
                // Reformat the input name into something that looks more like an ID
                $id = Craft::$app->getView()->formatInputId($name);

                // Figure out what that ID is going to look like once it has been namespaced
                $namespacedId = Craft::$app->getView()->namespaceInputId($id);

                // Default variables
                $variables = [
                    'hasUrl' => false,
                    'isNew' => false,
                ];

                if ($element) {
                    if ($element->uri) {
                        $uri = Analytics::$plugin->getAnalytics()->getElementUrlPath($element->id, $element->siteId);

                        $startDate = date('Y-m-d', strtotime('-1 month'));
                        $endDate = date('Y-m-d');
                        $metrics = 'ga:pageviews';
                        $dimensions = 'ga:date';
                        $filters = "ga:pagePath==".$uri;

                        $request = [
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                            'metrics' => $metrics,
                            'dimensions' => $dimensions,
                            'filters' => $filters
                        ];


                        // JS Options

                        $jsOptions = [
                            'chartLanguage' => Analytics::$plugin->getAnalytics()->getChartLanguage(),
                        ];


                        // Add locale definition to JS options

                        $siteView = Analytics::$plugin->getViews()->getSiteViewBySiteId($element->siteId);

                        if ($siteView) {
                            $reportingView = $siteView->getView();

                            if ($reportingView) {
                                $gaReportingViewResponse = Analytics::$plugin->getApis()->getAnalytics()->getService()->management_profiles->get($reportingView->gaAccountId, $reportingView->gaPropertyId, $reportingView->gaViewId);
                                $gaReportingView = $gaReportingViewResponse->toSimpleObject();

                                $jsOptions['localeDefinition'] = Analytics::$plugin->getAnalytics()->getD3LocaleDefinition(['currency' => $gaReportingView->currency]);
                            }
                        }


                        // Add cached response to JS options if any

                        $cacheId = ['reports.getElementReport', $request];
                        $response = Analytics::$plugin->cache->get($cacheId);

                        if ($response) {
                            $response = [
                                'type' => 'area',
                                'chart' => $response
                            ];

                            $jsOptions['cachedResponse'] = $response;
                        }


                        // Register JS & Styles

                        Craft::$app->getView()->registerAssetBundle(ReportFieldAsset::class);
                        Craft::$app->getView()->registerJs('new AnalyticsReportField("'.$namespacedId.'-field", '.json_encode($jsOptions).');');


                        // Variables

                        $variables = [
                            'isNew' => false,
                            'hasUrl' => true,
                            'id' => $id,
                            'uri' => $uri,
                            'name' => $name,
                            'value' => $value,
                            'model' => $this,
                            'element' => $element
                        ];
                    } elseif (!$element->id) {
                        $variables = [
                            'hasUrl' => false,
                            'isNew' => true,
                        ];
                    }
                }

                return Craft::$app->getView()->renderTemplate('analytics/_components/fieldtypes/Report/input', $variables);
            }

            return Craft::$app->getView()->renderTemplate('analytics/_components/fieldtypes/Report/disabled');
        }

        return Craft::$app->getView()->renderTemplate('analytics/_special/plugin-not-configured');
    }
}
