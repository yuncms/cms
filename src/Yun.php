<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

use Yii;
use GuzzleHttp\Client;

/**
 * Class Yun
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 1.0
 */
class Yun extends Yii
{
    /**
     * Creates a Guzzle client configured with the given array merged with any default values.
     *
     * @param array $config Guzzle client config settings
     *
     * @return Client
     */
    public static function createGuzzleClient(array $config = []): Client
    {
        // Set the Craft header by default.
        $defaultConfig = [
            'headers' => [
                'User-Agent' => 'Yun/' . self::$app->getVersion() . ' ' . \GuzzleHttp\default_user_agent()
            ],
        ];

        // Maybe they want to set some config options specifically for this request.
        $guzzleConfig = array_replace_recursive($defaultConfig, $config);

        return new Client($guzzleConfig);
    }

}