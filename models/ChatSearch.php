<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ChatSearch represents the model behind the search form of `app\models\Chat`.
 */
class ChatSearch extends Chat
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'estado_id'], 'integer'],
            [['mensaje', 'created_at', 'receptor.login', 'emisor.login'], 'safe'],
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
        return array_merge(parent::attributes(), ['receptor.login'], ['emisor.login']);
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
        $query = Chat::find()
            ->joinWith('receptor r')
            ->joinWith('emisor e');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['emisor.login'] = [
            'asc' => ['r.login' => SORT_ASC],
            'desc' => ['r.login' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['receptor.login'] = [
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
            'emisor_id' => $this->emisor_id,
            'receptor_id' => $this->receptor_id,
            'estado_id' => $this->estado_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'mensaje', $this->mensaje]);

        $query->andFilterWhere([
            'ilike', 'r.login', $this->getAttribute('receptor.login'),
        ]);

        $query->andFilterWhere([
            'ilike', 'e.login', $this->getAttribute('emisor.login'),
        ]);

        return $dataProvider;
    }
}
