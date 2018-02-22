<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\console;

use Yii;
use yuncms\base\ApplicationTrait;

/**
 * Class Application
 *
 * @property User $user             The user component
 * @package yuncms\console
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 1.0
 */
class Application extends \yii\console\Application
{
    use ApplicationTrait;


}