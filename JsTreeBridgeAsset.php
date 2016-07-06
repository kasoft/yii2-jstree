<?php
/**
 * @link http://www.studio255.de/
 * @copyright Copyright (c) 2016 Nils Menrad
 * @license http://www.yiiframework.com/license/
 */

namespace kasoft\jstree;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class JsTreeBridgeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/kasoft/yii2-jstree/assets';
    public $css = [
    ];
    public $js = [
        'js/easytree.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'kasoft\jstree\JsTreeAsset'
    ];
}
