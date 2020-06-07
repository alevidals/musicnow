<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pagos".
 *
 * @property int $id
 * @property string|null $payment
 * @property string|null $cart
 * @property string $nombre
 * @property string $apellidos
 * @property int $provincia_id
 * @property string $direccion
 * @property int $usuario_id
 * @property int|null $receptor_id
 * @property string $created_at
 *
 * @property Provincias $provincia
 * @property Usuarios $receptor
 * @property Usuarios $usuario
 */
class Pagos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pagos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'apellidos', 'provincia_id', 'direccion', 'usuario_id'], 'required'],
            [['provincia_id', 'usuario_id', 'receptor_id'], 'default', 'value' => null],
            [['provincia_id', 'usuario_id', 'receptor_id'], 'integer'],
            [['created_at'], 'safe'],
            [['payment', 'cart'], 'string', 'max' => 50],
            [['nombre', 'apellidos', 'direccion'], 'string', 'max' => 255],
            [['provincia_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provincias::className(), 'targetAttribute' => ['provincia_id' => 'id']],
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
            'payment' => Yii::t('app', 'Payment'),
            'cart' => Yii::t('app', 'Cart'),
            'nombre' => Yii::t('app', 'Nombre'),
            'apellidos' => Yii::t('app', 'Apellidos'),
            'provincia_id' => Yii::t('app', 'Provincia ID'),
            'direccion' => Yii::t('app', 'Direccion'),
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'receptor_id' => Yii::t('app', 'Receptor ID'),
        ];
    }

    /**
     * Gets query for [[Provincia]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvincia()
    {
        return $this->hasOne(Provincias::className(), ['id' => 'provincia_id']);
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id']);
    }

    /**
     * Gets query for [[Receptor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceptor()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'receptor_id']);
    }
}
