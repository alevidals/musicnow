<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "albumes".
 *
 * @property int $id
 * @property string $titulo
 * @property float $anyo
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
            [['titulo', 'anyo', 'usuario_id'], 'required'],
            [['anyo'], 'number'],
            [['created_at'], 'safe'],
            [['usuario_id'], 'default', 'value' => null],
            [['usuario_id'], 'integer'],
            [['titulo'], 'string', 'max' => 255],
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
        return $this->hasMany(AlbumesCanciones::className(), ['album_id' => 'id'])->inverseOf('album');
    }

    /**
     * Gets query for [[Canciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanciones()
    {
        return $this->hasMany(Canciones::className(), ['album_id' => 'id'])->inverseOf('album');
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


}
