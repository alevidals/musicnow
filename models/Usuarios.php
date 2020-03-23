<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $login
 * @property string $nombre
 * @property string $apellidos
 * @property string $email
 * @property string $password
 * @property string|null $fnac
 * @property int $rol
 * @property string|null $auth_key
 * @property string|null $confirm_token
 * @property string $created_at
 *
 * @property Albumes[] $albumes
 * @property Canciones[] $canciones
 * @property Roles $rol
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREAR = 'crear';

    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'nombre', 'apellidos', 'email'], 'required'],
            [['password'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_CREAR]],
            [['fnac', 'created_at'], 'safe'],
            [['rol'], 'default', 'value' => 2],
            [['rol'], 'integer'],
            [['login'], 'string', 'max' => 50],
            [['nombre', 'apellidos', 'email', 'password', 'auth_key', 'confirm_token'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['login'], 'unique'],
            [['password'], 'trim', 'on' => [self::SCENARIO_CREAR]],
            [['password_repeat'], 'trim', 'on' => [self::SCENARIO_CREAR]],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREAR]],
            [['rol'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['rol' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'login' => Yii::t('app', 'Login'),
            'nombre' => Yii::t('app', 'Nombre'),
            'apellidos' => Yii::t('app', 'Apellidos'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'fnac' => Yii::t('app', 'Fnac'),
            'rol' => Yii::t('app', 'Rol'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'confirm_token' => Yii::t('app', 'Confirm Token'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Albumes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlbumes()
    {
        return $this->hasMany(Albumes::className(), ['usuario_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public static function findByUsername($nombre)
    {
        return static::findOne(['nombre' => $nombre]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $security = Yii::$app->security;
        if ($insert) {
            if ($this->scenario === self::SCENARIO_CREAR) {
                $this->auth_key = $security->generateRandomString();
                $this->confirm_token = $security->generateRandomString(255);
                $this->password = $security->generatePasswordHash($this->password);
            }
        }

        return true;
    }

    /**
     * Gets query for [[Canciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanciones()
    {
        return $this->hasMany(Canciones::className(), ['usuario_id' => 'id']);
    }

    /**
     * Gets query for [[Rol]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRol()
    {
        return $this->hasOne(Roles::className(), ['id' => 'rol']);
    }

}
