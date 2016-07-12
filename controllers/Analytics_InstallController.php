<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2016, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
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
		$plugin = craft()->plugins->getPlugin('analytics');
		$pluginDependencies = $plugin->getMissingDependencies();

		if (count($pluginDependencies) > 0)
		{
			$this->renderTemplate('analytics/_special/install/dependencies', ['pluginDependencies' => $pluginDependencies]);
		}
		else
		{
			craft()->analytics->requireDependencies();

			$provider = craft()->oauth->getProvider('google');

			if ($provider && $provider->isConfigured())
			{
				$this->redirect('analytics/settings');
			}
			else
			{
				$this->renderTemplate('analytics/_special/install/oauth-provider-not-configured');
			}
		}
	}
}