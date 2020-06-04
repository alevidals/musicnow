# Dificultades encontradas

#### Página cargada por Ajax

- Al incluir esto, muchas de las validaciones y muchos scripts dejaron de funcionar por lo que tuve que controlarlo a mano, comprobando que dependiendo de en qué pagina estaba cargara unas cosas u otras.

#### Login y registrar en la misma página

- Para solucionar esta dificultad tuve que comprobar que formulario me estaba rellenando el usuario y a partir de ahí coger los datos de ese formulario.

#### Módulo de chat en Yii2

- Todos los módulos de chat en Yii2 pedían tener un servidor donde implementar socket y por falta de recursos limitados ya que heroku solo permitía un servidor tuve que realizar este chat a mano sin usar módulos.

---

# Elementos de innovación

- [Firebase](https://firebase.google.com/) para el almacenamiento de las canciones junto a sus portadas, así también la imágenes de perfil del usuario y los banner del perfil.
- Multilenguaje (Español e Inglés).
- Uso del módulo de chat en Yii2.
- Paypal, no estaba integrado desde el primer momento, pero decidí agregar esta innovación.
