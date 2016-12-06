<?php

namespace app\controllers;

use Yii;
use app\models\Treetest;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TreetestController implements the CRUD actions for Treetest model.
 */
class TreetestController extends Controller
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
            'modelName'=>'app\models\Treetest',
            'modelFirstParentId' => NULL,
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
     * Updates an existing Treetest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Treetest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Treetest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Treetest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Treetest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
