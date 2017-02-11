<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2016, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace dukt\analytics\variables;

use dukt\analytics\models\RequestCriteria;
use dukt\etc\craft\AnalyticsTracking;
use dukt\analytics\Plugin as Analytics;

class AnalyticsVariable
{
	// Public Methods
	// =========================================================================

	/**
	 * Returns a Analytics_RequestCriteriaModel model that can be sent to request Google Analytics' API.
	 *
	 * @param array $attributes
	 *
	 * @return RequestCriteria
	 */
	public function api($attributes = null)
	{
		return new RequestCriteria($attributes);
	}

	/**
	 * Sends tracking data to Google Analytics.
	 *
	 * @param array $options
	 *
	 * @return AnalyticsTracking|null
	 */
	public function track($options = null)
	{
		return Analytics::$plugin->analytics->track($options);
	}

	public function getProfileId()
	{
		return Analytics::$plugin->analytics->getProfileId();
	}
}
