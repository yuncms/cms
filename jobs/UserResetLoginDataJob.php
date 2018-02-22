<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use yuncms\models\User;

/**
 * Class UserResetLoginDataJob
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class UserResetLoginDataJob extends BaseObject implements JobInterface
{
    /**
     * @var string
     */
    public $user_id;

    /**
     * @var string
     */
    public $ip;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        if (($user = User::findOne($this->user_id)) != null) {
            $user->extra->updateAttributes(['login_at' => time(), 'login_ip' => $this->ip, 'login_num' => $user->extra->login_num + 1]);
        }
    }
}