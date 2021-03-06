<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\console\controllers;

/**
 * Class MigrateController
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class MigrateController extends \yii\console\controllers\MigrateController
{
    /**
     * {@inheritdoc}
     */
    public $templateFile = '@yuncms/views/migration.php';
}