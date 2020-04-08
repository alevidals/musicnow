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
            [['bloqueador_id', 'bloqueado_id'], 'integer'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Bloqueados::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'bloqueador_id' => $this->bloqueador_id,
            'bloqueado_id' => $this->bloqueado_id,
        ]);

        return $dataProvider;
    }
}
