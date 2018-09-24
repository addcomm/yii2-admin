<?php

namespace mdm\admin\controllers;

use common\components\Tools;
use mdm\admin\components\ItemController;
use Yii;
use yii\rbac\Item;

/**
 * RoleController implements the CRUD actions for AuthItem model.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class RoleController extends ItemController
{
    /**
     * @inheritdoc
     */
    public function labels()
    {
        return[
            'Item' => 'Role',
            'Items' => 'Roles',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return Item::TYPE_ROLE;
    }

    /**
     * actionUpdateRights
     * @return string
     */
    public function actionUpdateRights() : string
    {

        $response = array('response'=>'NOK','message'=>Yii::t('app','MESSAGE'),'data'=>'');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try{

             if(Yii::$app->request->isAjax){

             }else{

                 throw new Exception('Only Ajax request',400);
             }

        }catch (Exception $error){

            $error_code = !empty($error->getCode()) ? $error->getCode() : 400;

            Yii::$app->response->statusCode = $error_code;

            $response['message']= $error->getMessage();
        }

        return $response;

    }

}
