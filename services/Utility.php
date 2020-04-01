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

    protected static function getFactory()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('/home/ale/Escritorio/jeje.json');
        return (new Factory())
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://song-test-103af.firebaseio.com');
    }

    public static function uploadImageFirebase($img, $userId, $perfilImg)
    {
        $filename = str_replace(' ', '', $img->name);
        $origen = Yii::getAlias('@uploads/' . $filename);
        $img->saveAs($origen);

        \yii\imagine\Image::resize($origen, 500, 500)->save($origen);

        if ($perfilImg) {
            $name = 'images/perfil/' . $userId . '/perfil.png';
            $url_name = Utility::URL_PREFIX . 'images%2Fperfil%2F' . $userId . '%2Fperfil.png' . Utility::URL_SUFFIX;
        } else {
            $name = 'images/' . $userId . '/' . $filename;
            $url_name = Utility::URL_PREFIX . 'images%2F' . $userId . '%2F' . $filename . Utility::URL_SUFFIX;
        }

        $factory = Utility::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(Utility::BUCKET);
        $bucket->upload(file_get_contents($origen), [
            'name' => $name,
        ]);
        unlink($origen);
        return $url_name;
    }
}
