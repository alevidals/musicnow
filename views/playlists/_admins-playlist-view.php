<?php

use yii\grid\GridView;

?>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="mt-3"></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => $columns,
        'tableOptions' => [
            'class' => 'table admin-table ',
        ],
    ]); ?>
