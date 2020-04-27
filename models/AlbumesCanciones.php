<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "albumes_canciones".
 *
 * @property int $album_id
 * @property int $canciones_id
 *
 * @property Albumes $album
 * @property Canciones $canciones
 */
class AlbumesCanciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'albumes_canciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['album_id', 'canciones_id'], 'required'],
            [['album_id', 'canciones_id'], 'default', 'value' => null],
            [['album_id', 'canciones_id'], 'integer'],
            [['album_id', 'canciones_id'], 'unique', 'targetAttribute' => ['album_id', 'canciones_id']],
            [['album_id'], 'exist', 'skipOnError' => true, 'targetClass' => Albumes::className(), 'targetAttribute' => ['album_id' => 'id']],
            [['canciones_id'], 'exist', 'skipOnError' => true, 'targetClass' => Canciones::className(), 'targetAttribute' => ['canciones_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'album_id' => Yii::t('app', 'Album ID'),
            'canciones_id' => Yii::t('app', 'Canciones ID'),
        ];
    }

    /**
     * Gets query for [[Album]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlbum()
    {
        return $this->hasOne(Albumes::className(), ['id' => 'album_id']);
    }

    /**
     * Gets query for [[Canciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCanciones()
    {
        return $this->hasOne(Canciones::className(), ['id' => 'canciones_id']);
    }
}
