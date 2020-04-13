<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property int $emisor_id
 * @property int $receptor_id
 * @property string $mensaje
 * @property int $estado_id
 * @property string $created_at
 *
 * @property Estados $estado
 * @property Usuarios $emisor
 * @property Usuarios $receptor
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['emisor_id', 'receptor_id', 'mensaje'], 'required'],
            [['emisor_id', 'receptor_id', 'estado_id'], 'default', 'value' => null],
            [['emisor_id', 'receptor_id', 'estado_id'], 'integer'],
            [['mensaje'], 'string'],
            [['created_at'], 'safe'],
            [['estado_id'], 'default', 'value' => 3],
            [['estado_id'], 'exist', 'skipOnError' => true, 'targetClass' => Estados::className(), 'targetAttribute' => ['estado_id' => 'id']],
            [['emisor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['emisor_id' => 'id']],
            [['receptor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['receptor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'emisor_id' => Yii::t('app', 'Emisor ID'),
            'receptor_id' => Yii::t('app', 'Receptor ID'),
            'mensaje' => Yii::t('app', 'Mensaje'),
            'estado_id' => Yii::t('app', 'Estado ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
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

    /**
     * Gets query for [[Emisor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmisor()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'emisor_id']);
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
