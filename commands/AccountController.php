<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Usuarios;
use DateInterval;
use DateTime;
use yii\console\Controller;
use yii\console\ExitCode;

class AccountController extends Controller
{
    /**
     * Elimina las cuentas no hayan sido confirmadas si han pasado
     * 15 días desde su creación.
     */
    public function actionDeleteNotConfirmedAccounts()
    {
        $usuarios = Usuarios::find()
            ->where(['not', ['confirm_token' => null]])
            ->all();

        foreach ($usuarios as $usuario) {
            $created_at = (new DateTime($usuario->created_at))->add(new DateInterval('P15D'))->format('Y-m-d H:i:s');
            $hoy = (new DateTime())->format('Y-m-d H:i:s');
            if ($created_at < $hoy) {
                $usuario->delete();
            }
        }

        return ExitCode::OK;
    }

    /**
     * Elimina las cuentas que hayan sido eliminadas y no se hayan
     * recuperado si han pasado 30 días desde su creación.
     */
    public function actionDeleteDeletedAccounts()
    {
        $usuarios = Usuarios::find()
            ->where(['not', ['deleted_at' => null]])
            ->all();

        foreach ($usuarios as $usuario) {
            $deleted_at = (new DateTime($usuario->deleted_at))->add(new DateInterval('P30D'))->format('Y-m-d H:i:s');
            $hoy = (new DateTime())->format('Y-m-d H:i:s');
            if ($deleted_at < $hoy) {
                $usuario->delete();
            }
        }

        return ExitCode::OK;
    }
}
