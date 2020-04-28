<?php

use yii\bootstrap4\Html;

?>

<div class="tab-pane fade" id="videoclips" role="tabpanel" aria-labelledby="videoclips-tab">
    <?php if ($model->id == Yii::$app->user->id) : ?>
        <button class="action-btn outline-transparent mb-4" data-toggle="modal" data-target="#videoclip-modal">
            <div class="d-flex">
                <i class="far fa-plus-square"></i>
                <h2 class="ml-3"><?= Yii::t('app', 'Add') ?></h2>
            </div>
        </button>
        <div class="modal fade" id="videoclip-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <form id="add-videoclip-form">
                            <div class="form-group">
                                <label for="link">Youtube link</label>
                                <input class="form-control" type="text" name="link" id="link" placeholder="https://www.youtube.com/watch?v=KHAgoT4FZbc">
                                <div class="invalid-feedback invalid-videoclip"><?= Yii::t('app', 'EmptyField') ?></div>
                            </div>
                            <button class="btn main-yellow add-videoclip-btn" type="submit"><?= Yii::t('app', 'Save') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (count($videoclips) > 0) : ?>
        <div class="row row-videoclips">
            <?php foreach ($videoclips as $videoclip) : ?>
                <div id="video-<?= $videoclip->id ?>" class="col-12 col-lg-6 mb-4 fall-animation">
                    <?php if ($model->id == Yii::$app->user->id) : ?>
                        <button data-id="<?= $videoclip->id ?>" class="action-btn remove-videoclip-btn outline-transparent mb-4"><i class="fas fa-trash"></i></button>
                    <?php endif; ?>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="<?= $videoclip->link ?>" allowfullscreen></iframe>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="row mt-5 justify-content-center text-center mx-0 videoclip-warning">
            <div class="col-12">
                <h2><?= Yii::t('app', 'NoVideoclips') ?></h2>
            </div>
            <div class="col-10 col-lg-4 mt-4">
                <?= Html::img('@web/img/undraw_video_influencer_9oyy.png', ['class' => 'img-fluid', 'alt' => 'girl-music']) ?>
            </div>
        </div>
    <?php endif; ?>
</div>