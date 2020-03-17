<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Albumes;

/**
 * AlbumesSearch represents the model behind the search form of `app\models\Albumes`.
 */
class AlbumesSearch extends Albumes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'usuario_id'], 'integer'],
            [['titulo', 'created_at', 'usuario.nombre'], 'safe'],
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
        return array_merge(parent::attributes(), ['usuario.nombre']);
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
        $query = Albumes::find()
            ->joinWith('usuario u');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['usuario.nombre'] = [
            'asc' => ['u.nombre' => SORT_ASC],
            'desc' => ['u.nombre' => SORT_DESC],
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
            'anyo' => $this->anyo,
            'created_at' => $this->created_at,
            'usuario_id' => $this->usuario_id,
        ]);

        $query->andFilterWhere(['ilike', 'titulo', $this->titulo]);

        $query->andFilterWhere([
            'ilike', 'u.nombre', $this->getAttribute('usuario.nombre')
        ]);

        return $dataProvider;
    }
}
