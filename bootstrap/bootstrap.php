<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

// Load the files
$cmsPath = $vendorPath.'/yuncms/cms';
$libPath = $cmsPath.'/lib';
$srcPath = $cmsPath.'/src';
require $vendorPath.'/yiisoft/yii2/Yii.php';
require $srcPath.'/Yun.php';

// Set aliases
Yun::setAlias('@lib', $libPath);