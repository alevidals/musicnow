<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Chats');
?>
<div class="chat-chat">

    <div class="row">
        <div class="w-100 mb-5"></div>
        <?php foreach ($seguidos as $seguido) : ?>
            <div id="<?= $seguido->id ?>" class="col-12 mb-5">
                <h4 class="d-inline-block"><?= Html::encode($seguido->login) ?></h4>
                <span class="status badge badge-success d-inline-block"><?= $seguido->getEstado()->one()->estado ?></span>
                <span class="badge badge-warning" id="messages-number-<?= $seguido->id ?>"></span>
                <button class="btn main-yellow start-chat d-block" data-receptorid="<?= $seguido->id ?>" data-toggle="modal" data-target="#chat-<?= $seguido->id ?>">Chat</button>
                <div class="modal fade" id="chat-<?= $seguido->id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <?= Html::img($seguido->url_image, ['class' => 'user-search-img', 'width' => '40px', 'alt' => 'logo']) ?>
                                <h5 class="modal-title my-auto ml-3"><?= Html::encode($seguido->login) ?></h5>
                                <span class="status badge badge-success my-auto ml-3"><?= $seguido->getEstado()->one()->estado ?></span>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-white"><i class="fas fa-times"></i></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="user-chat-<?= $seguido->id ?>" class="user-chat">
                                    <div class="chat-history custom-overflow pr-2 pt-2" data-receptorid="<?= $seguido->id ?>" id="chat-history-<?= $seguido->id ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="chat-message<?= $seguido->id ?>" id="chat-message-<?= $seguido->id ?>" class="form-control chat-input">
                                    </div>
                                    <div class="form-group">
                                        <button type="button" id="<?= $seguido->id ?>" class="btn main-yellow send-chat">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

    </div>