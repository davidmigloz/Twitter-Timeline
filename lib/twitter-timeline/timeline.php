<?php
/***************************************************************************************
 * Twitter Timeline
 ***************************************************************************************
 * Permite mostrar el timeline de un usuario de twitter con la nueva API 1.1, mediante 
 * PHP y OAuth. Totalmente configurable. Genera código HTML5 (personalizable).
 *************************************************************************************** 
 * @version	1.0
 * @author	David Miguel Lozano
 * @link	https://github.com/davidmigloz/Twitter-Timeline
 * @license MIT License
 ***************************************************************************************
 * Características:
 *************************************************************************************** 
 * - Muestra el timeline de Twitter del usuario especificado. Incluyendo el nombre,
 *   nombre de usuario, fecha, tweet, número de RTs y número de favoritos.
 * - Permite configurar el número de tweets a mostrar.
 * - Permite mostrar RTs.
 * - Permite ocultar los tweets de respuesta (Los que empiezan con @usuario).
 * - Implementa cache para evitar superar el nº máx de peticiones permitidas por la API 
 *   (150 peticiones/hora).
 * - Permite configurar el formato de la fecha. Incluyendo el estilo de Twitter 
 *   (Ej: hace 1h).
 * - Permite personalizar totalmente el html que genera.
 * - Convierte en enlaces las url, twitter ids y hashtags.
 * - Estilos CSS predefinidos y fácilmente personalizables.
 * - Permite mostrar varios timelines por separado.
 * 		---> Demo: http://davidmiguel.com/proyectos/twitter-timeline/
 *************************************************************************************** 
 * Notas:
 ***************************************************************************************  
 * - Requiere la librería cURL (http://curl.haxx.se/docs/install.html)
 * 		*Problemas con cURL en XAMPP: 
 * 		 http://stackoverflow.com/questions/18574055/twitter-api-returns-null-on-xampp
 * - Utiliza Font Awesome para los iconos (http://fontawesome.io)
 * - Si no aparece ningún tweet puede ser porque exista el archivo de cache y este 
 *   esté vacío. Simplemente eliminalo, al ejecutar el script se creará automáticamente.
 ***************************************************************************************
 * Creditos:
 ***************************************************************************************
 * > twitter-api-php (James Mallisont)
 * http://github.com/j7mbo/twitter-api-php
 * > latest-tweets-php-o-auth (Andrew Biggart)
 * https://github.com/andrewbiggart/latest-tweets-php-o-auth/
 ***************************************************************************************
 */
	
require('TwitterAPIExchange.php');

// Zona horaria, para Fecha estilo Twitter (http://php.net/manual/en/timezones.php)
date_default_timezone_set('Europe/Madrid');
	
