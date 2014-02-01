Twitter-Timeline
========================

Muestra facilmente los últimos tweets de un usuario de Twitter mediante PHP y OAuth, haciendo uso de la nueva API 1.1.

Características
========================

- Muestra el timeline de Twitter del usuario especificado. Incluyendo el nombre, nombre de usuario, fecha, tweet, número de RTs y número de favoritos.
- Permite configurar el número de tweets a mostrar.
- Permite mostrar RTs.
- Permite ocultar los tweets de respuesta (Los que empiezan con @usuario).
- Implementa cache para evitar superar el nº máx de peticiones permitidas por la API (150 peticiones/hora).
- Permite configurar el formato de la fecha. Incluyendo el estilo de Twitter (Ej: hace 1h).
- Permite personalizar totalmente el html que genera.
- Convierte en enlaces las url, twitter ids y hashtags.
- Estilos CSS predefinidos y fácilmente personalizables.
- Permite mostrar varios timelines por separado.

![Captura pantalla](http://davidmiguel.com/proyectos/twitter-timeline/img/twitter-timeline.png "Captura pantalla")

Uso
========================

**-Demo:** http://davidmiguel.com/proyectos/twitter-timeline/

#### Registrar app ####

Antes de nada tienes que crearte una cuenta de desarrollador y registrar la app/website desde https://dev.twitter.com. 

Al completar el registro y crear un token de acceso, dispondrás de cuatro claves: **consumer key**, **consumer secret**, **access token** y **access token secret**.

*El nivel de acceso basta con `read`.

#### Añadir librería ####

Añade la carpeta `lib/twitter-timeline/` a tu proyecto. Esta contiene los archivos `timeline.php` y `TwitterAPIExchange.php`.

#### Añadir claves ####

Edita el archivo `timeline.php` introduciendo los tokens de acceso que te han proporcionado.


```php
$settings = array(
	'consumer_key'				=> "xxxxxxxxxxxxxxxxxxxx",
	'consumer_secret'			=> "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
	'oauth_access_token'		=> "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
	'oauth_access_token_secret'	=> "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",		
);
```

#### Añadir estilos predefinidos ####

Añade los estilos CSS predeterminados a tu hoja de estilos. Estos se encuentran en `css/main.css` en la sección `Twitter Timeline`.

*El archivo `main.css` contiene todos los estilos usados en la demo del proyecto. Los que afectan al timeline son todos los que empiezan por `#timeline`.

#### Añadir Font Awesome ####

El script necesita Font Awesome para los diferentes iconos que utiliza.

Se debe añadir el archivo `css/font-awesome.min.css` al proyecto y enlazarlo.

```html
<link rel="stylesheet" href="css/font-awesome.min.css">
```

Y añadir las siguiente tipografías en la carpeta `fonts`:

```
fonts/FontAwesome.otf
fonts/fontawesome-webfont.eot
fonts/fontawesome-webfont.svg
fonts/fontawesome-webfont.ttf
fonts/fontawesome-webfont.woff
```

*Las otras tipografías incluidas en la carpeta `fonts` se usan en la demo.

	
#### Configuración ####

En el archivo `timeline.php` puedes configurar todas las opciones por defecto disponibles:

- Ubicación archivo para caché por defecto
- Número de tweets a mostrar
- Incluir respuestas
- Incluir RTs
- Mostrar nombre, usuario, fecha, nº RTs y nº Favoritos
- Formato de la fecha
- Contenedores HTML

#### Mostrar timeline ####

En el archivo que desees mostrar el timeline, crear un contenedor con `id="timeline"`. Por ejemplo:

```html
<section id="timeline">
  ...
</section>
```

Incluir el archivo `timeline.php`.

```php
include('lib/twitter/timeline.php');
```

Y llamar a la función `mostrarTweets` indicando el nombre del usuario a mostrar (sin @), dentro del contenedor:

```php
mostrarTweets('ABI2burgos');
```
	
*Si no pasas más parámetros, se aplicarán los ajustes por defecto. Si se pasan más, se utilizarán esos ajustes. Por ejemplo, si se quiere modificar la ubicación del cache:

```php
mostrarTweets('ABI2burgos', './lib/twitter/tweets.txt');
```

*Se puede llamar a la función tantas veces como se quiera. Lo que permite mostrar timelines de distintos usuarios.

Notas
========================

- Requiere la librería cURL (http://curl.haxx.se/docs/install.html)

  *-Problemas con cURL en XAMPP:* 
	http://stackoverflow.com/questions/18574055/twitter-api-returns-null-on-xampp

- Utiliza Font Awesome para los iconos (http://fontawesome.io)
- Si no aparece ningún tweet puede ser porque exista el archivo de cache y este esté vacío. Simplemente eliminalo. Al ejecutar el script se creará automáticamente.
  
  
Créditos
========================

- Autentificación mediante: [twitter-api-php](http://github.com/j7mbo/twitter-api-php "twitter-api-php") (James Mallisont)
	
- Basado en: [latest-tweets-php-o-auth](https://github.com/andrewbiggart/latest-tweets-php-o-auth/ "latest-tweets-php-o-auth")  (Andrew Biggart)

Licencia
========================

The MIT License (MIT)

Copyright © 2014 David Miguel Lozano

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
