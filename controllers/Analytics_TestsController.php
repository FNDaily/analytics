<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2015, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace Craft;

class Analytics_TestsController extends BaseController
{
	// Public Methods
	// =========================================================================

	/**
	 * Account Explorer
	 *
	 * @return null
	 */
	public function actionAccountExplorer(array $variables = array())
	{
		$variables['accounts'] = craft()->analytics_api->getManagementAccounts()->listManagementAccounts();
		$variables['webProperties'] = craft()->analytics_api->getWebProperties();
		$variables['profiles'] = craft()->analytics_api->listProfiles();

		$this->renderTemplate('analytics/tests/_accountExplorer', $variables);
	}

	/**
	 * Data Types
	 *
	 * @return null
	 */
	public function actionDataTypes(array $variables = array())
	{
		$variables['googleAnalyticsDataTypes'] = craft()->analytics_metadata->getGoogleAnalyticsDataTypes();
		$variables['dataTypes'] = craft()->analytics_metadata->getDataTypes();

		$this->renderTemplate('analytics/tests/_dataTypes', $variables);
	}

	/**
	 * Charts
	 *
	 * @return null
	 */
	public function actionReportWidgets(array $variables = array())
	{
		craft()->templates->includeJsResource('analytics/js/jsapi.js', true);

		craft()->templates->includeJsResource('analytics/js/ReportWidget.js');
		craft()->templates->includeCssResource('analytics/css/ReportWidget.css');
		craft()->templates->includeCssResource('analytics/css/tests.css');

		$this->renderTemplate('analytics/tests/_reportWidgets', $variables);
	}

	/**
	 * Tests
	 *
	 * @return null
	 */
	public function actionFormatting(array $variables = array())
	{
		$this->renderTemplate('analytics/tests/_formatting', $variables);
	}

	/**
	 * Columns
	 *
	 * @return null
	 */
	public function actionColumns(array $variables = array())
	{
		$variables['columns'] = craft()->analytics_metadata->getColumns();

		$this->renderTemplate('analytics/tests/_columns', $variables);
	}

	/**
	 * Groups
	 *
	 * @return null
	 */
	public function actionColumnGroups(array $variables = array())
	{
		$variables['columnGroups'] = craft()->analytics_metadata->getColumnGroups();

		$this->renderTemplate('analytics/tests/_columnGroups', $variables);
	}
}