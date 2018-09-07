Extension für jsTree Plugin
===========================
jsTree for Yii2 is a Extension to display an handle ActiveRecord Models with jsTree.

- load tree data with ajax and display tree
- define icons for different tree items (with FontAwesome)
- context menu with update, rename and delete
- move tree items by drag'n'drop 


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kasoft/yii2-jstree "1.0.7"
```

or add

```
"kasoft/yii2-jstree": "1.0.7",
```

to the require section of your `composer.json` file.

Latest News
-----

Version 1.0.7
- Added modelPropertyType with default value + online/offline glyphicons as default

Version 1.0.6
- Fixed a Problem with Yii 2.0.14, because of a diffrent Error Handling, the Tree wasn't displayes
- REQUEST check in Controller isn't needed any more

Version 1.0.5
- Selecting a Node will trigger update action via Ajax and load result in .jstree-result div.
If the .jstree-result is not found, it will redirect to Update Action
- Changed all Submenu Icons to Glyhicon

Version 1.0.4
- Updated Composer Settings

Version 1.0.3
- Selected multiple Nodes are all deleted


Usage without Model (JSON only)
-----
The simple Version just displays the tree with a provided json url. You have 
to provide the json data by an url  

```php
$tree = new \kasoft\jstree\JsTree([
    'jsonUrl' => '/my_jsonurl/data/whatever',
]);
```

```html
<div id="jstree"></div>
```


Usage with Yii2 Model 
-----
If you want to use the Extension so Display the Tree from your Data, do Operations
like move, create, delete, rename and open the form view by Click use this Version.

Before you start, check your Database or your Model! Do you have all required Fields?

Together with a Database (tested with MySQL) and a set of Fields to order and
structure the tree are needed. The Tree is displayed in a DIV. The Extensions handels 
create, move, rename and delete for you. A click on a Tree Item will dipslay the form to
edith data in another div. 

See the Test Setup in the "demo" Folder! 

Set up you Database with the needed fileds (can have different names)
*name            Name or Titel to Display in in the tree (required)
*parent_id       Id for nesting the tree (required)
*position        For sorting the tree items (required)
*type            Type of the Item, used for Icon and rights (optional)

Set up your Model with these Fields. Important: Only the name is allwoed to be
a required Field! Otherwise the Contextmenue "New" will probably not work.


Add this to your Controller. All Items with (*) are required!

```php
<?
public function actionIndex() {
        $tree = new \kasoft\jstree\JsTree([
            'modelName'=>'backend\models\MY_MODEL_NAME',    // * Namespace of the Model
            'modelPropertyId' => 'id'                       // * primary Key
            'modelPropertyParentId' => 'parentId',          // * Parent ID for tree items
            'modelPropertyPosition' => 'position',          // *for sorting items
            'modelPropertyName' => 'name',                  // * Fieldname to show
            'modelFirstParentId' => NULL,                   // * ID for the Tree to start
            'modelPropertyType' => 'type',                  // Item type (for Icon and jsTree rights)
            'controllerId' => 'index',                      // Controler Actions which should handle everything
            'jstreeDiv' => '#jstree',                       // DIV where the Tree will be displayed
            'jstreeIcons' => false,                         // Show Icons or not
            'jstreePlugins' => ["contextmenu", "dnd",..],   // Plugins to be load
            'jstreeType' => [                               // jsTree Type Options
                "#" => [
                    "max_children" => -1,
                    "max_depth" => -1,
                    "valid_children" => -1, 
                    "icon" => "glyphicon glyphicon-th-list"
                ],
                "default" => [
                    "icon" => "glyphicon glyphicon-question-sign"
                ],
            ]
        ]);
        
        return $this->render('index');
    }
 ?>
```

Put this in the index view file. A Click on an Item will delegate the update
action to the result field. Also the form should have a class named "jstree-form"
to delegate the result of the form submit to the div.

```html
<div id="jstree"></div>
<div class="result"></div>
```


