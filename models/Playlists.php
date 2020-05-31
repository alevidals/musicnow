<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "playlists".
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $titulo
 *
 * @property CancionesPlaylist[] $cancionesPlaylists
 * @property Usuarios $usuario
 */
class Playlists extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'playlists';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'titulo'], 'required'],
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
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'titulo' => Yii::t('app', 'Titulo'),
        ];
    }

    /**
     * Gets query for [[CancionesPlaylists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCancionesPlaylists()
    {
        return $this->hasMany(CancionesPlaylist::className(), ['playlist_id' => 'id']);
    }

    /**
     * Devuelve un array con las canciones que pertenecen a dicha playlist
     *
     * @return void
     */
    public function getCanciones()
    {
        return $this->hasMany(Canciones::className(), ['id' => 'cancion_id'])->via('cancionesPlaylists');
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
