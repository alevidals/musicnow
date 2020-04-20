<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Seguidores;

/**
 * SeguidoresSearch represents the model behind the search form of `app\models\Seguidores`.
 */
class SeguidoresSearch extends Seguidores
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['seguidor.login', 'seguido.login'], 'safe'],
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
        return array_merge(parent::attributes(), ['seguidor.login'], ['seguido.login']);
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
        $query = Seguidores::find()
            ->joinWith('seguidor segr')
            ->joinWith('seguido seg');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['seguidor.login'] = [
            'asc' => ['segr.login' => SORT_ASC],
            'desc' => ['segr.login' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['seguido.login'] = [
            'asc' => ['seg.login' => SORT_ASC],
            'desc' => ['seg.login' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ilike', 'segr.login', $this->getAttribute('seguidor.login')
        ]);

        $query->andFilterWhere([
            'ilike', 'seg.login', $this->getAttribute('seguido.login')
        ]);

        return $dataProvider;
    }
}
