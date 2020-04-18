<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "canciones_playlist".
 *
 * @property int $playlist_id
 * @property int $cancion_id
 *
 * @property Canciones $cancion
 * @property Playlists $playlist
 */
class CancionesPlaylist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'canciones_playlist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['playlist_id', 'cancion_id'], 'required'],
            [['playlist_id', 'cancion_id'], 'default', 'value' => null],
            [['playlist_id', 'cancion_id'], 'integer'],
            [['playlist_id', 'cancion_id'], 'unique', 'targetAttribute' => ['playlist_id', 'cancion_id']],
            [['cancion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Canciones::className(), 'targetAttribute' => ['cancion_id' => 'id']],
            [['playlist_id'], 'exist', 'skipOnError' => true, 'targetClass' => Playlists::className(), 'targetAttribute' => ['playlist_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'playlist_id' => Yii::t('app', 'Playlist ID'),
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
     * Gets query for [[Playlist]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlaylist()
    {
        return $this->hasOne(Playlists::className(), ['id' => 'playlist_id']);
    }

}
