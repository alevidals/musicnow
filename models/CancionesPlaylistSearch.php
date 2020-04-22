<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CancionesPlaylist;

/**
 * CancionesPlaylistSearch represents the model behind the search form of `app\models\CancionesPlaylist`.
 */
class CancionesPlaylistSearch extends CancionesPlaylist
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['playlist.titulo', 'cancion.titulo'], 'safe'],
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
        return array_merge(parent::attributes(), ['playlist.titulo'], ['cancion.titulo']);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CancionesPlaylist::find()
            ->joinWith('cancion c')
            ->joinWith('playlist p');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $dataProvider->sort->attributes['cancion.titulo'] = [
            'asc' => ['c.titulo' => SORT_ASC],
            'desc' => ['c.titulo' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['playlist.titulo'] = [
            'asc' => ['p.titulo' => SORT_ASC],
            'desc' => ['p.titulo' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ilike', 'c.titulo', $this->getAttribute('cancion.titulo')
        ]);

        $query->andFilterWhere([
            'ilike', 'p.titulo', $this->getAttribute('playlist.titulo')
        ]);

        return $dataProvider;
    }
}
