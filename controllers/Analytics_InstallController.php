<?php
/**
 * @link      https://dukt.net/analytics/
 * @copyright Copyright (c) 2018, Dukt
 * @license   https://dukt.net/analytics/docs/license
 */

namespace Craft;

class Analytics_InstallController extends BaseController
{
	// Public Methods
	// =========================================================================

	/**
	 * Install Index
	 *
	 * @return null
	 */
	public function actionIndex()
	{
		$missingDependencies = craft()->analytics->getMissingDependencies();

		if (count($missingDependencies) > 0)
		{
			$this->renderTemplate('analytics/_special/install/dependencies', [
				'pluginDependencies' => $missingDependencies
			]);
		}
		else
		{
			$this->redirect('analytics/settings');
		}
	}
}