<?php
/**
 * @copyright Copyright Nils Menrad 2015
 * @version 1.0.0
 */

namespace backend\components\jstree;

use yii\web\AssetBundle;

/**
 * Asset bundle for JsTree Widget
 *
 * @author Nils Menrad
 * @since 1.0
 */
class JsTreeAsset extends AssetBundle
{
    
    public $sourcePath = '@app/components/jstree/assets';
    public $css = [
        'jstree/dist/themes/default/style.css',
    ];
    public $js = [
        'jstree/dist/jstree.min.js',
        'easytree.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $publishOptions = [
        'forceCopy'=>true,
    ];
    
}
