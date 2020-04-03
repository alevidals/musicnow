<?php

namespace app\services;

// use Imagine\Gd\Imagine;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Yii;

class Utility
{
    const URL_PREFIX = 'https://firebasestorage.googleapis.com/v0/b/song-test-103af.appspot.com/o/';
    const URL_SUFFIX = '?alt=media';
    const DATABASE_URI = 'https://song-test-103af.firebaseio.com';
    const BUCKET = 'song-test-103af.appspot.com';

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
                        stopOthersOnPlay: true
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

    protected static function getFactory()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('/home/ale/Escritorio/jeje.json');
        return (new Factory())
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://song-test-103af.firebaseio.com');
    }

    public static function uploadImageFirebase($img, $userId, $perfilImg = false)
    {
        $filename = str_replace(' ', '', $img->name);
        $origen = Yii::getAlias('@uploads/' . $filename);
        $img->saveAs($origen);

        if ($perfilImg) {
            $name = 'images/perfil/' . $userId . '/perfil.png';
            $url_name = self::URL_PREFIX . 'images%2Fperfil%2F' . $userId . '%2Fperfil.png' . self::URL_SUFFIX;
            $size = 150;
        } else {
            $name = 'images/portada/' . $userId . '/' . $filename;
            $url_name = self::URL_PREFIX . 'images%2Fportada%2F' . $userId . '%2F' . $filename . self::URL_SUFFIX;
            $size = 500;
        }

        \yii\imagine\Image::resize($origen, $size, $size)->save($origen);

        $factory = self::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(self::BUCKET);
        $bucket->upload(file_get_contents($origen), [
            'name' => $name,
        ]);
        unlink($origen);
        return $url_name;
    }

    public static function uploadFileFirebase($file, $userId)
    {
        $filename = str_replace(' ', '', $file->name);
        $origen = Yii::getAlias('@uploads/' . $filename);
        $file->saveAs($origen);

        $factory = self::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(self::BUCKET);
        $bucket->upload(file_get_contents($origen), [
            'name' => 'canciones/' . $userId . '/' . $filename,
        ]);
        unlink($origen);
        return self::URL_PREFIX . 'canciones%2F' . $userId . '%2F' . $filename . self::URL_SUFFIX;
    }

    public static function deleteFileFirebase($name)
    {
        $factory = self::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(self::BUCKET);
        $bucket->object($name)->delete();
    }
}
