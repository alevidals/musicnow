# Anexos

---

## **([R34](https://github.com/alevidals/musicnow/issues/34)) Validación HTML5, CSS3 y accesibilidad**

### Validación HTML5

![Validación HTML](images/validacion_html.png)

### Validación CSS

![Accesibilidad CSS](images/validacion_css.png)

### Validación accesibilidad AA

![Validación accesibilidad](images/validacion_accesibilidad.png)

---

## **([R36](https://github.com/alevidals/musicnow/issues/36)) Varios navegadores**

### Microsoft Edge

![Microsoft Edge](images/prueba_navegador_edge.png)

### Google Chrome

![Google Chrome](images/prueba_navegador_chrome.png)

### Mozilla Firefox

![Mozilla Firefox](images/prueba_navegador_firefox.png)

### Opera

![Opera](images/prueba_navegador_opera.png)

### Brave

![Brave](images/prueba_navegador_brave.png)

---

## **([R25](https://github.com/alevidals/musicnow/issues/25)) Codeception**

![Pruebas](images/tests-funcionales.png)

---

### Prueba del seis

 1. ¿Qué sitio es éste?
    - En todo momento podemos saber que sitio es debido a que en la barra superior existe un logo que indica que estamos en esta página y en la barra inferior (footer) está indicado el nombre de la página por lo que siempre vamos a tener presentes que estamos dentro de Mus!c Now.

 2. ¿En qué página estoy?
    - Siempre vamos a saber en qué página estoy debido a que Mus!c Now incorpora en casi todas las página migas de pan (breadcrumbs) para saber donde estamos. Además con ayuda de las pretty urls con solo echar un vistazo también vamos a saber dónde estamos.

 3. ¿Cuales son las principales secciones del sitio?
    - Las principales secciones del sitio son:
        - Inicio: la página principal.
        - Tendencias: página donde vamos a poder ver que canciones son tendencia el mes actual.
        - Chat: página dónde vamos a poder chatear con los amigos (personas que se siguen mutuamente).
        - Administrar: es menú desplegable de administración con las siguiente opciones: Álbumes, Canciones, Comentarios y Playlists.
        - En forma de imagen un menú desplegable con enlace a la cuenta de perfil, a configurar el perfil y a cerrar sesión.

 4. ¿Qué opciones tengo en este nivel?
    - En este nivel además de tener posibilidad de navegar haciendo clic en las secciones anteriormente comentadas, tenemos una barra de búsqueda para buscar canciones, álbumes y usuarios. Debajo de esta barra, tendremos en forma de lista vertical las canciones que vayan subiendo las personas que seguimos y podremos escucharlas, darles likes, agregarlas a cola o a playlists.

 5. ¿Dónde estoy en el esquema de las cosas?
    - Al existir migas de pan (breadcrumbs) y al hacer uso de las pretty urls en todo momento vamos saber dónde estoy en el esquema de las cosas.

 6. ¿Cómo busco algo?
    - En la página principal tenemos un búscador que filtra por canciones, álbumes y  usuarios.

### Despliegue en servidor local

En primer lugar configure un adaptador de red interna en ambas máquinas virtuales y les di la siguiente configuración de red:

- Servidor

![Netplan servidor](images/despliegue_local/netplan-servidor.png)

- Cliente

![Netplan cliente](images/despliegue_local/netplan-cliente.png)

Ahora configuramos el DNS en el servidor.

1. Archivos creados
2. Configuración de named.conf.local
3. Configuración de db.musicnow.com (directa)
4. Configuración de db.0.168.192 (inversa)

![DNS conf](images/despliegue_local/server-conf.png)

Hacemos ping entre ambas máquinas y vemos que se ven la una a la otra.

![Ping](images/despliegue_local/ping.png)

Configuración de apache en el servidor, donde ajuste la directiva **DocumentRoot** al directorio y añadí la redirección con la directiva **Redirect**.

![Apache](images/despliegue_local/apache-conf.png)

Para configurar el SSL:

- Habilité el modulo de ssl y el sitio ssl

   ```sh
   $ sudo a2enmod ssl
   $ sudo a2ensite default-ssl
   ```

- Creé las llaves y los certificados

   ```sh
   $ sudo openssl genrsa -des3 -out server.key 2048
   $ sudo openssl req -new -key server.key -out server.csr
   $ sudo openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt
   $ sudo cp server.crt /etc/ssl/certs/
   $ sudo cp server.key /etc/ssl/private/
   ```

- Vamos al archivo default-ssl.conf que se encuentra en el directorio `/etc/apache/sites-enabled` y añadimos estas líneas (las dos últimas existen, solo modificarlas)

   ```sh
   SSLOptions +FakeBasicAuth +ExportCertData +StrictRequire
   SSLCertificateFile /etc/ssl/certs/server.crt
   SSLCertificateKeyFile /etc/ssl/private/server.key
   ```

- Por último reiniciamos Apache y ya estaría todo listo:

   ```sh
   $ sudo service apache2 restart
   ```

- Comprobamos que nos redirige correctamente a https:

![ssl](images/despliegue_local/ssl.gif)

- Comprobamos la redirección

![Redirect](images/despliegue_local/redireccion.gif)