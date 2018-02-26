<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */


namespace yuncms\jobs;

use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;

/**
 * Class BaseJob
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
abstract class BaseJob extends BaseObject implements RetryableJobInterface
{
    public $ttr = 60;

    public $attempt = 3;

    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return $this->ttr;
    }

    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error)
    {
        return $attempt < $this->attempt;
    }
}