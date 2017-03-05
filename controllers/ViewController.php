<?php

namespace app\controllers;

use Yii;
use app\models\View;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * ViewController implements the CRUD actions for View model.
 */
class ViewController extends Controller
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

    /**
     * Lists all View models.
     * @return mixed
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->getId();
        $activeViewId = null;
        $views = View::findAll(['user_id' => $userId]);

        // No view was created, create default
        if (count($views) == 0) {
            $view = new View();
            $view->name = 'Default';
            $view->user_id = $userId;
            $view->active = 1;
            $view->save();
            array_push($views,$view);
        }

        foreach ($views as $temp)
        {
            if ($temp->getAttribute('active') == 1)
            {
                $activeViewId = $temp->getAttribute('id');
            }
        }

        // no active view was found, select default view returned by reset
        if (!isset($activeViewId) && reset($views))
        {
            reset($views)->active = 1;
            reset($views)->save();
        }

        return $this->render('index', [
            'views' => $views,
            'activeViewId' => $activeViewId
        ]);
    }

    /**
     * Displays a single View model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new View model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new View();

        // setting currently active view to false
        $userId = Yii::$app->user->getId();
        $views = View::findAll(['user_id' => $userId]);
        foreach ($views as $temp)
        {
            if ($temp->getAttribute('active') == 1)
            {
                $temp->active = 0;
                $temp->save();
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing View model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing View model.
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
     * Change of currently selected view to one selected from drop down with views.
     *
     * @param $viewId - id of currently selected view
     * @return $components - components of new selected view
     */
    public function actionChangeView($viewId)
    {
        $userId = Yii::$app->user->getId();

        // requested view does not exist
        $view = View::findOne(['user_id' => $userId, 'id' => $viewId]);
        if (empty($view))
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $components = $this->getComponentsOfView($viewId);
        $activeViewId = View::findOne(['user_id' => $userId, 'active' => 1])->getAttribute('id');

        $this->changeActiveAttributeOfView($viewId, 1);

        if ($activeViewId != $viewId) $this->changeActiveAttributeOfView($activeViewId, 0);

        return Json::encode($components);
    }

    public function actionCreateComponent($viewId, $config)
    {
        $component = new View\Component();
        $component->view_id = $viewId;
        $component->config = $config;

        return $component->save() ? $component->id : false;
    }

    public function actionUpdateComponent($componentId, $config)
    {
        $component = View\Component::findOne($componentId);
        $component->config = $config;

        return !empty($component) ? ($component->update() ? $component->id : false) : false;
    }

    public function actionDeleteComponent($componentId)
    {
        $component = View\Component::findOne($componentId);

        return !empty($component) ? ($component->delete() ? true : false) : false;
    }

    /**
     * Finds the View model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return View the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = View::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function getComponentsOfView($viewId)
    {
        $components = View\Component::findAll(['view_id' => $viewId]);

        return $components;
    }

    /**
     * Change attribute active for view.
     *
     * @param $viewId - id of view for attribute change
     * @param $active - attribute active - 0/1
     * @return $viewId - id of view for attribute change
     */
    protected function changeActiveAttributeOfView($viewId, $active)
    {
        $view = View::findOne($viewId);
        $view->active = $active;
        $view->update();

        return $viewId;
    }
}
