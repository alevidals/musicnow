<?php

namespace app\services;

use DateTime;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Yii;

class Utility
{
    const GET_COOKIE = <<<EOT
        function getCookie(name) {
            var dc = document.cookie;
            var prefix = name + "=";
            var begin = dc.indexOf("; " + prefix);
            if (begin == -1) {
                begin = dc.indexOf(prefix);
                if (begin != 0) return null;
            }
            else
            {
                begin += 2;
                var end = document.cookie.indexOf(";", begin);
                if (end == -1) {
                end = dc.length;
                }
            }
            return decodeURI(dc.substring(begin + prefix.length, end));
        }
    EOT;

    const PLAY_SONG = <<<EOT
        var firstTime = true;
        $('.play-btn').on('click', function ev(e) {
            var cancion_id = $(this).attr('id').split('-')[1];
            $.ajax({
                method: 'GET',
                url: '/index.php?r=canciones%2Fget-song-data',
                data: {
                    cancion_id: cancion_id
                },
                success: function (data) {
                    if ($('.loading').length) {
                        $('.loading').remove();
                        $('.play-pause-btn').remove();
                        $('.controls').remove();
                        $('.volume').remove();
                        $('.download').remove();
                    }
                    GreenAudioPlayer.init({
                        selector: '.player',
                        stopOthersOnPlay: true,
                        showDownloadButton: true,
                    });
                    $('.info-song img').attr('src', data.url_portada);
                    $('.player audio source').attr('src', data.url_cancion);
                    $('.artist-info p').html(data.titulo);
                    $('.artist-info small').html(data.album);
                    $('.full-player').css('display', 'flex');
                    if (firstTime) {
                        $('.full-player').effect('slide','slow');
                        firstTime = false;
                    }
                    $('.player').css('display', 'flex');
                }
            });
        });
    EOT;

    const LIKE_COMMENT_PROFILE = <<<EOT
        $('.like-btn').on('click', function ev(e) {
            var cancion_id = $(this).attr('id').split('-')[1];
            $.ajax({
                'method': 'POST',
                url: '/index.php?r=likes%2Flike&cancion_id=' + cancion_id,
                success: function (data) {
                    if (data.class == 'far') {
                        $('#outerlike-' + cancion_id + ' i').removeClass('fas');
                        $('#outerlike-' + cancion_id + ' i').addClass('far');
                        $('#like-' + cancion_id + ' i').removeClass('fas');
                        $('#like-' + cancion_id + ' i').addClass('far');
                    } else {
                        $('#outerlike-' + cancion_id + ' i').removeClass('far');
                        $('#outerlike-' + cancion_id + ' i').addClass('fas');
                        $('#like-' + cancion_id + ' i').removeClass('far');
                        $('#like-' + cancion_id + ' i').addClass('fas');
                    }
                    $('.like-btn ~ p span').html(data.likes);
                }
            });
        });

