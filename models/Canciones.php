<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "canciones".
 *
 * @property int $id
 * @property string $titulo
 * @property int $album_id
 * @property int $genero_id
 * @property string $url_cancion
 * @property string $url_portada
 * @property float $anyo
 * @property string $duracion
 * @property int $usuario_id
 * @property string $created_at
 *
 * @property AlbumesCanciones[] $albumesCanciones
 * @property Albumes $album
 * @property Generos $genero
 * @property Usuarios $usuario
 */
class Canciones extends \yii\db\ActiveRecord
{
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
            [['titulo', 'album_id', 'genero_id', 'url_cancion', 'url_portada', 'anyo', 'duracion', 'usuario_id'], 'required'],
            [['album_id', 'genero_id', 'usuario_id'], 'default', 'value' => null],
            [['album_id', 'genero_id', 'usuario_id'], 'integer'],
            [['anyo'], 'number'],
            [['duracion'], 'string'],
            [['created_at'], 'safe'],
            [['titulo'], 'string', 'max' => 255],
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
            'url_portada' => Yii::t('app', 'Url Portada'),
            'anyo' => Yii::t('app', 'Anyo'),
            'duracion' => Yii::t('app', 'Duracion'),
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
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
}
