<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ComentariosSearch represents the model behind the search form of `app\models\Comentarios`.
 */
class ComentariosSearch extends Comentarios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'usuario_id', 'cancion_id'], 'integer'],
            [['comentario', 'created_at', 'cancion.titulo', 'usuario.login'], 'safe'],
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
        return array_merge(parent::attributes(), ['cancion.titulo'], ['usuario.login']);
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
        $query = Comentarios::find()
            ->joinWith('cancion c')
            ->joinWith('usuario u');

        if (Yii::$app->user->identity->rol_id != 1) {
            $query->where(['comentarios.usuario_id' => Yii::$app->user->id]);
        }

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
            'comentarios.usuario_id' => $this->usuario_id,
            'cancion_id' => $this->cancion_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'comentario', $this->comentario]);

        $query->andFilterWhere([
            'ilike', 'c.titulo', $this->getAttribute('cancion.titulo'),
        ]);

        $query->andFilterWhere([
            'ilike', 'u.login', $this->getAttribute('usuario.login'),
        ]);

        Yii::debug($query->createCommand()->rawSql);

        return $dataProvider;
    }
}
