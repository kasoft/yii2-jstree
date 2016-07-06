<?php /**
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
class JsTreeAsset extends AssetBundle
{
    public $sourcePath = '@bower/jstree';
    public $js = [
        'dist/jstree.min.js',
    ];
    public $css = [
        'dist/themes/default/style.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'kasoft\jstree\JsTreeAsset'
    ];
}
