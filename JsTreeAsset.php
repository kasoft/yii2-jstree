<?php 
/**
 * @link http://www.studio255.de/
 * @author Nils Menrad
 * @since 1.0
 * @see http://jstree.com
 */

namespace kasoft\jstree;
use yii\web\AssetBundle;

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
        'kasoft\jstree\JsTreeBridgeAsset'
    ];
}