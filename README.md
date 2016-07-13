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

You need some fileds in your Database to allow all the Extension to work.

name            Name or Titel to Display in in the tree
parent_id       Id for nesting the tree
position        For sorting the tree items
type            Type of the Item, used for Icon and right as far as implemented

The Filednames can be configured as shown below. Just copy this code in 
your Controller 

```php
<?
public function actionIndex() {
        
        // required database fields:
        // name, parent_id, online, type, position
        
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


