<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\web;

use Yii;
use yuncms\base\ApplicationTrait;

/**
 * Class Application
 * @property Request $request The request component
 * @property Response $response The response component
 * @property User $user The user component
 * @package yuncms\web
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 1.0
 */
class Application extends \yii\web\Application
{
    use ApplicationTrait;

    /**
     * @inheritdoc
     */
    public function setVendorPath($path)
    {
        parent::setVendorPath($path);

        // Override the @bower and @npm aliases if using asset-packagist.org
        // todo: remove this whenever Yii is updated with support for asset-packagist.org
        $altBowerPath = $this->getVendorPath() . DIRECTORY_SEPARATOR . 'bower-asset';
        $altNpmPath = $this->getVendorPath() . DIRECTORY_SEPARATOR . 'npm-asset';
        if (is_dir($altBowerPath)) {
            Yii::setAlias('@bower', $altBowerPath);
        }
        if (is_dir($altNpmPath)) {
            Yii::setAlias('@npm', $altNpmPath);
        }

        // Override where Yii should find its asset deps
        Yii::setAlias('@bower/bootstrap/dist', '@yuncms/resources/lib/bootstrap');
        Yii::setAlias('@bower/jquery/dist', '@yuncms/resources/lib/jquery');
        Yii::setAlias('@bower/inputmask/dist', '@yuncms/resources/lib/inputmask');
        Yii::setAlias('@bower/punycode', '@yuncms/resources/lib/punycode');
        Yii::setAlias('@bower/yii2-pjax', '@yuncms/resources/lib/yii2-pjax');
        Yii::setAlias('@bower/font-awesome', '@yuncms/resources/lib/font-awesome');
    }
}