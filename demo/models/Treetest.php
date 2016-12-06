<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tree_test".
 *
 * @property integer $id
 * @property integer $parentId
 * @property string $name
 * @property integer $position
 * @property integer $type
 */
class Treetest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tree_test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parentId', 'position', 'type'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parentId' => 'Parent ID',
            'name' => 'Name',
            'position' => 'Position',
            'type' => 'Type',
        ];
    }
}
