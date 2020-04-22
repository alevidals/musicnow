<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Likes;

/**
 * LikesSearch represents the model behind the search form of `app\models\Likes`.
 */
class LikesSearch extends Likes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario.login', 'cancion.titulo'], 'safe'],
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
        return array_merge(parent::attributes(), ['usuario.login'], ['cancion.titulo']);
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
        $query = Likes::find()
            ->joinWith('usuario u')
            ->joinWith('cancion c');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $dataProvider->sort->attributes['usuario.login'] = [
            'asc' => ['u.login' => SORT_ASC],
            'desc' => ['u.login' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['cancion.titulo'] = [
            'asc' => ['c.titulo' => SORT_ASC],
            'desc' => ['c.titulo' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ilike', 'u.login', $this->getAttribute('usuario.login')
        ]);

        $query->andFilterWhere([
            'ilike', 'c.titulo', $this->getAttribute('cancion.titulo')
        ]);

        return $dataProvider;
    }
}
