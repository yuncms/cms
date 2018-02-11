<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

// Set the vendor path. By default assume that it's 4 levels up from here
$vendorPath = dirname(__DIR__, 3);

// Load the files
require $vendorPath . '/yiisoft/yii2/Yii.php';
require $vendorPath . '/yuncms/cms/src/Yun.php';

// Set aliases
Yun::setAlias('@lib', $vendorPath . '/yuncms/cms/lib');