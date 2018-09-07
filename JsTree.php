<?php

/**
 * JsTree widget is a Yii2 wrapper for the jsTree jQuery plugin with extended
 * functions.
 *
 * @author Nils Menrad
 * @since 1.0
 * @see http://jstree.com
 */

namespace kasoft\jstree;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\bootstrap\Widget;
use yii\web\View;
use yii\web\Response;
use yii\helpers\Url;
use kasoft\jstree\JsTreeAsset;

class JsTree extends Widget
{

    // Basic Settings Model/Column Names

    /*
     * @var object the yii2 active record model which should be used. set to false
     * if the tree should be build from a json url. note: only with a model, the 
     * move, delete, sort, and create functions can be used
     */
    public $modelName = false;

    /*
     * @var string URL that will generate the needed json for buildung the tree. 
     * Only needed when you DON'T set the modelName Property. Otherwise not needed.
     */
    public $jsonUrl = false;

    /*
     * @var int ID where the Tree should start loading stuff, can be NULL too 
     */
    public $modelFirstParentId;

    /*
     * @var string Name of the PrimaryKey Column e.g. 'id'
     */
    public $modelPropertyId;

    /*
     * @var string Column Name of the Parent Key e.g. 'parent_id'
     */
    public $modelPropertyParentId;

    /*
     * @var string Column Name of the Title/Name e.g. 'title'
     */
    public $modelPropertyName;

    /*
     * @var string Column Name of the Position attribute e. g. 'position'
     */
    public $modelPropertyPosition;

    /*
     * @var string Column Name for a Type attribute. This can be used to give each
     * Tree item a different Type which will be used for displaying icons and can 
     * be also used to set allwoed/disallowed functions (child creation, new nodes, etc.)
     * This is not implemented at the Moment! 
     */
    public $modelPropertyType = NULL;     // Column Name of the type Attribute og Node e. g. 'type'

    /*
     * @var string Text for the initial name of a new node. As a new node will be craeted
     * first as a blank entry, this text will be set. Standard will be used if empty 
     */
    public $modelStandardName;          // String for a new Node if not entered by the user
    // JS Vars
    
    /*
     * @var string Name of the Controller used for calling ajax actions.
     */
    public $controller;           // controller name for ajax call "cms"
    
    /*
     * @var string Name of the Action that should be called when node is clicked
     */
    public $action_click;
    
    /*
     * @var string Default Action for Tree (e.g. index)
     */
    public $action_default;

    public $jstreeIcons=NULL;
    public $jstreeType;
    public $jstreePlugins;
    /*
     * @var string ID oder Class of the JsTree Div
     * If not set, it will become #jstree
     */
    public $jstreeDiv;
    
    /*
     * With this state key, jstree sets a cookie to remember the state of the
     * tree (e.g. which bracnhes are open, etc.)
     */
    public $jstreestatekey;

    /**
     * @var array Configure which plugins will be active on an instance. Should be an array of strings, where each element is a plugin name.
     */
    public $plugins = ["checkbox"];

    // NOT IMPLEMENTED, DEVEPOLMENT
    public $showIcons;              // Show Type/Icons in Tree
    public $modelCondition;         // not implementes yet, additionl conditions
    public $modelAddCondition;      // not implementes yet, additionl conditions

    /**
     * @inheritdoc
     */

