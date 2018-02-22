<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\base;

/**
 * Trait ApplicationTrait
 * @property \yuncms\components\Settings $settings The settings manager component
 * @property \yii\authclient\Collection $authClientCollection The authClient Collection component
 * @property \yii\queue\Queue $queue The queue component
 * @property \yuncms\notifications\NotificationManager $notifications The notifications component
 * @property \yuncms\components\Volumes $volumes The volumes component
 * @property \yii\redis\Connection $redis The redis component
 */
trait ApplicationTrait
{
    /**
     * Returns the settings component.
     * @return \yuncms\components\Settings the settings connection.
     */
    public function getSettings()
    {
        return $this->get('settings');
    }

    /**
     * Returns the queue component.
     * @return \yii\queue\Queue the queue connection.
     */
    public function getQueue()
    {
        return $this->get('queue');
    }

    /**
     * Returns the notifications component.
     * @return \yuncms\notifications\NotificationManager the notifications connection.
     */
    public function getNotifications()
    {
        return $this->get('notifications');
    }

    /**
     * Returns the authClientCollection component.
     * @return \yii\authclient\Collection the authClientCollection connection.
     */
    public function getAuthClientCollection()
    {
        return $this->get('authClientCollection');
    }
}