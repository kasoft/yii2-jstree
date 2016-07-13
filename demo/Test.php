<?php

namespace app\models;

use Yii;
use Html;
use yii\base\Exception;

/**
 * This is the model class for table "admin_menue".
 *
 * @property integer $id
 * @property integer $parentId
 * @property string $name
 * @property string $link
 * @property integer $position
 * @property integer $admin
 * @property integer $hidden
 * @property string $only_for
 */
class Test extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parentId', 'position'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'parentId' => 'Parent',
            'name' => 'Name',
            'position' => 'Position',
        );
    }
    
   
}