function mostrarTweets(
	// Parámetros
	$screen_name,						// Nombre de usuario (Ej: nombre_2013). Pasar por parámetro
	$archivo_cache			= './tweets.txt',	// Archivo para cache. (Por defecto: en el dir actual)
	$tweets_a_mostrar		= 6,			// Nº tweets a mostrar
	$ignorar_respuestas		= false,		// No incluir tweets de respuestra (Los que empiezan por @usuario)
	$incluir_rts			= true,			// Incluir RTs
	$mostrar_nombre			= true,			// Mostrar nombre
	$mostrar_usuario		= true,			// Mostrar usuario
	$mostrar_fecha			= true,			// Mostrar fecha
	$mostrar_nRTs			= true,			// Mostrar número de retweets	
	$mostrar_nFav			= true,			// Mostrar número de favoritos
	$formato_fecha			= 'd/m/y', 		// Formato fecha (http://php.net/manual/en/function.date.php)	
	$fecha_estilo_twitter	= true,           		// Fecha estilo Twitter (Ej: hace 1h) (para tweets de hace <24h)	
	// HTML
	$tweet_open    			= '<article>',							// Contenedor tweet
	$tweet_close  			= '</article>',							// Contenedor tweet cierre
	$tweet_header_open		= '<header>',							// Contenedor tweet
	$tweet_header_close		= '</header>',							// Contenedor tweet cierre	
	$nombre_open			= '<span class="nombre"><i class="fa fa-caret-right"></i> ',	// Contenedor nombre
	$nombre_close     		= '</span>',							// Contenedor nombre cierre	
	$usuario_open			= '<span class="usuario">· @',					// Contenedor usuario
	$usuario_close     		= '</span>',							// Contenedor usuario cierre
	$fecha_open       		= '<time datetime="',						// Contenedor fecha
	$fecha_mid       		= '"><i class="fa fa-clock-o"></i> ',				// Contenedor fecha intermedio
	$fecha_close      		= '</time>',							// Contenedor fecha cierre	
	$tweet_retweeted		= '<i class="fa fa-retweet"></i>',				// Símbolo de tweet retweeted
	$nRTs_open				= '<i class="fa fa-retweet nRTs">',			// Contenedor número de retweets
	$nRTs_close     		= '</i>',							// Contenedor número de retweets cierre
	$nFav_open				= '<i class="fa fa-star nFav">',			// Contenedor número de favoritos
	$nFav_close     		= '</i>',							// Contenedor número de favoritos cierre	
	$tweet_text_open		= '<p>',							// Contenedor texto tweet
	$tweet_text_close		= '</p>'){							// Contenedor texto tweet cierre

	// Claves para autentificación (https://dev.twitter.com/)
	$settings = array(
		'consumer_key'			=> "xxxxxxxxxxxxxxxxxxxx",
		'consumer_secret'		=> "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
		'oauth_access_token'		=> "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
		'oauth_access_token_secret'	=> "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
	);

	// Periodo de cache
	$periodo_cache = 60*3;

	// Última actualización del cache
	$actualizado_cache = ((file_exists($archivo_cache))) ? filemtime($archivo_cache) : 0;

	// Mostrar datos cache si no ha superado el periodo
	if(time() - $periodo_cache < $actualizado_cache) {
		readfile($archivo_cache);		 
	} else {
		// Para evitar que salgan menos tweets de los establecidos cuando no están activados los RTs o respuestas
		if(!$incluir_rts || $ignorar_respuestas){
			// Si no están activados los RTs o respuestas, traigo más tweets de los que voy a mostrar
			$factor = 2; // Si aún así no se muestran el nº de tweets establecido, aumentar el factor
			$nTweets = $tweets_a_mostrar*$factor;
			$nTweets  = ($nTweets>200) ? 200 : $nTweets; // El máx nº de tweets que devuelve la API es 200
		}else{
			// Si están activados, traigo los justos
			$nTweets = $tweets_a_mostrar;
		}
	
		// Petición get (https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline)
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$parametros = "?screen_name=".$screen_name."&count=".$nTweets."&include_rts=".$incluir_rts."&exclude_replies=".$ignorar_respuestas;
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($settings);
		$get_tweets = $twitter->setGetfield($parametros)
					  ->buildOauth($url, $requestMethod)
					  ->performRequest();

		// Decodificar string JSON recibido			  
		$get_tweets = json_decode($get_tweets, true); 
		
		// Comprobar si ha habído algún error, si es así, mostrar error y salir
		if(array_key_exists('errors', $get_tweets)){
			echo "<b>Error:</b> ".$get_tweets['errors'][0]['message'];
			return;
		}
				  
		if (count($get_tweets)) {
			// Contador tweets
			$tweet_count = 0;

			// Inicir buffer salida (para cache)
			ob_start();
			
			// Código html resultante
			$twitter_html = "";

			// Iterar cada tweet
			foreach($get_tweets as $tweet) {
				// Aumentamos contador tweets
				$tweet_count++;
				
				// Saber si es retweeted
				$rt = array_key_exists('retweeted_status', $tweet);
				
				if($rt){
					// Si es retweeted
					$nombre = $tweet['retweeted_status']['user']['name'];		// Nombre
					$usuario = $tweet['retweeted_status']['user']['screen_name'];	// Usuario
					$fecha = strtotime($tweet['retweeted_status']['created_at']);	// Fecha
					$tweet_text = $tweet['retweeted_status']['text'];		// Texto
				}else{
					// Si es propio
					$nombre = $tweet['user']['name'];		// Nombre 
					$usuario = $tweet['user']['screen_name'];	// Usuario
					$fecha = strtotime($tweet['created_at']);	// Fecha
					$tweet_text = $tweet['text'];			// Texto
					$nRTs = $tweet['retweet_count'];		// Nº RTs					
					$nFav = $tweet['favorite_count'];		// Nº favoritos
				}
												
				// Formatear fecha
				if ($fecha_estilo_twitter){
					// Estilo Twitter
					$current_time = time();
					$time_diff = abs($current_time - $fecha);
					switch ($time_diff) {
						case ($time_diff < 60):
							$fecha_formateada = 'hace '.$time_diff.' segundo';     
							if ($time_diff > 1){ $fecha_formateada .= 's'; }
							break;      
						case ($time_diff >= 60 && $time_diff < 3600):
							$min = floor($time_diff/60);
							$fecha_formateada = 'hace '.$min.' minuto'; 
							if ($min > 1){ $fecha_formateada .= 's'; }
							break;      
						case ($time_diff >= 3600 && $time_diff < 86400):
							$hour = floor($time_diff/3600);
							$fecha_formateada = 'hace '.$hour.' hora';
							if ($hour > 1){ $fecha_formateada .= 's'; }
							break;          
						default:
							$fecha_formateada = date($formato_fecha,$fecha);
							break;
					}
				} else {
					// Formato indicado
					$fecha_formateada = date($formato_fecha,$fecha);
				}
								
				// Añadir enlaces a las url, twitter ids o hashtags
				$tweet_text = preg_replace("/((http)+(s)?:\/\/[^<>\s]+)/i", "<a href=\"$0\" target=\"_blank\">$0</a>", $tweet_text );
				$tweet_text = preg_replace("/[@]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/$1\" target=\"_blank\">$0</a>", $tweet_text );
				$tweet_text = preg_replace("/[#]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/search?q=%23$1\" target=\"_blank\">$0</a>", $tweet_text );

				// Ensamblar html	
				$twitter_html .= $tweet_open."\n";		//<article>		
				$twitter_html .= "\t".$tweet_header_open."\n";	//<header>
				if($mostrar_nombre)
					$twitter_html .= "\t\t".$nombre_open.'<a href="http://twitter.com/'.$usuario.'" target="_blank">'.$nombre.'</a>'.$nombre_close."\n";	//<span class="nombre"><i class="fa fa-caret-right"></i> Nombre</span>
				if($mostrar_usuario)
					$twitter_html .= "\t\t".$usuario_open.'<a href="http://twitter.com/'.$usuario.'" target="_blank">'.$usuario.'</a>'.$usuario_close."\n";	//<span class="usuario">· @usuario</span>
				if($mostrar_fecha)
					$twitter_html .= "\t\t".$fecha_open.date("Y-m-d",$fecha).$fecha_mid.$fecha_formateada.$fecha_close."\n"; //<time datetime="2014-01-24"><i class="fa fa-clock-o"></i> hace 10h</time>
				if($rt){
					$twitter_html .= "\t\t".$tweet_retweeted."\n";	// Símbolo retweet
				}else{
					if($mostrar_nRTs && $nRTs>0)
						$twitter_html .= "\t\t".$nRTs_open.$nRTs.$nRTs_close."\n";	// Símbolo retweet, número RTs
					if($mostrar_nFav && $nFav>0)
						$twitter_html .= "\t\t".$nFav_open.$nFav.$nFav_close."\n";	// Símbolo estrella, número favoritos				
				}
				$twitter_html .= "\t".$tweet_header_close."\n";	//</header>
				$twitter_html .= "\t\t".$tweet_text_open.html_entity_decode($tweet_text).$tweet_text_close."\n"; //<p>...tweet...</p>
				$twitter_html .= $tweet_close."\n";								 //</article>	
				
				// Salir del bucle si ya se han mostrado el nº tweets configurado
				if ($tweet_count >= $tweets_a_mostrar){
					break;
				}
			}

			//Enlace "más tweets"
			$twitter_html .= '<br/><a href="http://twitter.com/'.$screen_name.'" target="_blank">Más tweets</a>'."\n";
			
			// Mostrar html
			echo $twitter_html;

			// Guardar en cache
			$file = fopen($archivo_cache, 'w');
			fwrite($file, ob_get_contents()); 
			fclose($file); 
			
			// Limpiar buffer
			ob_end_flush();	
		}
	}	
}	
?>
