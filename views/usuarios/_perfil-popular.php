<?php

use yii\bootstrap4\Html;

?>

<div class="tab-pane fade show" id="popular" role="tabpanel" aria-labelledby="popular-tab">
    <h3 class="mb-3 text-center"><?= Yii::t('app', 'MostListened') ?></h3>
    <section class="row">
        <?php foreach ($canciones->limit(3)->orderBy(['reproducciones' => SORT_DESC])->all() as $cancion) : ?>
            <article class="col-12 col-md-6 col-lg-4 col-xl" itemscope itemtype="https://schema.org/MusicRecording">
                <div class="song-container">
                    <div class="box-3">
                        <?= Html::img($cancion->url_portada, ['class' => 'img-fluid', 'alt' => 'portada', 'itemprop'=> 'image'])?>
                        <div class="share-buttons">
                            <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                            <button id="outerlike-<?= $cancion->id ?>" class="action-btn outline-transparent like-btn"><i class="<?= in_array($cancion->id, $likes) ? 'fas' : 'far' ?> fa-heart red-hearth"></i></button>
                        </div>
                        <div class="layer"></div>
                    </div>
                </div>
                <h5 class="text-center my-3 text-truncate" itemprop="name"><?= Html::encode($cancion->titulo) ?></h5>
            </article>
        <?php endforeach; ?>
    </section>
    <h3 class="mb-3 text-center"><?= Yii::t('app', 'MostLikes') ?></h3>
    <section class="row">
        <?php foreach ($canciones->limit(3)->joinWith('likes l')->groupBy('canciones.id')->orderBy(['COUNT(l.usuario_id)' => SORT_DESC])->all() as $cancion) : ?>
            <article class="col-12 col-md-6 col-lg-4 col-xl" itemscope itemtype="https://schema.org/MusicRecording">
                <div class="song-container">
                    <div class="box-3">
                        <?= Html::img($cancion->url_portada, ['class' => 'img-fluid', 'alt' => 'portada', 'itemprop'=> 'image'])?>
                        <div class="share-buttons">
                            <button id="play-<?= $cancion->id ?>" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                            <button id="outerlike-<?= $cancion->id ?>" class="action-btn outline-transparent like-btn"><i class="<?= in_array($cancion->id, $likes) ? 'fas' : 'far' ?> fa-heart red-hearth"></i></button>
                        </div>
                        <div class="layer"></div>
                    </div>
                </div>
                <h5 class="text-center my-3 text-truncate" itemprop="name"><?= Html::encode($cancion->titulo) ?></h5>
            </article>
        <?php endforeach; ?>
    </section>
</div>