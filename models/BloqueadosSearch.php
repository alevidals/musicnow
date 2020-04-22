<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bloqueados;

/**
 * BloqueadosSearch represents the model behind the search form of `app\models\Bloqueados`.
 */
class BloqueadosSearch extends Bloqueados
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bloqueador.login', 'bloqueado.login'], 'safe'],
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
        return array_merge(parent::attributes(), ['bloqueador.login'], ['bloqueado.login']);
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
        $query = Bloqueados::find()
            ->joinWith('bloqueador br')
            ->joinWith('bloqueado b');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $dataProvider->sort->attributes['bloqueador.login'] = [
            'asc' => ['br.login' => SORT_ASC],
            'desc' => ['br.login' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['bloqueado.login'] = [
            'asc' => ['b.login' => SORT_ASC],
            'desc' => ['b.login' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ilike', 'b.login', $this->getAttribute('bloqueado.login')
        ]);

        $query->andFilterWhere([
            'ilike', 'br.login', $this->getAttribute('bloqueador.login')
        ]);

        return $dataProvider;
    }
}
