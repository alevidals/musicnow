<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "provincias".
 *
 * @property int $id
 * @property string $denominacion
 *
 * @property Pagos[] $pagos
 */
class Provincias extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'provincias';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['denominacion'], 'required'],
            [['denominacion'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * Gets query for [[Pagos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPagos()
    {
        return $this->hasMany(Pagos::className(), ['provincia_id' => 'id']);
    }

    /**
     * Devuelve la denominación de todas las provincias indexadas por el id
     *
     * @return void
     */
    public static function lista()
    {
        return static::find()
            ->select('denominacion')
            ->indexBy('id')
            ->column();
    }
}