    public function init() {
        parent::init();
        $this->registerAssets();
        
        if (empty($this->jstreeDiv)) 
            $this->jstreeDiv  = "#jstree";
        
        if ($this->jstreeIcons===NULL)
            $this->jstreeIcons = true;
        
        if (empty($this->jstreePlugins)) {
            $this->jstreePlugins = [
                "contextmenu", "dnd", "search","state", "types", "wholerow", "changed"
            ];
        }
        
        if (empty($this->jstreeType)) {
            $this->jstreeType = [
                "#" => [
                    "max_children" => -1,
                    "max_depth" => -1,
                    "valid_children" => -1, 
                    "icon" => "glyphicon glyphicon-th-list"
                ],
                "default" => [
                    "max_children" => -1,
                    "max_depth" => -1,
                    "valid_children" => -1, 
                    "icon" => "glyphicon glyphicon-list-alt"
                ],
                "online" => [
                    "max_children" => -1,
                    "max_depth" => -1,
                    "valid_children" => -1, 
                    "icon" => "glyphicon glyphicon-ok-sign"
                ],
                "offline" => [
                    "max_children" => -1,
                    "max_depth" => -1,
                    "valid_children" => -1, 
                    "icon" => "glyphicon glyphicon-minus-sign"
                ],
            ];
        }
        
        $this->getView()->registerJs("var jstreediv = '" . $this->jstreeDiv . "';", View::POS_HEAD);
        $this->getView()->registerJs("var jstreetype = " . Json::encode($this->jstreeType) . ";", View::POS_HEAD);
        $this->getView()->registerJs("var jstreeplugins = " . Json::encode($this->jstreePlugins) . ";", View::POS_HEAD);
        $this->getView()->registerJs("var jstreeicons = " . Json::encode($this->jstreeIcons) . ";", View::POS_HEAD);
        

        // Use with ActiveRecord Model and all Actions 
        if ($this->modelName) {

            $this->controller = Yii::$app->controller->id;
            
            if (empty($this->action_default))
                $this->action_default = "index";
            
            if (empty($this->action_click))
                $this->action_click = "update";
            
            // Create Needed URLs for JS
            // e.g. /index.php?r=site/index
            // or /site/index
            $baseUrl = Url::to();
            $this->getView()->registerJs("var url_default = '" . Url::to([$this->controller."/".$this->action_default]) . "';", View::POS_HEAD);
            $this->getView()->registerJs("var url_click = '" . Url::to([$this->controller."/".$this->action_click]) . "';", View::POS_HEAD);

            if(empty($this->jstreestatekey))
                $this->jstreestatekey = $this->controller;
            $this->getView()->registerJs("var jstreestatekey = " . Json::encode($this->jstreestatekey) . ";", View::POS_HEAD);
            
            if (!isset($this->showIcons))
                $this->showIcons = true;

            if (empty($this->modelPropertyName))
                $this->modelPropertyName = "name";

            if (empty($this->modelPropertyId))
                $this->modelPropertyId = "id";

            if (empty($this->modelPropertyParentId))
                $this->modelPropertyParentId = "parent_id";

            if (empty($this->modelPropertyPosition))
                $this->modelPropertyPosition = "sort";
            
            if (empty($this->modelPropertyType))
                $this->modelPropertyType = "type";

            if (empty($this->modelStandardName))
                $this->modelStandardName = "New Entry";
            
            // Only Display Tree with loading Data via JSON URL    
        } else {
            $this->getView()->registerJs("var jsonurl = '" . $this->jsonUrl . "';", View::POS_HEAD);
        }
        
        if (isset($_REQUEST["easytree"])) {
            $this->treeaction();
        }
        
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
    /*
     * Handelns Ajax calls of different Operations
     * fulljson: return full json for tree init
     * create: create new node
     * rename: change the name of the node
     * position: move the node, change parent id and position of node
     * delete: delete node
     */
    public function treeaction() {
        
        if (isset($_REQUEST["easytree"])) {
            $operation = $_REQUEST["easytree"];
            if ($operation == "fulljson")
               self::sendJSON(self::treeChildren($this->modelName, $this->modelFirstParentId));
            if ($operation == "move") {
                $modelName = $this->modelName;
                $model = $modelName::findOne($_POST["id"]);
                if ($model) {
                    $model->{$this->modelPropertyParentId} = $_POST["parent"];
                    $model->{$this->modelPropertyPosition} = $_POST["position"];

                    // find all in the same node but without already re-orderd item
                    $sort = $modelName::find()
                            ->where([$this->modelPropertyParentId => $_POST["parent"]])
                            ->andWhere(['!=', $this->modelPropertyId, $_POST["id"]])
                            ->orderBy($this->modelPropertyPosition)
                            ->all();

                    // If moved to top 
                    if ($_POST["position"] != 0)
                        $pos = 0;
                    else
                        $pos = 1;

                    foreach ($sort as $s) {
                        if ($pos == $_POST["position"])
                            $pos++;
                        $s->{$this->modelPropertyPosition} = $pos;
                        $s->save();
                        $pos++;
                    }

                    if ($model->save())
                        self::sendJSON(array('status' => 1));
                }
            }
            if ($operation == "create") {
                $modelName = $this->modelName;
                $model = new $modelName;
                $model->{$this->modelPropertyParentId} = $_POST["parent"];
                $model->{$this->modelPropertyPosition} = $_POST["position"];
                if ($this->modelPropertyType)
                    $model->{$this->modelPropertyType} = $_POST["type"];
                $model->{$this->modelPropertyName} = $this->modelStandardName;

                if ($model->save())
                    self::sendJSON(array('status' => 1, 'id' => $model->{$this->modelPropertyId}));
                else
                    print_r($model->getErrors());
            }
            if ($operation == "rename") {
                $modelName = $this->modelName;
                $model = $modelName::findOne($_POST["id"]);
                $model->{$this->modelPropertyName} = $_POST["text"];
                if ($model->save())
                    self::sendJSON(array('status' => 1));
                else
                    print_r($model->getErrors());
            }
            if ($operation == "delete") {
                // check for children
                $check = self::treeChildren($this->modelName, $_POST["id"]);
                if (empty($check)) {
                    $modelName = $this->modelName;
                    $model = $modelName::findOne($_POST["id"]);
                    if (!$model) {
                        self::sendJSON(array('status' => 0, 'error' => 'Item does not exist!'));
                    }
                    else {
                        if ($model->delete()) {
                            self::sendJSON(array('status' => 1));
                        }
                        else {
                            self::sendJSON(array('status' => 0, 'error' => 'Item cannot be deleted!'));
                        }
                    }
                }
                else {
                    self::sendJSON(array('status' => 0, 'error' => 'Item has children and cannot be deleted! Delete first all children.'));
                }
            }
        }
        else {
            return false;
        }
    }

    /*
     * Load Childitems of tree
     */

    public function treeChildren($modelName, $parent_id = NULL) {
        $models = $modelName::find()
                ->where([$this->modelPropertyParentId => $parent_id])
                ->orderBy($this->modelPropertyPosition)
                ->all();

        $data = [];
        foreach ($models as $item) {
            $name = $item->{$this->modelPropertyName};

            //if tree entry id is top id, set parent to null
            if ($item->{$this->modelPropertyParentId} == $this->modelFirstParentId)
                $parent = "#";
            else
                $parent = "id" . $item->{$this->modelPropertyParentId};

            if ($this->modelPropertyType)
                $type = $item->{$this->modelPropertyType};
            else
                $type = "default";

            $data[] = ['id' => "id" . $item->{$this->modelPropertyId}, 'parent' => $parent, 'type' => $type, 'text' => $name];
            $mixin = self::treeChildren($modelName, $item->{$this->modelPropertyId});
            if (!empty($mixin))
                $data = array_merge($data, $mixin);
        }
        return $data;
    }

    static function sendJSON($json) {
        header("HTTP/1.0 200 OK");
        header('Content-type: text/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo json_encode($json);
        die();
    }

}

?>
