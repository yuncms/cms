<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\jobs;

use yuncms\models\User;

/**
 * 更新用户最后活动时间
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class UserLastVisitJob extends BaseJob
{
    /**
     * @var int user id
     */
    public $user_id;

    /**
     * @var int 最后活动时间
     */
    public $time;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        if (($user = User::findOne(['id' => $this->user_id])) != null) {
            $user->extra->updateAttributes(['last_visit' => $this->time]);
        }
    }
}