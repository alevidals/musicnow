<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bloqueados".
 *
 * @property int $bloqueador_id
 * @property int $bloqueado_id
 *
 * @property Usuarios $bloqueador
 * @property Usuarios $bloqueado
 */
class Bloqueados extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bloqueados';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bloqueador_id', 'bloqueado_id'], 'required'],
            [['bloqueador_id', 'bloqueado_id'], 'default', 'value' => null],
            [['bloqueador_id', 'bloqueado_id'], 'integer'],
            [['bloqueador_id', 'bloqueado_id'], 'unique', 'targetAttribute' => ['bloqueador_id', 'bloqueado_id']],
            [['bloqueador_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['bloqueador_id' => 'id']],
            [['bloqueado_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['bloqueado_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bloqueador_id' => Yii::t('app', 'Bloqueador ID'),
            'bloqueado_id' => Yii::t('app', 'Bloqueado ID'),
        ];
    }

    /**
     * Gets query for [[Bloqueador]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloqueador()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'bloqueador_id']);
    }

    /**
     * Gets query for [[Bloqueado]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloqueado()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'bloqueado_id']);
    }
}
