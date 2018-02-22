<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\web;

use Yii;
use yuncms\jobs\UserLastVisitJob;
use yuncms\jobs\UserResetLoginDataJob;

/**
 * Class User
 *
 * @package yuncms\web
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class User extends \yii\web\User
{
    /**
     * @var string the class name of the [[identity]] object.
     */
    public $identityClass = 'yuncms\models\User';

    /**
     * @inheritdoc
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        Yii::$app->queue->push(new UserLastVisitJob(['user_id' => Yii::$app->user->getId(), 'time' => time()]));
        parent::afterLogin($identity, $cookieBased, $duration);
    }
}