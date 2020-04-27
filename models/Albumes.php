<?php

namespace app\models;

use app\services\Utility;
use Yii;

/**
 * This is the model class for table "albumes".
 *
 * @property int $id
 * @property string $titulo
 * @property float $anyo
 * @property string $image_name
 * @property string $url_portada
 * @property string $created_at
 * @property int $usuario_id
 *
 * @property Usuarios $usuario
 * @property AlbumesCanciones[] $albumesCanciones
 * @property Canciones[] $canciones
 */
class Albumes extends \yii\db\ActiveRecord
{
    private $_total = null;
    public $portada;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'albumes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titulo', 'anyo', 'image_name', 'url_portada', 'usuario_id'], 'required'],
            [['anyo'], 'number'],
            [['created_at'], 'safe'],
            [['usuario_id'], 'default', 'value' => null],
            [['usuario_id'], 'integer'],
            [['portada'], 'image', 'extensions' => ['png', 'jpg'], 'minWidth' => 500, 'maxWidth' => 1000, 'minHeight' => 500, 'maxHeight' => 1000],
            [['titulo', 'image_name'], 'string', 'max' => 255],
            [['url_portada'], 'string', 'max' => 2048],
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
            'anyo' => Yii::t('app', 'Anyo'),
            'image_name' => Yii::t('app', 'Image Name'),
            'url_portada' => Yii::t('app', 'Url Portada'),
            'created_at' => Yii::t('app', 'Created At'),
            'usuario_id' => Yii::t('app', 'Usuario ID'),
        ];
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id'])->inverseOf('albumes');
    }

    /**
     * Gets query for [[AlbumesCanciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlbumesCanciones()
    {
        return $this->hasMany(AlbumesCanciones::className(), ['album_id' => 'id']);
    }

    /**
     * Gets query for [[Canciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanciones()
    {
        return $this->hasMany(Canciones::className(), ['album_id' => 'id']);
    }

    /**
     * Devuelve el título de todos los álbumes indexados por el id.
     *
     * @return array
     */
    public static function lista()
    {
        return static::find()
            ->select('titulo')
            ->indexBy('id')
            ->column();
    }

    public function setTotal($total)
    {
        $this->_total = $total;
    }

    public function getTotal()
    {
        if ($this->_total === null && !$this->isNewRecord) {
            $this->setTotal($this->getCanciones()->count());
        }
        return $this->_total;
    }

    public static function findWithTotal()
    {
        return static::find()
            ->select(['albumes.*', 'COUNT(c.id) AS total'])
            ->joinWith('canciones c', false)
            ->groupBy('albumes.id');
    }

    public function uploadPortada()
    {
        if ($this->portada !== null) {
            $uploadedImageInfo = Utility::uploadImageFirebase($this->portada, Yii::$app->user->id, Utility::PORTADA);
            $this->url_portada = $uploadedImageInfo['url'];
            $this->image_name = $uploadedImageInfo['image_name'];
        }
    }

    public function deletePortada()
    {
        Utility::deleteFileFirebase('images/portada/' . Yii::$app->user->id . '/' . $this->image_name);
    }

}
