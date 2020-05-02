<?php

use yii\bootstrap4\Html;

?>
<div class="tab-pane fade" id="albumes" role="tabpanel" aria-labelledby="albumes-tab">
    <section class="row">
        <?php if (count($albumes) > 0) : ?>
            <?php foreach ($albumes as $album) : ?>
                <article class="col-12 col-md-6 col-lg-4 col-xl-3" itemscope itemtype="https://schema.org/MusicAlbum">
                    <div class="song-container">
                        <div class="box-3">
                            <?= Html::img($album->url_portada, ['class' => 'img-fluid', 'alt' => 'portada', 'itemprop' => 'image'])?>
                            <div class="share-buttons">
                                <button id="<?= $album->id ?>" class="action-btn play-album-btn outline-transparent"><i class="fas fa-play"></i></button>
                                <?= Html::a(
                                    '<i class="fas fa-eye"></i>',
                                    ['albumes/view', 'id' => $album->id],
                                    ['class' => 'action-btn outline-transparent', 'rol' => 'button']
                                ) ?>
                            </div>
                            <div class="layer"></div>
                        </div>
                    </div>
                    <h5 class="text-center my-3" itemprop="name"><?= Html::encode($album->titulo) ?></h5>
                </article>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="row mt-5 justify-content-center text-center mx-0">
                <div class="col-12">
                    <h2><?= Yii::t('app', 'NoAlbums') ?></h2>
                </div>
                <div class="col-10 col-lg-4">
                    <?= Html::img('@web/img/undraw_no_data_qbuo.png', ['class' => 'img-fluid', 'alt' => 'girl-music']) ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>