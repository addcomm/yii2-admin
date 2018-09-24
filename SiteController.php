<?php

namespace backend\controllers;

use backend\assets\AdminLTEAsset;
use backend\assets\LoginAsset;
use backend\components\BackendController;
use backend\models\ChangePasswordForm;
use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;
use common\components\Tools;
use common\models\SignupForm;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\BadRequestHttpException;

/**
 * Site controller
 */
class SiteController extends BackendController
{
    public $defaultAction = 'login';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow'   => true,
                    ],
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','index','profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            /*'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],*/
        ];
    }

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if ( !Yii::$app->user->isGuest ) {
            return $this->goHome();
        }

        $this->view->registerAssetBundle(LoginAsset::className());

        $model = new LoginForm(['scenario' => 'login_email']);
        if ( $model->load(Yii::$app->request->post()) && $model->login() ) {
            return $this->goBack();
        } else {
            return $this->render('login.twig', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return string
     */
    public function actionProfile(){

        $data = [];

        $modelChangePassword = new ChangePasswordForm(Yii::$app->user->id);

        $data['modelChangePassword'] = $modelChangePassword;

        $data['Tools'] = new Tools();

        try{

            if(!empty($_POST['ChangePasswordForm'])){

                $modelChangePassword->setAttributes($_POST['ChangePasswordForm']);

                if($modelChangePassword->validate()){

                    $modelChangePassword->updatePassword();

                    Yii::$app->session->setFlash('success',\Yii::t('backend/controllers/SiteController','Your password has been updated.') );

                    $modelChangePassword = new ChangePasswordForm(Yii::$app->user->id);

                    $data['modelChangePassword'] = $modelChangePassword;

                }else{

                    Throw new Exception(Tools::modelErrorsToString($modelChangePassword->getErrors()));
                }
            }

        }catch (\Throwable $error){

            Yii::$app->session->setFlash('error', $error->getMessage() );
        }

        return $this->render('profile.twig',$data);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $this->view->registerAssetBundle(LoginAsset::className());

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup.twig', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }
        return $this->render('requestPasswordResetToken.twig', [
            'model' => $model,
        ]);
    }
    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return $this->goHome();
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
