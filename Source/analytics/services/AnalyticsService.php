<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2015, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace Craft;

class AnalyticsService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    private $tracking;

    public function getRealtimeRefreshInterval()
    {
        $interval = craft()->config->get('realtimeRefreshInterval', 'analytics');

        if(!$interval)
        {
            $plugin = craft()->plugins->getPlugin('analytics');
            $settings = $plugin->getSettings();

            if(!empty($settings['realtimeRefreshInterval']))
            {
                $interval = $settings['realtimeRefreshInterval'];
            }
        }

        if(empty($interval))
        {
            $interval = 60;
        }

        return $interval;
    }

    /**
     * Get data source from its class name
     */
    public function getDataSource($className = 'GoogleAnalytics')
    {
        $nsClassName = "\\Dukt\\Analytics\\DataSources\\$className";
        return new $nsClassName;
    }

    /**
     * Get Profile ID
     */
    public function getProfileId()
    {
        $plugin = craft()->plugins->getPlugin('analytics');
        $settings = $plugin->getSettings();

        if(!empty($settings['profileId']))
        {
            return 'ga:'.$settings['profileId'];
        }

    }

    /**
     * Track
     */
    public function track($options)
    {
        if(!$this->tracking)
        {
            $this->tracking = new AnalyticsTracking($options);
        }

        return $this->tracking;
    }

    /**
     * Send Request
     */
    public function sendRequest(Analytics_RequestCriteriaModel $criteria)
    {
        $criteria->ids = craft()->analytics->getProfileId();

        if($criteria->realtime)
        {
            $response = craft()->analytics_api->apiGetGADataRealtime(
                $criteria->ids,
                $criteria->metrics,
                $criteria->optParams
            );
        }
        else
        {
            $response = craft()->analytics_api->apiGetGAData(
                $criteria->ids,
                $criteria->startDate,
                $criteria->endDate,
                $criteria->metrics,
                $criteria->optParams,
                $criteria->enableCache
            );
        }

        if($criteria->format == 'gaData')
        {
            return $response;
        }
        else
        {
            return AnalyticsHelper::gaDataToArray($response);
        }
    }

    /**
     * Get Element URL Path
     *
     * @param int           $elementId
     * @param string|null   $localeId
     */
    public function getElementUrlPath($elementId, $localeId)
    {
        $element = craft()->elements->getElementById($elementId, null, $localeId);

        $uri = $element->uri;
        $url = $element->url;

        $components = parse_url($url);

        if($components['path'])
        {
            $uri = $components['path'];
        }

        return $uri;
    }

    // Private Methods
    // =========================================================================

    /**
     * Get Data
     *
     * @param string $name
     */
    private function getData($name)
    {
        $jsonData = file_get_contents(CRAFT_PLUGINS_PATH.'analytics/data/'.$name.'.json');
        $data = json_decode($jsonData, true);

        return $data;
    }
}