        $('.cancion').on('click', function ev(e) {
            var cancion_id = $(this).data('target').split('-')[1];
            $('#like-' + cancion_id + ' i').removeClass('fas far');
            $.ajax({
                'method': 'POST',
                url: '/index.php?r=likes%2Fget-data&cancion_id=' + cancion_id,
                success: function (data) {
                    $('#like-' + cancion_id + ' i').addClass(data.class);
                    $('.like-btn ~ p span').html(data.likes);
                }
            });

            $.ajax({
                method: 'GET',
                url: '/index.php?r=canciones%2Fcomentarios',
                data: {
                    cancion_id: cancion_id
                },
                success: function (data) {
                    var comentarios = Object.entries(data);
                    $('.row-comments').empty();
                    comentarios.forEach(element => {
                        $('.row-comments').append(`
                            <div class="col-12 mt-3">
                                <div class="row">
                                    <a href="/index.php?r=usuarios%2Fperfil&id=\${element[1].id}">
                                        <img class="user-search-img" src="\${element[1].url_image}" alt="perfil" width="50px" height="50px">
                                    </a>
                                    <div class="col">
                                        <a href="/index.php?r=usuarios%2Fperfil&id=\${element[1].id}">\${element[1].login}</a>
                                        <small class="ml-1 comment-time">\${element[1].created_at}</small>
                                        <p class="m-0">\${element[1].comentario}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
            });

        });

        $('.comment-btn').on('click', function ev(e) {
            var cancion_id = $(this).attr('id').split('-')[1];
            var comentario = $('#text-area-comment-' + cancion_id).val();
            if (comentario.length > 255 || comentario.length == 0) {
                $('.invalid-feedback').show();
            } else {
                $('.invalid-feedback').hide();
                $.ajax({
                    'method': 'POST',
                    url: '/index.php?r=comentarios%2Fcomentar&cancion_id=' + cancion_id,
                    data: {
                        comentario: comentario,
                    },
                    success: function (data) {
                        $('.row-comments').prepend(`
                            <div class="col-12 mt-3">
                                <div class="row">
                                    <a href="/index.php?r=usuarios%2Fperfil&id=\${data.usuario_id}">
                                        <img class="user-search-img" src="\${data.url_image}" alt="perfil" width="50px" height="50px">
                                    </a>
                                    <div class="col">
                                        <a href="/index.php?r=usuarios%2Fperfil&id=\${data.usuario_id}">\${data.login}</a>
                                        <small class="ml-1 comment-time">\${data.created_at}</small>
                                        <p>\${data.comentario}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                        $('.text-area-comment').val('');
                    }
                });
            }
        });
    EOT;

    protected static function getFactory()
    {
        $config = '
        {
            "type": "' . getenv('type') . '",
            "project_id": "' . getenv('project_id') . '",
            "private_key_id": "' . getenv('private_key_id') . '",
            "private_key": "' . getenv('private_key') . '",
            "client_email": "' . getenv('client_email') . '",
            "client_id": "' . getenv('client_id') . '",
            "auth_uri": "' . getenv('auth_uri') . '",
            "token_uri": "' . getenv('token_uri') . '",
            "auth_provider_x509_cert_url": "' . getenv('auth_provider_x509_cert_url') . '",
            "client_x509_cert_url": "' . getenv('client_x509_cert_url') . '"
        }
        ';

        $serviceAccount = ServiceAccount::fromJson($config);
        return (new Factory())
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri(getenv('databaseUri'));
    }

    public static function uploadImageFirebase($img, $userId, $perfilImg = false)
    {
        $filename = str_replace(' ', '', $img->basename) . (new DateTime())->format('Y-m-d_H:i:s') . '.' . $img->extension;
        $origen = Yii::getAlias('@uploads/' . $filename);
        $img->saveAs($origen);

        if ($perfilImg) {
            $name = 'images/perfil/' . $userId . '/perfil.png';
            $url_name = getenv('url_prefix') . 'images%2Fperfil%2F' . $userId . '%2Fperfil.png' . getenv('url_suffix');
            $size = 150;
        } else {
            $name = 'images/portada/' . $userId . '/' . $filename;
            $url_name = getenv('url_prefix') . 'images%2Fportada%2F' . $userId . '%2F' . $filename . getenv('url_suffix');
            $size = 500;
        }

        \yii\imagine\Image::resize($origen, $size, $size)->save($origen);

        $factory = self::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(getenv('bucket'));
        $bucket->upload(file_get_contents($origen), [
            'name' => $name,
        ]);
        unlink($origen);
        if ($perfilImg) {
            return $url_name;
        } else {
            return [
                'url' => $url_name,
                'image_name' => $filename
            ];
        }
    }

    public static function uploadFileFirebase($file, $userId)
    {
        $filename = str_replace(' ', '', $file->basename) . (new DateTime())->format('Y-m-d_H:i:s') . '.' . $file->extension;
        $origen = Yii::getAlias('@uploads/' . $filename);
        $file->saveAs($origen);

        $factory = self::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(getenv('bucket'));
        $bucket->upload(file_get_contents($origen), [
            'name' => 'canciones/' . $userId . '/' . $filename,
        ]);
        unlink($origen);
        return [
            'url' => getenv('url_prefix') . 'canciones%2F' . $userId . '%2F' . $filename . getenv('url_suffix'),
            'song_name' => $filename
        ];
    }

    public static function deleteFileFirebase($name)
    {
        $factory = self::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(getenv('bucket'));
        $bucket->object($name)->delete();
    }
}
