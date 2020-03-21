<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CancionesSearch represents the model behind the search form of `app\models\Canciones`.
 */
class CancionesSearch extends Canciones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'album_id', 'genero_id', 'usuario_id'], 'integer'],
            [['titulo', 'url_cancion', 'url_portada', 'duracion', 'created_at', 'song_name', 'image_name', 'album.titulo', 'genero.denominacion', 'usuario.login'], 'safe'],
            [['anyo'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['album.titulo'], ['genero.denominacion'], ['usuario.login']);
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Canciones::find()
            ->joinWith('album a')
            ->joinWith('genero g')
            ->joinWith('usuario u');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['album.titulo'] = [
            'asc' => ['a.titulo' => SORT_ASC],
            'desc' => ['a.titulo' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['genero.denominacion'] = [
            'asc' => ['g.denominacion' => SORT_ASC],
            'desc' => ['g.denominacion' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['usuario.login'] = [
            'asc' => ['u.login' => SORT_ASC],
            'desc' => ['u.login' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'album_id' => $this->album_id,
            'genero_id' => $this->genero_id,
            'anyo' => $this->anyo,
            'usuario_id' => $this->usuario_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'titulo', $this->titulo])
            ->andFilterWhere(['ilike', 'url_cancion', $this->url_cancion])
            ->andFilterWhere(['ilike', 'url_portada', $this->url_portada])
            ->andFilterWhere(['ilike', 'song_name', $this->song_name])
            ->andFilterWhere(['ilike', 'image_name', $this->image_name])
            ->andFilterWhere(['ilike', 'duracion', $this->duracion]);

        $query->andFilterWhere([
            'ilike', 'a.titulo', $this->getAttribute('album.titulo'),
        ]);

        $query->andFilterWhere([
            'ilike', 'g.denominacion', $this->getAttribute('genero.denominacion'),
        ]);

        $query->andFilterWhere([
            'ilike', 'u.login', $this->getAttribute('usuario.login'),
        ]);

        return $dataProvider;
    }
}
