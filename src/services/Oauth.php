<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace dukt\analytics\services;

use Craft;
use yii\base\Component;
use League\OAuth2\Client\Token\AccessToken;
use Dukt\OAuth2\Client\Provider\Google;
use craft\helpers\UrlHelper;
use dukt\analytics\Plugin as Analytics;

class Oauth extends Component
{
    // Properties
    // =========================================================================

    private $token;

    // Public Methods
    // =========================================================================

    /**
     * Save Token
     *
     * @param AccessToken $token
     */
    public function saveToken(AccessToken $token)
    {
        // Save token and token secret in the plugin's settings

        $plugin = Craft::$app->getPlugins()->getPlugin('analytics');

        $settings = $plugin->getSettings();

        $token = [
            'accessToken' => $token->getToken(),
            'expires' => $token->getExpires(),
            'refreshToken' => $token->getRefreshToken(),
            'resourceOwnerId' => $token->getResourceOwnerId(),
            'values' => $token->getValues(),
        ];

        $settings->token = $token;

        Craft::$app->getPlugins()->savePluginSettings($plugin, $settings->getAttributes());
    }

    /**
     * Returns the OAuth Token.
     *
     * @param bool $refresh
     *
     * @return AccessToken
     */
    public function getToken($refresh = true)
    {
        if (!$this->token) {
            $plugin = Craft::$app->getPlugins()->getPlugin('analytics');
            $settings = $plugin->getSettings();

            if ($settings->token) {
                $token = new AccessToken([
                    'access_token' => (isset($settings->token['accessToken']) ? $settings->token['accessToken'] : null),
                    'expires' => (isset($settings->token['expires']) ? $settings->token['expires'] : null),
                    'refresh_token' => (isset($settings->token['refreshToken']) ? $settings->token['refreshToken'] : null),
                    'resource_owner_id' => (isset($settings->token['resourceOwnerId']) ? $settings->token['resourceOwnerId'] : null),
                    'values' => (isset($settings->token['values']) ? $settings->token['values'] : null),
                ]);

                if ($refresh && $token->hasExpired()) {
                    $provider = $this->getOauthProvider();
                    $grant = new \League\OAuth2\Client\Grant\RefreshToken();
                    $newToken = $provider->getAccessToken($grant, ['refresh_token' => $token->getRefreshToken()]);

                    $token = new AccessToken([
                        'access_token' => $newToken->getToken(),
                        'expires' => $newToken->getExpires(),
                        'refresh_token' => $settings->token['refreshToken'],
                        'resource_owner_id' => $newToken->getResourceOwnerId(),
                        'values' => $newToken->getValues(),
                    ]);

                    $this->saveToken($token);
                }

                return $token;
            }
        }

        return $this->token;
    }

    /**
     * Delete Token
     *
     * @return bool
     */
    public function deleteToken()
    {
        $plugin = Craft::$app->getPlugins()->getPlugin('analytics');

        $settings = $plugin->getSettings();
        $settings->token = null;
        Craft::$app->getPlugins()->savePluginSettings($plugin, $settings->getAttributes());

        return true;
    }

    /**
     * Returns a Twitter provider (server) object.
     *
     * @return Google
     */
    public function getOauthProvider()
    {
        $options = [
            'clientId' => Analytics::$plugin->getSettings()->oauthClientId,
            'clientSecret' => Analytics::$plugin->getSettings()->oauthClientSecret
        ];

        $options = array_merge($options, Analytics::$plugin->getSettings()->oauthProviderOptions);

        if (!isset($options['redirectUri'])) {
            $options['redirectUri'] = $this->getRedirectUri();
        }

        return new Google($options);
    }

    public function getJavascriptOrigin()
    {
        return UrlHelper::baseUrl();
    }

    public function getRedirectUri()
    {
        return UrlHelper::actionUrl('analytics/oauth/callback');
    }
}
