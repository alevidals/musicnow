<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "likes".
 *
 * @property int $usuario_id
 * @property int $cancion_id
 *
 * @property Canciones $cancion
 * @property Usuarios $usuario
 */
class Likes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'likes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'cancion_id'], 'required'],
            [['usuario_id', 'cancion_id'], 'default', 'value' => null],
            [['usuario_id', 'cancion_id'], 'integer'],
            [['usuario_id', 'cancion_id'], 'unique', 'targetAttribute' => ['usuario_id', 'cancion_id']],
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
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'cancion_id' => Yii::t('app', 'Cancion ID'),
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
