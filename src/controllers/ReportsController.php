<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace dukt\analytics\controllers;

use Craft;
use craft\web\Controller;
use dukt\analytics\Plugin as Analytics;

class ReportsController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Get element report.
     *
     * @return \yii\web\Response
     */
    public function actionElement()
    {
        try {
            $elementId = Craft::$app->getRequest()->getRequiredParam('elementId');
            $locale = Craft::$app->getRequest()->getRequiredParam('locale');
            $metric = Craft::$app->getRequest()->getRequiredParam('metric');

            $uri = Analytics::$plugin->getAnalytics()->getElementUrlPath($elementId, $locale);

            if ($uri) {
                if ($uri == '__home__') {
                    $uri = '';
                }

                $start = date('Y-m-d', strtotime('-1 month'));
                $end = date('Y-m-d');
                $dimensions = 'ga:date';

                $optParams = [
                    'dimensions' => $dimensions,
                    'filters' => "ga:pagePath==".$uri
                ];

                $request = [
                    'startDate' => $start,
                    'endDate' => $end,
                    'metrics' => $metric,
                    'optParams' => $optParams,
                ];

                $cacheId = ['ReportsController.actionGetElement', $request];
                $response = Analytics::$plugin->cache->get($cacheId);

                if (!$response) {
                    $viewId = Analytics::$plugin->getAnalytics()->getProfileId();

                    $ids = $viewId;
                    $startDate = $request['startDate'];
                    $endDate = $request['endDate'];
                    $metrics = $request['metrics'];
                    $optParams = $request['optParams'];

                    if(!$optParams)
                    {
                        $optParams = [];
                    }

                    $dataGaResponse = Analytics::$plugin->getAnalyticsApi()->googleAnalytics()->data_ga->get($ids, $startDate, $endDate, $metrics, $optParams);

                    $response = Analytics::$plugin->getAnalyticsApi()->parseReportResponse($dataGaResponse);

                    if ($response) {
                        Analytics::$plugin->cache->set($cacheId, $response);
                    }
                }

                return $this->asJson([
                    'type' => 'area',
                    'chart' => $response
                ]);
            } else {
                throw new \Exception("Element doesn't support URLs.", 1);
            }
        } catch (\Google_Service_Exception $e) {
            $errors = $e->getErrors();
            $errorMsg = $e->getMessage();

            if (isset($errors[0]['message'])) {
                $errorMsg = $errors[0]['message'];
            }

            Craft::info('Couldn’t get element data: '.$errorMsg."\r\n".print_r($errors, true), __METHOD__);

            return $this->asErrorJson($errorMsg);
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            Craft::info('Couldn’t get element data: '.$errorMsg, __METHOD__);

            return $this->asErrorJson($errorMsg);
        }
    }

    /**
     * Get realtime widget report.
     *
     * @return \yii\web\Response
     */
    public function actionRealtimeWidget()
    {
        $newVisitor = 0;
        $returningVisitor = 0;
        $total = 0;

        if (!Analytics::$plugin->getSettings()->demoMode) {
            try {
                $request = [
                    'metrics' => 'ga:activeVisitors',
                    'optParams' => ['dimensions' => 'ga:visitorType']
                ];

                $response = Analytics::$plugin->getReports()->getRealtimeReport($request);


                // total

                if (!empty($response['totalResults'])) {
                    $total = $response['totalResults'];
                }


                // new & returning visitors

                if (!empty($response['rows'])) {
                    $rows = $response['rows'];

                    if (!empty($rows[0][1])) {
                        switch ($rows[0][0]) {
                            case "RETURNING":
                                $returningVisitor = $rows[0][1];
                                break;

                            case "NEW":
                                $newVisitor = $rows[0][1];
                                break;
                        }
                    }

                    if (!empty($rows[1][1])) {
                        switch ($rows[1][0]) {
                            case "RETURNING":
                                $returningVisitor = $rows[1][1];
                                break;

                            case "NEW":
                                $newVisitor = $rows[1][1];
                                break;
                        }
                    }
                }
            } catch (\Google_Service_Exception $e) {
                $errors = $e->getErrors();
                $errorMsg = $e->getMessage();

                if (isset($errors[0]['message'])) {
                    $errorMsg = $errors[0]['message'];
                }

                Craft::info('Couldn’t get realtime widget data: '.$errorMsg."\r\n".print_r($errors, true), __METHOD__);

                return $this->asErrorJson($errorMsg);
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                Craft::info('Couldn’t get element data: '.$errorMsg, __METHOD__);

                return $this->asErrorJson($errorMsg);
            }
        } else {
            // Demo Mode
            $newVisitor = 5;
            $returningVisitor = 7;
            $total = ($newVisitor + $returningVisitor);
        }

        return $this->asJson([
            'total' => $total,
            'newVisitor' => $newVisitor,
            'returningVisitor' => $returningVisitor
        ]);
    }

    /**
     * Get report widget report.
     *
     * @return \yii\web\Response
     */
    public function actionReportWidget()
    {
        try {
            $profileId = Analytics::$plugin->getAnalytics()->getProfileId();

            $chart = Craft::$app->getRequest()->getBodyParam('chart');
            $period = Craft::$app->getRequest()->getBodyParam('period');
            $options = Craft::$app->getRequest()->getBodyParam('options');

            $request = [
                'chart' => $chart,
                'period' => $period,
                'options' => $options,
            ];

            $cacheId = ['getReport', $request, $profileId];

            $response = Analytics::$plugin->cache->get($cacheId);

            if (!$response) {
                switch($chart) {
                    case 'area':
                        $response = Analytics::$plugin->getReports()->getAreaReport($request);
                        break;
                    case 'counter':
                        $response = Analytics::$plugin->getReports()->getCounterReport($request);
                        break;
                    case 'pie':
                        $response = Analytics::$plugin->getReports()->getPieReport($request);
                        break;
                    case 'table':
                        $response = Analytics::$plugin->getReports()->getTableReport($request);
                        break;
                    case 'geo':
                        $response = Analytics::$plugin->getReports()->getGeoReport($request);
                        break;
                    default:
                        throw new \Exception("Chart type `".$chart."` not supported.");
                }

                if ($response) {
                    Analytics::$plugin->cache->set($cacheId, $response);
                }
            }

            return $this->asJson($response);
        } catch (\Google_Service_Exception $e) {
            $errors = $e->getErrors();
            $errorMsg = $e->getMessage();

            if (isset($errors[0]['message'])) {
                $errorMsg = $errors[0]['message'];
            }

            Craft::info('Couldn’t get report widget data: '.$errorMsg."\r\n".print_r($errors, true), __METHOD__);

            return $this->asErrorJson($errorMsg);
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            Craft::info('Couldn’t get element data: '.$errorMsg, __METHOD__);

            return $this->asErrorJson($errorMsg);
        }
    }
}
