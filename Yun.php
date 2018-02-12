<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

use GuzzleHttp\Client;
use yii\helpers\VarDumper;

require __DIR__ . '../../yiisoft/yii2/Yii.php';

/**
 * Class Yun
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 1.0
 */
class Yun extends Yii
{
    /**
     * @var \yuncms\web\Application|\yuncms\console\Application|\yii\console\Application|\yii\web\Application The application instance.
     */
    public static $app;

    /**
     * Returns a string representing the current version of the Yii framework.
     * @return string the version of Yii framework
     */
    public static function getYiiVersion()
    {
        return parent::getVersion();
    }

    /**
     * Displays a variable.
     *
     * @param mixed $var The variable to be dumped.
     * @param int $depth The maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param bool $highlight Whether the result should be syntax-highlighted. Defaults to true.
     *
     * @return void
     */
    public static function dump($var, int $depth = 10, bool $highlight = true)
    {
        VarDumper::dump($var, $depth, $highlight);
    }

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

// Set aliases
Yun::setAlias('@resources', __DIR__ . '/resources');

if (getenv('APP_ENV') == 'Development') {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
} else {
    defined('YII_DEBUG') or define('YII_DEBUG', false);
    defined('YII_ENV') or define('YII_ENV', 'prod');
}