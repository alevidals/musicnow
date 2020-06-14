<?php

namespace app\models;

use app\services\Utility;
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
 * @property int $rol_id
 * @property string|null $auth_key
 * @property string|null $confirm_token
 * @property string|null $url_image
 * @property string|null $image_name
 * @property string|null $url_banner
 * @property string|null $banner_name
 * @property string $created_at
 * @property string|null $deleted_at
 * @property int $estado_id
 * @property bool $privated_account
 *
 * @property Albumes[] $albumes
 * @property Usuarios[] $bloqueados
 * @property Canciones[] $canciones
 * @property Pagos[] $pagos
 * @property Playlists[] $playlists
 * @property Comentarios[] $comentarios
 * @property Usuarios[] $seguidores
 * @property Usuarios[] $seguidos
 * @property Estados $estado_id
 * @property Roles $rol
 * @property Likes[] $likes
 * @property SolicitudesSeguimiento[] $solicitudesSeguimientos
 * @property Canciones[] $cancionesFavoritas
 * @property Chat[] $sendchats
 * @property Chat[] $receivedchats
 * @property Videoclips[] $videoclips
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREAR = 'crear';
    const SCENARIO_UPDATE = 'update';

    public $password_repeat;
    public $image;
    public $banner;

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
            [['rol_id'], 'default', 'value' => 2],
            [['estado_id'], 'default', 'value' => 1],
            [['rol_id', 'estado_id'], 'integer'],
            [['image'], 'image', 'extensions' => ['png', 'jpg'], 'minWidth' => 150, 'maxWidth' => 500, 'minHeight' => 150, 'maxHeight' => 500],
            [['banner'], 'image', 'extensions' => ['png', 'jpg'], 'minWidth' => 1110, 'maxWidth' => 1110, 'minHeight' => 201, 'maxHeight' => 201],
            [['privated_account'], 'boolean'],
            [['login'], 'string', 'max' => 50],
            [['nombre', 'apellidos', 'email', 'password', 'auth_key', 'confirm_token', 'image_name', 'banner_name'], 'string', 'max' => 255],
            [['url_image', 'url_banner'], 'string', 'max' => 2048],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['login'], 'unique'],
            [['password'], 'trim', 'on' => [self::SCENARIO_CREAR, self::SCENARIO_UPDATE]],
            [['password_repeat'], 'trim', 'on' => [self::SCENARIO_CREAR]],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREAR, self::SCENARIO_UPDATE]],
            [['estado_id'], 'exist', 'skipOnError' => true, 'targetClass' => Estados::className(), 'targetAttribute' => ['estado_id' => 'id']],
            [['rol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['rol_id' => 'id']],
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
            'rol_id' => Yii::t('app', 'Rol'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'confirm_token' => Yii::t('app', 'Confirm Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'url_image' => Yii::t('app', 'Url Image'),
            'image_name' => Yii::t('app', 'Image Name'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'url_banner' => Yii::t('app', 'Url Banner'),
            'banner_name' => Yii::t('app', 'Banner Name'),
            'privated_account' => Yii::t('app', 'Privated Account'),
        ];
    }

    /**
     * Sube la imagen o el banner de perfil, del usuario al servidor de
     * almacenamiento Firebase.
     *
     * @param string $type el tipo de fichero a subir, o imagen o banner
     */
    public function uploadImg($type)
    {
        switch ($type) {
            case Utility::PERFIL:
                if ($this->image !== null) {
                    $this->url_image = Utility::uploadImageFirebase($this->image, $this->id, $type);
                    $this->image_name = 'perfil.png';
                }
            break;
            case Utility::BANNER:
                if ($this->banner !== null) {
                    $this->url_banner = Utility::uploadImageFirebase($this->banner, $this->id, $type);
                    $this->banner_name = 'banner.png';
                    Yii::debug($this);
                }
            break;
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

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public static function findByUsername($nombre)
    {
        return static::findOne(['nombre' => $nombre]);
    }

    /**
     * Comprueba si la contraseña es correcta.
     *
     * @param string $password la contraseña a comprobar
     * @return bool true si coindice y en caso contrario false
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $security = Yii::$app->security;
        if ($insert) {
            if ($this->scenario === self::SCENARIO_CREAR) {
                $this->auth_key = $security->generateRandomString();
                if ($this->rol_id != 1) {
                    $this->confirm_token = $security->generateRandomString(255);
                }
                $this->password = $security->generatePasswordHash($this->password);
                $this->url_image = Yii::getAlias('@web/img/user-profile.png');
                // $this->image_name = 'perfil.png';
            }
        } else {
            if ($this->scenario === self::SCENARIO_UPDATE) {
                if ($this->privated_account == false) {
                    $solicitudesPendientes = SolicitudesSeguimiento::find()
                        ->select('seguidor_id')
                        ->where(['seguido_id' => Yii::$app->user->id])
                        ->all();
                    foreach ($solicitudesPendientes as $solicitud) {
                        (new Seguidores([
                            'seguidor_id' => $solicitud->seguidor_id,
                            'seguido_id' => Yii::$app->user->id,
                        ]))->save();
                        SolicitudesSeguimiento::findOne([
                            'seguidor_id' => $solicitud->seguidor_id,
                            'seguido_id' => Yii::$app->user->id,
                        ])->delete();
                    }
                }
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
        return $this->hasOne(Roles::className(), ['id' => 'rol_id']);
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

    /**
     * Gets query for [[Estado]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstado()
    {
        return $this->hasOne(Estados::className(), ['id' => 'estado_id']);
    }

    /** Gets query for [[SendChats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSendChats()
    {
        return $this->hasMany(Chat::className(), ['emisor_id' => 'id']);
    }

    /**
     * Gets query for [[ReceivedChats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceivedChats()
    {
        return $this->hasMany(Chat::className(), ['receptor_id' => 'id']);
    }

    /**
     * Gets query for [[Bloqueados]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloqueados()
    {
        return $this->hasMany(self::className(), ['id' => 'bloqueador_id'])->viaTable('bloqueados', ['bloqueado_id' => 'id']);
    }

    /**
     * Gets query for [[Playlists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlaylists()
    {
        return $this->hasMany(Playlists::className(), ['usuario_id' => 'id']);
    }

    /**
     * Gets query for [[Videoclips]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVideoclips()
    {
        return $this->hasMany(Videoclips::className(), ['usuario_id' => 'id']);
    }

    /**
     * Gets query for [[SolicitudesSeguimientos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitudesSeguimientos()
    {
        return $this->hasMany(SolicitudesSeguimiento::className(), ['seguido_id' => 'id']);
    }

    /**
     * Gets query for [[Pagos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPagos()
    {
        return $this->hasMany(Pagos::className(), ['usuario_id' => 'id']);
    }

    /**
     * Devuelve una consulta donde se comprueba si ambos usuarios se siguen.
     *
     * @return ActiveQuery
     */
    public static function findMutualFollow()
    {
        $seguidoresIds = Seguidores::find()
            ->select('seguidores.seguido_id')
            ->innerJoin('seguidores s2', 'seguidores.seguidor_id = s2.seguido_id AND seguidores.seguido_id = s2.seguidor_id')
            ->where(['seguidores.seguidor_id' => Yii::$app->user->id])
            ->column();

        return static::find()
            ->where(['IN', 'id', $seguidoresIds]);
    }

    /**
     * Devuelve la lista de amigos indexada por id.
     *
     * @return ActiveQuery
     */
    public static function lista()
    {
        return Seguidores::find()
            ->select('u.login')
            ->innerJoin('seguidores s2', 'seguidores.seguidor_id = s2.seguido_id AND seguidores.seguido_id = s2.seguidor_id')
            ->leftJoin('usuarios u', 'u.id = seguidores.seguido_id')
            ->where(['seguidores.seguidor_id' => Yii::$app->user->id])
            ->andWhere(['!=', 'u.rol_id', 3])
            ->indexBy('u.id')
            ->column();
    }

    /**
     * Eliminar la imagen del perfil del servidor de almacenamiento
     * Firebase.
     */
    public function deleteImage()
    {
        Utility::deleteFileFirebase('images/perfil/' . $this->id . '/' . $this->image_name);
    }

    /**
     * Eliminar el banner del perfil del servidor de almacenamiento
     * Firebase.
     */
    public function deleteBanner()
    {
        Utility::deleteFileFirebase('images/perfil/' . $this->id . '/' . $this->banner_name);
    }

    /**
     * Comprueba si un usuario es premium o no.
     *
     * @return bool true si el usuario es premium y false en caso
     * contrario
     */
    public function esPremium()
    {
        return $this->rol_id == 3;
    }
}
