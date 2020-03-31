<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comentarios".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $cancion_id
 * @property string $comentario
 *
 * @property Canciones $cancion
 * @property Usuarios $usuario
 */
class Comentarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comentarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'cancion_id', 'comentario'], 'required'],
            [['usuario_id', 'cancion_id'], 'default', 'value' => null],
            [['usuario_id', 'cancion_id'], 'integer'],
            [['comentario'], 'string', 'max' => 255],
            [['cancion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Canciones::className(), 'targetAttribute' => ['cancion_id' => 'id']],
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
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'cancion_id' => Yii::t('app', 'Cancion ID'),
            'comentario' => Yii::t('app', 'Comentario'),
        ];
    }

    /**
     * Gets query for [[Cancion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCancion()
    {
        return $this->hasOne(Canciones::className(), ['id' => 'cancion_id']);
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
}
