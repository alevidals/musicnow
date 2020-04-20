<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Videoclips;

/**
 * VideoclipsSearch represents the model behind the search form of `app\models\Videoclips`.
 */
class VideoclipsSearch extends Videoclips
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['link', 'usuario.login'], 'safe'],
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
        return array_merge(parent::attributes(), ['usuario.login']);
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
        $query = Videoclips::find()
            ->joinWith('usuario u');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

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
        
        $query->andFilterWhere(['ilike', 'link', $this->link]);

        $query->andFilterWhere([
            'ilike', 'u.login', $this->getAttribute('usuario.login')
        ]);

        return $dataProvider;
    }
}
