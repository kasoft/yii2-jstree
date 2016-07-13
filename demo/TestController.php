<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminmenuController implements the CRUD actions for AdminMenu model.
 */
class AdminmenuController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    
    public function actionIndex() {
        
        // required database fields:
        // name, parent_id, online, type, position
        
        $tree = new \kasoft\jstree\JsTree([
            'modelName'=>'@app\models\Test',
            'modelFirstParentId' => 2,
            'modelPropertyName' => 'name',
            'modelPropertyParentId' => 'parentId',
            'modelPropertyPosition' => 'position',
        ]);
        
        if (isset($_REQUEST["easytree"])) {
            $tree->treeaction();
            Yii::$app->end();
        }
        
        return $this->render('index');
        
    }


    /**
     * Updates an existing AdminMenu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->layout = false;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the AdminMenu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdminMenu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Test::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
