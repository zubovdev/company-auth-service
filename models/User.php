<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;

use function Webmozart\Assert\Tests\StaticAnalysis\null;

/**
 * Class User
 *
 * @property int $id
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $city
 * @property string $passwordHash
 * @property bool $isActive
 * @property int $createTime
 * @property int $updateTime
 *
 * @package app\models
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const SCENARIO_LOGIN = 'login';
    public const SCENARIO_UPDATE = 'update';
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_CHANGE_PWD = 'change-pwd';

    /**
     * @var string|null
     */
    public ?string $password = null;

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createTime',
                'updatedAtAttribute' => 'updateTime',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_CREATE => ['email', 'firstName', 'lastName', 'city'],
            self::SCENARIO_LOGIN => ['email', 'password'],
            self::SCENARIO_UPDATE => ['isActive'],
            self::SCENARIO_CHANGE_PWD => ['password'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'firstName', 'lastName', 'city'], 'required'],
            [['email', 'firstName', 'lastName', 'city'], 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique', 'on' => self::SCENARIO_CREATE],
            ['isActive', 'default', 'value' => false],
            ['isActive', 'boolean'],
            [
                'email',
                'exist',
                'filter' => fn(ActiveQuery $query) => $query->andWhere(['isActive' => true]),
                'on' => self::SCENARIO_LOGIN,
            ],
            ['password', 'required', 'on' => [self::SCENARIO_LOGIN, self::SCENARIO_CHANGE_PWD]],
            [
                'password',
                'validatePassword',
                'on' => self::SCENARIO_LOGIN,
                'when' => fn() => !$this->hasErrors('email'),
            ],
        ];
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function validatePassword(): void
    {
        /** @var User $user */
        $user = self::findOne(['email' => $this->email]);
        if ($user === null) {
            throw new ServerErrorHttpException('Failed to validate user identity');
        }

        if (!Yii::$app->security->validatePassword($this->password, $user->passwordHash)) {
            $this->addError('password', 'Incorrect password');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset($fields['passwordHash']);
        return $fields;
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->isActive = (new Query())->from('whitelist_email')->where(['email' => $this->email])->exists();
        }

        if ($insert || $this->scenario === self::SCENARIO_CHANGE_PWD) {
            $pwd = Yii::$app->security->generateRandomString(8);
            $this->passwordHash = Yii::$app->security->generatePasswordHash($pwd);

            $mail = Yii::$app->mailer->compose('views/default', ['pwd' => $pwd])
                ->setSubject('Пароль от учетной записи Company')
                ->setFrom(Yii::$app->params['smtp.user'])
                ->setTo($this->email);

            if (!$mail->send()) {
                throw new ServerErrorHttpException('Failed to send message with password');
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function findIdentity($id): ?IdentityInterface
    {
        return static::findOne($id);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        /** @var Authorization $auth */
        $auth = Authorization::find()
            ->with('user')
            ->where(['token' => $token])
            ->limit(1)
            ->one();

        if ($auth === null) {
            return null;
        }

        return $auth->user;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->lastName} {$this->firstName}";
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey): string
    {
        return '';
    }
}
