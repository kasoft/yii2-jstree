<?php

namespace kasoft\jstree;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\bootstrap\Widget;
use yii\web\View;
use kasoft\jstree\JsTreeAsset;

/**
 * JsTree widget is a Yii2 wrapper for the jsTree jQuery plugin.
 *
 * @author Nils Menrad
 * @since 1.0
 * @see http://jstree.com
 */
class JsTree extends Widget
{

    // Basic Settings Model/Column Names
    public $modelName;              // Name of the Model as String
    public $modelFirstParentId;     // Start the Tree with this parent_id, can be NULL
    public $modelPropertyId;        // Column Name of the Primary Key e.g. 'id'
    public $modelPropertyParentId;  // Column Name of the Parent Key e.g. 'parent_id'
    public $modelPropertyName;      // Column Name of the Title/Name e.g. 'title'
    public $modelPropertyPosition;  // Column Name of the Position attribute e. g. 'position'
    public $modelPropertyType;      // Column Name of the Position attribute e. g. 'position'
    public $modelStandardName;      // String for a new Node if not entered by the user
    public $modelPropertyOnline;    // Boolean for Online/Offlien = Italic non Italic Text
    
    
    // Not fully implemented
    // relative url to controller action
    public $ajaxUrl;                
 
    public $modelCondition;         // not implementes yet, additionl conditions
    public $modelAddCondition;      // not implementes yet, additionl conditions
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->registerAssets();
     
        echo "<pre>";
        var_dump($this->getView());
        die();
        
        if (empty($this->ajaxUrl))
            $this->ajaxUrl = "index";
        $this->getView()->registerJs("var ajax_url = '".$this->ajaxUrl."';",View::POS_HEAD);
        
        /*
         * https://github.com/yiisoft/yii2/issues/305
         * $config['token'] = 1234
         * 
            $this->registerJs("var Yii = ". json_encode($config).";",View::POS_HEAD);
         */
        
        
        if (empty($this->modelPropertyName))
            $this->modelPropertyName = "name";
        
        if (empty($this->modelPropertyId))
            $this->modelPropertyId = "id";
        
        if (empty($this->modelPropertyParentId))
            $this->modelPropertyParentId="parent_id";
        
        if (empty($this->modelPropertyPosition))
            $this->modelPropertyPosition="sort";
        
        if (empty($this->modelPropertyType))
            $this->modelPropertyType="type";
        
        if (empty($this->modelPropertyOnline))
            $this->modelPropertyOnline="online";
        else 
            $this->modelPropertyOnline=NULL;
        
        if (empty($this->modelStandardName))
            $this->modelStandardName="Neuer Eintrag";
    }

    
    public function run() {
        parent::run();
    }
    
    /**
     * Registers the needed assets
     */
    public function registerAssets() {
        $view = $this->getView();
        JsTreeAsset::register($view);

    }

    // AJAX call for 
    // -> load: return full json for tree init
    // -> move: change parent id and position of node
    public function treeaction() {
        if (isset($_REQUEST["easytree"])) {
            $operation = $_REQUEST["easytree"];
            if ($operation=="fulljson")
                echo json_encode (self::treeChildren($this->modelName,$this->modelFirstParentId));
            if ($operation=="move") {
                $modelName = $this->modelName;
                $model = $modelName::findOne($_POST["id"]);
                if ($model) {
                    $model->{$this->modelPropertyParentId} = $_POST["parent"];
                    $model->{$this->modelPropertyPosition} = $_POST["position"];
                    
                    // find all in the same node but without already re-orderd item
                    $sort = $modelName::find()
                            ->where([$this->modelPropertyParentId => $_POST["parent"]])
                            ->andWhere(['!=',$this->modelPropertyId,$_POST["id"]])
                            ->orderBy($this->modelPropertyPosition)
                            ->all();
                    
                    // If moved to top 
                    if ($_POST["position"]!=0) $pos = 0;
                    else $pos=1;
                    
                    foreach($sort as $s) {
                        if ($pos==$_POST["position"]) 
                            $pos++;
                        $s->{$this->modelPropertyPosition} = $pos;
                        $s->save();
                        $pos++;
                    }
                    
                    if ($model->save())
                        self::sendJSON(array('status' => 1));
                }
            }
            if ($operation=="create") {
                $modelName = $this->modelName;
                $model = new $modelName;
                $model->{$this->modelPropertyParentId} = $_POST["parent"];
                $model->{$this->modelPropertyPosition} = $_POST["position"];
                $model->{$this->modelPropertyType} = $_POST["type"];
                $model->{$this->modelPropertyName} = $this->modelStandardName;
                
                if ($model->save())
                    self::sendJSON(array('status' => 1,'id'=>$model->{$this->modelPropertyId}));
                else 
                    print_r($model->getErrors());
            }
            if ($operation=="rename") {
                $modelName = $this->modelName;
                $model = $modelName::findOne($_POST["id"]);
                $model->{$this->modelPropertyName} =  $_POST["text"];
                if ($model->save())
                    self::sendJSON(array('status' => 1));
                else 
                    print_r($model->getErrors());
            }
            if ($operation=="delete") {
                // check for children
                $check = self::treeChildren($this->modelName,$_POST["id"]);
                if (empty($check)) {
                    $modelName = $this->modelName;
                    $model = $modelName::findOne($_POST["id"]);
                    if (!$model) {
                        self::sendJSON(array('status' => 0,'error'=>'Eintrag existiert nicht!'));    
                    } else {
                       if ($model->delete()) {
                           self::sendJSON(array('status' => 1));
                        } else {
                            self::sendJSON(array('status' => 0,'error'=>'Eintrag konnten nicht gelöscht werden!'));
                        }
                    }
                } else {
                    self::sendJSON(array('status' => 0,'error'=>'Eintrag enthält Unterobjekte und kann nicht gelöscht werden!'));
                }
            }
        } else {
            return false;
        }
    }
    
    public function treeChildren($modelName,$parent_id=NULL) {
        $models = $modelName::find()
                ->where([$this->modelPropertyParentId => $parent_id])
                ->orderBy($this->modelPropertyPosition)
                ->all();
        
        $data = [];
        foreach($models as $item) {
            // $name = preg_replace('/[^-\w\d .,äöüÖÄÜß]/', "", $item->{$this->modelPropertyName});
            $name= $item->{$this->modelPropertyName};
            if ($this->modelPropertyOnline) {
                if (!$item->{$this->modelPropertyOnline})
                    $name= "<i>(offline) ".$item->{$this->modelPropertyName}."</i>";
            } 
            $data[] = ['id'=>"id".$item->{$this->modelPropertyId},'parent'=>($item->{$this->modelPropertyParentId})?"id".$item->{$this->modelPropertyParentId}:"#",'type'=>$item->{$this->modelPropertyType},'text'=> $name];
            $mixin = self::treeChildren($modelName, $item->{$this->modelPropertyId});
            if (!empty($mixin)) $data = array_merge($data,$mixin);
        }
        return $data;
    }
    
    static function sendJSON($json) {
        header("HTTP/1.0 200 OK");
        header('Content-type: text/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo json_encode($json);
    }
    

}
?>
