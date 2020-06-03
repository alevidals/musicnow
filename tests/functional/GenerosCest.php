<?php

use app\models\Usuarios;

class GenerosCest
{
    public function permisoAceptadoUsuarioNoAdmin(FunctionalTester $I)
    {
        $I->amLoggedInAs(Usuarios::findOne(1));
        $I->amOnRoute('generos/index');
        $I->dontSee('No tiene permitido ejecutar esta acción.');
    }

    public function permisoDenegadoUsuarioNoAdmin(FunctionalTester $I)
    {
        $I->amLoggedInAs(Usuarios::findOne(2));
        $I->amOnRoute('generos/index');
        $I->see('No tiene permitido ejecutar esta acción.');
    }
}
