<?php

namespace app\services;

use DateTime;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Yii;

class Utility
{
    const PERFIL = 'img_perfil';
    const BANNER = 'img_banner';
    const PORTADA = 'img_portada';

    /**
     * Instancia y devuelve el objeto con el que se pueden realizar
     * operaciones sobre el servidor de almacenamiento Firebase
     *
     * @return object
     */
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

    /**
     * Sube la imagen al servidor de almacenamiento Firebase, sube
     * una imagen de perfil, una imagen de portada o un banner
     * dependiendo del tipo especificado.
     *
     * @param string $img la imagen a subir
     * @param int $userId el id del usuario que está subiendo la imagen
     * @param string $type el tipo de imagen a subir
     * @return void
     */
    public static function uploadImageFirebase($img, $userId, $type)
    {
        $filename = str_replace(' ', '', $img->basename) . (new DateTime())->format('Y-m-d_H:i:s') . '.' . $img->extension;
        $origen = Yii::getAlias('@uploads/' . $filename);
        $img->saveAs($origen);
        $res = '';

        switch ($type) {
            case self::PERFIL:
                $name = 'images/perfil/' . $userId . '/perfil.png';
                $url_name = getenv('url_prefix') . 'images%2Fperfil%2F' . $userId . '%2Fperfil.png' . getenv('url_suffix');
                $width = 150;
                $height = 150;
                $res = $url_name;
            break;
            case self::PORTADA:
                $name = 'images/portada/' . $userId . '/' . $filename;
                $url_name = getenv('url_prefix') . 'images%2Fportada%2F' . $userId . '%2F' . $filename . getenv('url_suffix');
                $width = 500;
                $height = 500;
                $res = [
                    'url' => $url_name,
                    'image_name' => $filename,
                ];
            break;
            case self::BANNER:
                $name = 'images/perfil/' . $userId . '/banner.png';
                $url_name = getenv('url_prefix') . 'images%2Fperfil%2F' . $userId . '%2Fbanner.png' . getenv('url_suffix');
                $width = 1110;
                $height = 201;
                $res = $url_name;
            break;
            default:
                break;
        }

        \yii\imagine\Image::resize($origen, $width, $height)->save($origen);

        $factory = self::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(getenv('bucket'));
        $bucket->upload(file_get_contents($origen), [
            'name' => $name,
        ]);
        unlink($origen);
        return $res;
    }

    /**
     * Sube la canciones especificada al servidor de almacenamiento
     * Firebase dentro del directorio reservado a cada usuario
     *
     * @param string $file nombre de la canción a subir
     * @param int $userId id del usuario que sube el archivo
     * @return array devuelve un array con el nombre de la canción
     * y con la url de la canción
     */
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
            'song_name' => $filename,
        ];
    }

    /**
     * Eliminar el fichero especificado del servidor de almacenamiento
     * Firebase
     *
     * @param string $name el nombre del fichero a borrar
     * @return void
     */
    public static function deleteFileFirebase($name)
    {
        $factory = self::getFactory();
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket(getenv('bucket'));
        $bucket->object($name)->delete();
    }
}
