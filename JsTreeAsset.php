<?php 
/**
 * Publishes all JsTree Assets, more Informations on https://www.jstree.com/
 * @link http://www.studio255.de/
 * @author Nils Menrad
 * @since 1.0
 * @see http://jstree.com
 */

namespace kasoft\jstree;
use Yii;
use yii\web\AssetBundle;

class JsTreeAsset extends AssetBundle
{
    
    public $sourcePath = '@bower/vakata/jstree';
    
    public function __construct($config = array()) {
        $path = Yii::getAlias('@bower/vakata/jstree');
        if (!is_dir($path)) $this->sourcePath = '@bower/jstree';
        else $this->sourcePath = '@bower/vakata/jstree';
        parent::__construct($config);
    }
    
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