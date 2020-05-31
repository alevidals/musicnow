<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "generos".
 *
 * @property int $id
 * @property string $denominacion
 * @property string $created_at
 *
 * @property Canciones[] $canciones
 */
class Generos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'generos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['denominacion'], 'required'],
            [['created_at'], 'safe'],
            [['denominacion'], 'string', 'max' => 255],
            [['denominacion'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'denominacion' => Yii::t('app', 'Denominacion'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Canciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanciones()
    {
        return $this->hasMany(Canciones::className(), ['genero_id' => 'id'])->inverseOf('genero');
    }

    /**
     * Devuelve la denominación de todos los géneros indexados por el id
     *
     * @return array
     */
    public static function lista()
    {
        return static::find()
            ->select('denominacion')
            ->indexBy('id')
            ->column();
    }
}
