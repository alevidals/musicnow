# Instrucciones de instalación y despliegue

## En local

### Requisitos

- *PHP 7.3.0*
- *PostgreSQL 12.0 o superior*
- *Composer*
- *Cuenta de Firebase*
- *Cuenta de gmail*
- *Cuenta de desarrollador de PayPal*

### Instalación

1. Ejecutamos los siguientes comandos
    ```sh
    $ git clone https://github.com/alevidals/musicnow.git
    $ cd musicnow
    $ componser install
    ```

2. Rellenamos las variables de entorno, para ello cogemos el fichero `.env.dist` y lo guardamos como `.env`. Una vez lo hayais guardado rellenar las variables.

3. Creamos la base de datos y le cargamos los datos, para ello:
    ```sh
    $ db/create.sh
    $ db/load.sh
    ```

4. Para iniciar la aplicación ejecutamos el comando:
    ```sh
    $ make serve
    ```

5. Ya estaría todo funcionando!! Para acceder a la aplicación introducimos en el navegador `localhost:8080`.

## En la nube

### Requisitos

- Instalar Heroku CLI

### Despliegue

1. Ejecutamos el siguiente comando para clonar el repositorio: ` $ git clone https://github.com/alevidals/musicnow.git`

2. Creamos una aplicación en Heroku (anteriormente debemos registrarnos).

3. Añadimos el add-on **Heroku Postgres** para poder conectar nuestra base de datos de PostgreSQL.

4. En el mismo directorio que hemos clonado, debemos de ejecutar los siguiente comandos:
    1. `$ heroku login` para iniciar sesión con Heroku.
    2. `$ heroku git:remote -a nombre_app_heroku` para añadir el remoto.
    3. `$heroku psql < db/musicnow.sql -a nombre_aplicacion` para tener en Heroku nuestra base de datos.

5. Configuramos las variables de entorno:
    - `YII_ENV` en esta variable indicamos prod
    - `url_suffix` en esta variable añadiremos el sufijo de los archivos de Firebase.
    - `url_prefix` en esta variable añadiremos el prefijo de los archivos de Firebase.
    - `type` el tipo de cuenta de Google.
    - `token_uri` el token de acceso a Firebase.
    - `SMTP_PASS` contraseña de la cuenta de correo.
    - `project_id` el id del proyecto de Google.
    - `private_key_id` el id de la clave privada.
    - `private_key` la clave privada.
    - `payPalClientSecret` la clave secreta de PayPal.
    - `payPalClientId` el id de cliente de PayPal.
    - `DATABASE_URL` la url de la base de datos.
    - `databaseUri` el uri de la base de datos de Firebase.
    - `client_x509_cert_url` la url de cliente de la api.
    - `client_id` el id de cliente de Firebase.
    - `client_email` el email de cliente de Firebase.
    - `bucket` el bucket Firebase.
    - `auth_uri` la uri de autenticación.
    - `auth_provider_x509_cert_url` la url de autenticación.

6. Ya estaría todo funcionando!!