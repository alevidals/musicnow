<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Playlists;
use Yii;

/**
 * PlaylistsSearch represents the model behind the search form of `app\models\Playlists`.
 */
class PlaylistsSearch extends Playlists
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titulo', 'usuario.login'], 'safe'],
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
        $query = Playlists::find()
            ->joinWith('usuario u');

        if (Yii::$app->user->identity->rol != 1) {
            $query->where(['usuario_id' => Yii::$app->user->id]);
        }

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

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
        ]);

        $query->andFilterWhere(['ilike', 'titulo', $this->titulo]);

        $query->andFilterWhere([
            'ilike', 'u.login', $this->getAttribute('usuario.login')
        ]);

        return $dataProvider;
    }
}
