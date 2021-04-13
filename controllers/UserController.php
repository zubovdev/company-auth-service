<?php

namespace app\controllers;

use app\models\Authorization;
use app\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class DefaultActiveController
 * @package app\controllers
 */
class UserController extends ActiveController
{

    public $modelClass = User::class;
    public $createScenario = User::SCENARIO_CREATE;
    public $updateScenario = User::SCENARIO_UPDATE;

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['create', 'login', 'options'],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'except' => ['options'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['?'],
                    'actions' => ['login', 'create'],
                ],
                [
                    'allow' => true,
                    'roles' => ['@'],
                    'actions' => ['user', 'view'],
                ],
                [
                    'allow' => true,
                    'roles' => ['admin'],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * {@inheritDoc}
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create']['checkAccess']);
        return $actions;
    }

    /**
     * @return IdentityInterface|null
     * @throws UnauthorizedHttpException
     */
    public function actionUser(): ?IdentityInterface
    {
        $user = Yii::$app->user->identity;
        if ($user->isActive) {
            return $user;
        }

        throw new UnauthorizedHttpException('User is not active');
    }

    /**
     * @param $id
     * @return User|string[]
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     */
    public function actionChangePassword($id)
    {
        $user = User::findOne($id);
        if ($user === null) {
            throw new BadRequestHttpException("User not found: {$id}.");
        }

        $user->scenario = User::SCENARIO_CHANGE_PWD;
        $user->load(Yii::$app->request->getBodyParams(), '');
        if (!$user->save()) {
            return $user;
        }

        return ['message' => 'New password sent to email.'];
    }

    /**
     * @return User|array
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        /** @var User $user */
        $user = new $this->modelClass(['scenario' => User::SCENARIO_LOGIN]);
        $user->load(Yii::$app->request->getBodyParams(), '');
        if (!$user->validate()) {
            return $user;
        }

        /** @var User $user */
        $user = User::findOne(['email' => $user->email]);
        if ($user === null) {
            throw new ServerErrorHttpException('Failed to login user for unknown reason.');
        }

        $auth = new Authorization(['userId' => $user->id]);
        if (!$auth->save(false)) {
            throw new ServerErrorHttpException('Failed to generate token for unknown reason.');
        }

        return ['token' => $auth->token];
    }

    /**
     * {@inheritDoc}
     */
    public function checkAccess($action, $model = null, $params = []): void
    {
        /** @var User $model */
        if ($action === 'view' && Yii::$app->user->id !== $model->id && !Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }

        if (in_array($action, ['update', 'delete', 'change-password'], true) && $model->email === 'admin@admin.ru') {
            throw new ForbiddenHttpException('Cannot edit or delete administrator.');
        }
    }

}
