<?php

namespace app\models;

use app\services\Utility;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "canciones".
 *
 * @property int $id
 * @property string $titulo
 * @property int $album_id
 * @property int $genero_id
 * @property string $url_cancion
 * @property string $song_name
 * @property string $url_portada
 * @property string $image_name
 * @property float $anyo
 * @property string $duracion
 * @property int $usuario_id
 * @property string $created_at
 *
 * @property AlbumesCanciones[] $albumesCanciones
 * @property Albumes $album
 * @property Comentarios[] $comentarios
 * @property Generos $genero
 * @property Usuarios $usuario
 * @property Likes[] $likes
 * @property Usuarios[] $usuarios
 */
class Canciones extends \yii\db\ActiveRecord
{

    public $portada;
    public $cancion;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'canciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titulo', 'album_id', 'genero_id', 'url_cancion', 'song_name', 'url_portada', 'image_name', 'anyo', 'duracion', 'usuario_id'], 'required'],
            [['album_id', 'genero_id', 'usuario_id'], 'default', 'value' => null],
            [['album_id', 'genero_id', 'usuario_id'], 'integer'],
            [['anyo'], 'number'],
            [['duracion'], 'string'],
            [['created_at'], 'safe'],
            [['portada'], 'image', 'extensions' => ['png', 'jpg'], 'minWidth' => 500, 'maxWidth' => 1000, 'minHeight' => 500, 'maxHeight' => 1000],
            [['cancion'], 'file', 'extensions' => ['mp3'], 'maxSize' => 1024 * 1024 * 20],
            [['titulo', 'song_name', 'image_name'], 'string', 'max' => 255],
            [['url_cancion', 'url_portada'], 'string', 'max' => 2048],
            [['album_id'], 'exist', 'skipOnError' => true, 'targetClass' => Albumes::className(), 'targetAttribute' => ['album_id' => 'id']],
            [['genero_id'], 'exist', 'skipOnError' => true, 'targetClass' => Generos::className(), 'targetAttribute' => ['genero_id' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'titulo' => Yii::t('app', 'Titulo'),
            'album_id' => Yii::t('app', 'Album ID'),
            'genero_id' => Yii::t('app', 'Genero ID'),
            'url_cancion' => Yii::t('app', 'Url Cancion'),
            'song_name' => Yii::t('app', 'Song Name'),
            'image_name' => Yii::t('app', 'Image Name'),
            'url_portada' => Yii::t('app', 'Url Portada'),
            'anyo' => Yii::t('app', 'Anyo'),
            'duracion' => Yii::t('app', 'Duracion'),
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function uploadPortada()
    {
        if ($this->portada !== null) {
            $this->url_portada = Utility::uploadImageFirebase($this->portada, Yii::$app->user->id);
            $this->image_name = str_replace(' ', '', $this->portada->name);
        }
    }

    public function uploadCancion()
    {
        if ($this->cancion !== null) {
            $this->url_cancion = Utility::uploadFileFirebase($this->cancion, Yii::$app->user->id);
            $this->song_name = str_replace(' ', '', $this->cancion->name);
        }
    }

    public function deletePortada()
    {
        Utility::deleteFileFirebase('images/portada/' . Yii::$app->user->id . '/' . $this->image_name);
    }

    public function deleteCancion()
    {
        Utility::deleteFileFirebase('canciones/' . Yii::$app->user->id . '/' . $this->song_name);
    }

    /**
     * Gets query for [[AlbumesCanciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlbumesCanciones()
    {
        return $this->hasMany(AlbumesCanciones::className(), ['canciones_id' => 'id'])->inverseOf('canciones');
    }

    /**
     * Gets query for [[Album]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlbum()
    {
        return $this->hasOne(Albumes::className(), ['id' => 'album_id'])->inverseOf('canciones');
    }

    /**
     * Gets query for [[Genero]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGenero()
    {
        return $this->hasOne(Generos::className(), ['id' => 'genero_id'])->inverseOf('canciones');
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id'])->inverseOf('canciones');
    }

    /**
     * Gets query for [[Likes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLikes()
    {
        return $this->hasMany(Likes::className(), ['cancion_id' => 'id']);
    }
    /**
     * Gets query for [[Usuarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuarios::className(), ['id' => 'usuario_id'])->viaTable('likes', ['cancion_id' => 'id']);
    }

    /**
     * Gets query for [[Comentarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['cancion_id' => 'id']);
    }

    public static function findWithTotalLikes()
    {
        return static::find()
            ->select(['canciones.*', 'COUNT(l.cancion_id) AS likes'])
            ->joinWith('likes l', false)
            ->groupBy('canciones.id');
    }
}
