<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\web;

use Yun;
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
            Yun::setAlias('@bower', $altBowerPath);
        }
        if (is_dir($altNpmPath)) {
            Yun::setAlias('@npm', $altNpmPath);
        }

        // Override where Yii should find its asset deps
        $libPath = Yun::getAlias('@lib');
        Yun::setAlias('@bower/bootstrap/dist', $libPath . '/bootstrap');
        Yun::setAlias('@bower/jquery/dist', $libPath . '/jquery');
        Yun::setAlias('@bower/inputmask/dist', $libPath . '/inputmask');
        Yun::setAlias('@bower/punycode', $libPath . '/punycode');
        Yun::setAlias('@bower/yii2-pjax', $libPath . '/yii2-pjax');
        Yun::setAlias('@bower/font-awesome', $libPath . '/font-awesome');
    }
}