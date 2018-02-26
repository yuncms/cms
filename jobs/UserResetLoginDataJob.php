<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\jobs;

use yuncms\models\User;

/**
 * 重新设置用户登录时间、IP、和次数
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class UserResetLoginDataJob extends BaseJob
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