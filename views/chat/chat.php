<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$urlStatus = Url::to(['usuarios/estados', 'id' => $usuario->id]);
$urlSendChat = Url::to(['chat/send-chat']);
$urlGetChatHistory = Url::to(['chat/get-chat']);
$urlGetNoReadMessages = Url::to(['usuarios/get-no-read-messages']);

$js = <<<EOT

    var interval;

    getStatusFromUsers();

    setInterval(function(){
        getStatusFromUsers();
    }, 5000);

    $('.modal').on('show.bs.modal', function (e) {
        interval = setInterval(function(){
            updateChatHistory();
        }, 5000);
    });

    $('.modal').on('hide.bs.modal', function (e) {
        clearInterval(interval);
    });

    function getStatusFromUsers() {
        $.ajax({
            method: 'GET',
            url: '$urlStatus',
            success: function (data) {
                data.forEach(element => {
                    var id = element.id;
                    if (element.estado_id == 'online') {
                        $('#' + id + ' .status').removeClass('badge-danger');
                        $('#' + id + ' .status').addClass('badge-success');
                    } else {
                        $('#' + id + ' .status').removeClass('badge-success');
                        $('#' + id + ' .status').addClass('badge-danger');
                    }
                    $('#' + id + ' .status').html(element.estado_id);
                    getNoReadMessages(id);
                });
            }
        });
    }

    function getNoReadMessages(receptor_id) {
        $.ajax({
            method: 'GET',
            url: '$urlGetNoReadMessages&receptor_id=' + receptor_id,
            success: function (data) {
                if (data != 0) {
                    $('#messages-number-' + receptor_id).html(data);
                }
            }
        });
    }

    function getMessagesFromChat(receptor_id) {
        $.ajax({
            method: 'POST',
            url: '$urlGetChatHistory&receptor_id=' + receptor_id,
            success: function (data) {
                $('#chat-history-' + receptor_id).html('');
                data.historial.forEach(element => {
                    if (element.emisor_id != receptor_id) {
                        $('#chat-history-' + receptor_id).append(`
                            <p class="text-right"><span class="message">\${element.mensaje}<small class="pl-2">\${element.created_at}<i class="fas fa-check-double pl-2 read-tick"></i></small></span></p>
                        `);
                        if (element.estado_id == 4) {
                            $('.read-tick').addClass('text-primary');
                        }
                    } else {
                        $('#chat-history-' + receptor_id).append(`
                            <p><span class="message">\${element.mensaje}<small class="pl-2">\${element.created_at}</small></span></p>
                        `);
                    }
                });
            }
        });
    }

    function updateChatHistory() {
        $('.chat-history').each(function() {
            var receptor_id = $(this).data('receptorid');
            getMessagesFromChat(receptor_id);
        });
    }

    $('.send-chat').on('click', function ev(e) {
        var receptor_id = $(this).attr('id');
        var mensaje = $('#chat-message-' + receptor_id).val().trim();
        $.ajax({
            method: 'POST',
            url: '$urlSendChat',
            data: {
                receptor_id: receptor_id,
                mensaje: mensaje
            },
            success: function(data) {
                $('#chat-message-' + receptor_id).val('');
                $('#chat-history-' + receptor_id).html('')
                data.forEach(element => {
                    if (element.emisor_id != receptor_id) {
                        $('#chat-history-' + receptor_id).append(`
                            <p class="text-right"><span class="message">\${element.mensaje}<small class="pl-2">\${element.created_at}<i class="fas fa-check-double pl-2 read-tick"></i></small></span></p>
                        `);
                        if (element.estado_id == 4) {
                            $('.read-tick').addClass('text-primary');
                        }
                    } else {
                        $('#chat-history-' + receptor_id).append(`
                            <p><span class="message">\${element.mensaje}<small class="pl-2">\${element.created_at}</small></span></p>
                        `);
                    }
                });
                $('#chat-history-' + receptor_id).scrollTop($('#chat-history-' + receptor_id)[0].scrollHeight);
            }
        });
    });

    $('textarea').on('keydown', function ev(e) {
        var key = (event.keyCode ? event.keyCode : event.which);
        if (key == 13) {
            var receptor_id = $(this).attr('id').split('-')[2];
            var mensaje = $('#chat-message-' + receptor_id).val().trim();
            $.ajax({
                method: 'POST',
                url: '$urlSendChat',
                data: {
                    receptor_id: receptor_id,
                    mensaje: mensaje
                },
                success: function(data) {
                    $('#chat-message-' + receptor_id).val('');
                    $('#chat-history-' + receptor_id).html('')
                    data.forEach(element => {
                        if (element.emisor_id != receptor_id) {
                            $('#chat-history-' + receptor_id).append(`
                                <p class="text-right"><span class="message">\${element.mensaje}<small class="pl-2">\${element.created_at}</small></span></p>
                            `);
                        } else {
                            $('#chat-history-' + receptor_id).append(`
                                <p><span class="message">\${element.mensaje}<small class="pl-2">\${element.created_at}</small></span></p>
                            `);
                        }
                    });
                    $('#chat-history-' + receptor_id).scrollTop($('#chat-history-' + receptor_id)[0].scrollHeight);
                }
            });
        }
    });

    $('.start-chat').on('click', function ev(e) {
        var receptor_id = $(this).data('receptorid');
        getMessagesFromChat(receptor_id);
    });
EOT;

$this->registerJS($js);

$this->title = Yii::t('app', 'Chats');
?>
<div class="chat-chat">

    <div class="row">
        <div class="w-100 mb-5"></div>
        <?php foreach ($seguidos as $seguido) : ?>
            <div id="<?= $seguido->id ?>" class="col-12 mb-5">
                <h4 class="d-inline-block"><?= $seguido->login ?></h4>
                <span class="status badge badge-success d-inline-block"><?= $seguido->getEstado()->one()->estado ?></span>
                <span class="badge badge-warning" id="messages-number-<?= $seguido->id ?>"></span>
                <button class="btn main-yellow start-chat d-block" data-receptorid="<?= $seguido->id ?>" data-toggle="modal" data-target="#chat-<?= $seguido->id ?>">Chat</button>
                <div class="modal fade" id="chat-<?= $seguido->id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <?= Html::img($seguido->url_image, ['class' => 'user-search-img', 'width' => '40px', 'alt' => 'logo']) ?>
                                <h5 class="modal-title my-auto ml-3"><?= $seguido->login ?></h5>
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
                                        <textarea name="chat-message-<?= $seguido->id ?>" id="chat-message-<?= $seguido->id ?>" class="form-control"></textarea>
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