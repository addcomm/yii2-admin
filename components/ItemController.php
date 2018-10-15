<?php

namespace mdm\admin\components;

use common\components\Tools;
use common\models\AuthItemChild;
use Yii;
use mdm\admin\models\AuthItem;
use mdm\admin\models\searchs\AuthItem as AuthItemSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\NotSupportedException;
use yii\filters\VerbFilter;
use yii\rbac\Item;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 *
 * @property integer $type
 * @property array $labels
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class ItemController extends \mdm\admin\components\Yii2adminController
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
                    'delete' => ['post'],
                    'assign' => ['post'],
                    'remove' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param  string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem(null);
        $model->type = $this->type;
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
             $this->_insertDefaultRoutes($model);
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            //get the routes related to this role.
            $modelRoutes = ArrayHelper::map(AuthItemChild::findAll(['parent'=>$model->name]),'child','rights');
            return $this->render('create', ['model' => $model,'modelRoutes'=>$modelRoutes]);
        }
    }
    
    /**
     * _insertDefaultRoutes
     * @param AuthItem $modelAuthItem
     * @throws Exception
     */
    protected function _insertDefaultRoutes(AuthItem $modelAuthItem) : void
    {

        try{

            foreach (Yii::$app->params['ADMIN-DEFAULT-ROUTES'] as $route){

                $modelAuthItemChild         = new AuthItemChild();
                $modelAuthItemChild->parent = $modelAuthItem->name;
                $modelAuthItemChild->child  = $route;
                $modelAuthItemChild->rights = 'read,write';

                if(!$modelAuthItemChild->save()){
                    throw new Exception(Tools::modelErrorsToString($modelAuthItemChild->getAttributes()));

                }
            }

        }catch (\Throwable $error){
            throw new Exception( $error->getMessage());
        }

    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param  string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(!empty(Yii::$app->getRequest()->post())){

            if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {

                //import controller rights
                if(!empty(Yii::$app->getRequest()->post('Rights'))){

                    $rights = Yii::$app->getRequest()->post('Rights');

                    //clear the current rights
                    $modelAuthItem = AuthItemChild::findAll(['parent'=>$model->name]);

                    foreach ($modelAuthItem as $right){

                        $right->rights = null;
                        $right->save();
                    }

                    foreach($rights as $route =>$right){

                        $modelAuthItem = AuthItemChild::findOne(['parent'=>$model->name,'child'=>$route]);

                        if(!empty($modelAuthItem)){

                            $modelAuthItem->rights = implode(',',array_keys($right));

                            if(!$modelAuthItem->save()){
                                die(Tools::modelErrorsToString($modelAuthItem->getErrors()));
                            }
                        }
                    }
                }

            }

        }

        //get the routes related to this role.
        $modelRoutes = ArrayHelper::map(AuthItemChild::findAll(['parent'=>$model->name]),'child','rights');

        return $this->render('update', ['model' => $model,'modelRoutes'=>$modelRoutes]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Configs::authManager()->remove($model->item);
        Helper::invalidate();

        return $this->redirect(['index']);
    }

    /**
     * Assign items
     * @param string $id
     * @return array
     */
    public function actionAssign($id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $model = $this->findModel($id);
        $success = $model->addChildren($items);
        Yii::$app->getResponse()->format = 'json';

        return array_merge($model->getItems(), ['success' => $success]);
    }

    /**
     * Assign or remove items
     * @param string $id
     * @return array
     */
    public function actionRemove($id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $model = $this->findModel($id);
        $success = $model->removeChildren($items);
        Yii::$app->getResponse()->format = 'json';

        return array_merge($model->getItems(), ['success' => $success]);
    }

    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . 'item';
    }

    /**
     * Label use in view
     * @throws NotSupportedException
     */
    public function labels()
    {
        throw new NotSupportedException(get_class($this) . ' does not support labels().');
    }

    /**
     * Type of Auth Item.
     * @return integer
     */
    public function getType()
    {
        
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $auth = Configs::authManager();
        $item = $this->type === Item::TYPE_ROLE ? $auth->getRole($id) : $auth->getPermission($id);
        if ($item) {
            return new AuthItem($item);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
