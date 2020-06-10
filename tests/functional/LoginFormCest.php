<?php

class LoginFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('usuarios/login');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('INICIA SESIÓN CON TU NOMBRE DE USUARIO');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(\FunctionalTester $I)
    {
        $I->amLoggedInAs('1');
        $I->amOnPage('/');
        $I->see('Logout');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\app\models\Usuarios::findByUsername('admin'));
        $I->amOnPage('/');
        $I->see('Logout');
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->expect(Yii::$app->user->isGuest);
    }

    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Nombre de usuario o contraseña incorrecta.');
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'pepe',
        ]);
        $I->see('Logout');
        $I->dontSeeElement('form#login-form');
    }

    public function adminLogin(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'pepe',
        ]);
        $I->see('Panel de administración', 'h1');
        $I->dontSeeElement('form#login-form');
    }
}
