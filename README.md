Extension für jsTree Plugin
===========================
jsTree for Yii2 is a Extension to display an ActiveRecord Model with jsTree.

This Extension is at developement at the moment. The following functions are
implemented and should work.

- load tree data with ajax and display tree
- define icons for different tree items
- context menu with update, rename and delete
- move tree items by drag'n'drop 


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kasoft/yii2-jstree "@dev"
```

or add

```
"kasoft/yii2-jstree": "@dev"
```

to the require section of your `composer.json` file.


Usage
-----

This Extension can be use in 2 Ways. 

1. The simple Version just displays the tree with a provided json url. You have 
to take care of the json part and all other tree operations by yourself. In this
case just set 'jsonUrl'

a) Add this to your Controller action
$tree = new \kasoft\jstree\JsTree([
    'jsonUrl' => $url,
]);

b) add a div with <div id="jstree"></div> to your view

---

2. Together with a Database (tested with MySQL) and a set of Fields to order and
structure the tree. The Tree is displayed in a DIV. The Extensions handels create,
move, rename and delete for you. A click on a Tree Item will dipslay the form to
edith data in another div. See the Test Setup. You have to adjust your Controllers
and views!

Set up you Database with the needed fileds (can have different names)

name            Name or Titel to Display in in the tree
parent_id       Id for nesting the tree
position        For sorting the tree items
type            Type of the Item, used for Icon and rights (still in developement)

The Filednames can be configured as shown below. Just copy this code in 
your Controller 

```php
<?
public function actionIndex() {
        
        // required database fields:
        // name, parent_id, type, position
        
        $tree = new \kasoft\jstree\JsTree([
            'modelName'=>'backend\models\MY_MODEL_NAME',    // Namespace of the Model
            'modelPropertyId' => 'id'                       // primary Key
            'modelFirstParentId' => NULL,                   // ID for the Tree to start
            'modelPropertyName' => 'name',                  // Fieldname to show
            'modelPropertyParentId' => 'parentId',          // Parent ID for tree items
            'modelPropertyPosition' => 'position',          // for sorting items
        ]);
        
        if (isset($_REQUEST["easytree"])) {
            $tree->treeaction();
            Yii::$app->end();
        }
        
        return $this->render('index');
        
    }

 ?>


Put this in the index view file
<div id="jstree"></div>
<div class="result"></div>


