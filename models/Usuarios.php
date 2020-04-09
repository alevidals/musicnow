<?php

namespace app\models;

use app\services\Utility;
use Yii;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

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
 * @property string|null $url_image
 * @property string|null $image_name
 * @property string $created_at
 * @property string|null $deleted_at
 *
 * @property Albumes[] $albumes
 * @property Canciones[] $canciones
 * @property Comentarios[] $comentarios
 * @property Usuarios[] $seguidores
 * @property Usuarios[] $seguidos
 * @property Roles $rol
 * @property Likes[] $likes
 * @property Canciones[] $cancionesFavoritas
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREAR = 'crear';
    const SCENARIO_UPDATE = 'update';

    public $password_repeat;
    public $image;

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
            [['fnac', 'created_at', 'deleted_at'], 'safe'],
            [['rol'], 'default', 'value' => 2],
            [['rol'], 'integer'],
            [['image'], 'image', 'extensions' => ['png', 'jpg'], 'minWidth' => 150, 'maxWidth' => 500, 'minHeight' => 150, 'maxHeight' => 500],
            [['login'], 'string', 'max' => 50],
            [['nombre', 'apellidos', 'email', 'password', 'auth_key', 'confirm_token', 'image_name'], 'string', 'max' => 255],
            [['url_image'], 'string', 'max' => 2048],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['login'], 'unique'],
            [['password'], 'trim', 'on' => [self::SCENARIO_CREAR, self::SCENARIO_UPDATE]],
            [['password_repeat'], 'trim', 'on' => [self::SCENARIO_CREAR]],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREAR, self::SCENARIO_UPDATE]],
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
            'password_repeat' => Yii::t('app', 'Password Repeat'),
            'fnac' => Yii::t('app', 'Fnac'),
            'rol' => Yii::t('app', 'Rol'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'confirm_token' => Yii::t('app', 'Confirm Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'url_image' => Yii::t('app', 'Url Image'),
            'image_name' => Yii::t('app', 'Image Name'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
        ];
    }

    public function uploadImg($perfilImg)
    {
        Yii::debug('jaja');
        Yii::debug($this->image);
        if ($this->image !== null) {
            $this->url_image = Utility::uploadImageFirebase($this->image, $this->id, $perfilImg);
            $this->image_name = 'perfil.png';
        }
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
                $this->url_image = Yii::$app->params['defaultImgProfile'];
                $this->image_name = 'perfil.png';
            }
        } else {
            if ($this->scenario === self::SCENARIO_UPDATE) {
                if ($this->password === '') {
                    $this->password = $this->getOldAttribute('password');
                } else {
                    $this->password = $security->generatePasswordHash($this->password);
                }
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

    /**
     * Gets query for [[Seguidores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSeguidores()
    {
        return $this->hasMany(self::className(), ['id' => 'seguidor_id'])->viaTable('seguidores', ['seguido_id' => 'id']);
    }

    /**
     * Gets query for [[Seguidos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSeguidos()
    {
        return $this->hasMany(self::className(), ['id' => 'seguido_id'])->viaTable('seguidores', ['seguidor_id' => 'id']);
    }

    /**
     * Gets query for [[Canciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCancionesFavoritas()
    {
        return $this->hasMany(Canciones::className(), ['id' => 'cancion_id'])->viaTable('likes', ['usuario_id' => 'id']);
    }

    /**
     * Gets query for [[Likes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLikes()
    {
        return $this->hasMany(Likes::className(), ['usuario_id' => 'id']);
    }

    /**
     * Gets query for [[Comentarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['usuario_id' => 'id']);
    }
}
