<?php

/**
 * Ejemplo de prueba funcional combinada con fixtures.
 */
use app\models\Usuarios;
use tests\unit\fixtures\UsuariosFixture;

class UsuariosCest
{
    public function _fixtures()
    {
        return [
            [
                'class' => UsuariosFixture::class,
            ],
        ];
    }

    public function hayUsuarios(FunctionalTester $I)
    {
        $I->assertNotEquals(0, Usuarios::find()->count());
    }
}
