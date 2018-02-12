<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\base;

use yuncms\components\Settings;
use yuncms\notifications\NotificationManager;

/**
 * Trait ApplicationTrait
 * @property Settings $settings The settings manager component
 * @property \yii\authclient\Collection $authClientCollection The authClient Collection component
 * @property \yii\queue\Queue $queue The queue component
 * @property NotificationManager $notifications The notifications component
 * @property \yuncms\components\Volumes $volumes The volumes component
 * @property \yii\redis\Connection $redis The redis component
 */
trait ApplicationTrait
{

}