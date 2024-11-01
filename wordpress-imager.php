<?php
/*
Plugin Name: WordPress Imager
Plugin URI: http://www.wordpress-imager.de/
Description: Integrate flickr harmonically into your WordPress posts and templates
Author: Alexander Beutl
Version: 0.9.5
Author URI: http://blog.netgra.de/
*/

//PHP 4.2.x Compatibility function
if (!function_exists('file_get_contents')) {
      function file_get_contents($filename, $incpath = false, $resource_context = null)
      {
          if (false === $fh = fopen($filename, 'rb', $incpath)) {
              trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
              return false;
          }
          clearstatcache();
          if ($fsize = @filesize($filename)) {
              $data = fread($fh, $fsize);
          } else {
              $data = '';
              while (!feof($fh)) {
                  $data .= fread($fh, 8192);
              }
          }
          fclose($fh);
          return $data;
      }
  }

if (!class_exists('WordPressImager')) {
		class WordPressImager {
			var $optname = "WordPressImager";
			var $options = array();
			var $api_key = null;
			var $error = false;
			var $dbg = '';

			function WordPressImager() { //constructor
				$this->options = $this->getOptions();

				wp_enqueue_script('lytebox', get_bloginfo('wpurl').'/wp-content/plugins/wordpress-imager/js/lytebox.js.php', '', $this->options['theme']);
			}
			
// Ajax - Functions
			function ajax() {
				global $user_ID;
				if(isset($user_ID)){
				header('Content-type: application/xml');
				switch($_GET['action']) {
					case 'galleries' :
						$this->flickr_echo_galleries();
						break;
					case 'pix' :
						$this->flickr_echo_pix();
						break;
					case 'license' :
						$this->flickr_echo_license();
						break;
				}
				exit();
				}
			}
			
			function flickr_echo_galleries() {
				$params = array(
					'method' => 'flickr.photosets.getList',
				);
				// yet we do not need to specify the user id since we only have one user which is authentified.
				$sets = $this->flickr_api($params);
				if ($sets !== false) {
					$sets = $sets['photosets']['photoset'];
					echo '<photosets>';
					foreach($sets as $set) {
						echo '<photoset';
						echo ' name="'.$set['title']['_content'].'"';
						echo ' id="'.$set['id'].'"';
						echo ' url="http://farm'.$set['farm'].'.static.flickr.com/'.$set['server'].'/'.$set['primary'].'_'.$set["secret"].'_s.jpg"';
						echo ' />';
					}
					echo '</photosets>';
				}
			}

			function flickr_echo_pix($user = null) {
				if($user == null) {
					$user = $GLOBALS['user_ID'];
				}
				$params = array(
					"method" => 'flickr.photos.search',
					"user_id" => $this->options['user_'.(string)$user]['nsid'],
					"min_upload_date" => '10'
				);
				$imgs = $this->flickr_api($params);
				if ($imgs !== false) {
					$imgs = $imgs['photos']['photo'];
					echo '<imgs>';
					foreach($imgs as $img) {
						echo '<img id="'.$img['id'].'" name="'.$img['title'].'" url="'.'http://farm'.$img['farm'].'.static.flickr.com/'.$img['server'].'/'.$img['id'].'_'.$img["secret"].'_s.jpg'.'" />';
					}
					echo '</imgs>';
				} else echo $this->dbg;
			}
			
			function flickr_echo_license() {
				$params = array(
					"method" => 'flickr.photos.licenses.getInfo',
				);
				$lic = $this->flickr_api($params);
				if($lic !== false) {
					echo "<licenses>";
					foreach($lic['licenses']['license'] as $license){
						echo '<license id="'.$license['id'].'" name="'.$license['name'].'" />';
					}
					echo "</licenses>";
				}
			}
			
// Functions called by wordpress
			function addHeaderCode() {
				echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wordpress-imager/css/lytebox.css.php?ver='.$this->options['theme'].'" />';
				echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wordpress-imager/css/WordPressImager.css" />';
			}

			function shortcode($att, $content='') {
				$output = '<div id="WordPressImager">';
				if(!isset($att['type'])) {
					$att['type'] == $this->options['default_type'];
				}
				if(!isset($att['user'])) {
					$att['user'] = $GLOBALS['user_ID'];
				}
				if($att['type'] == "setslide" || $att['type'] == "set") {
					$output .= $this->flickr_getgallycode($att['id'], $att['type'], $att['user'], $att['thumbsize'], $att['size']);
				} elseif($att['type'] == "pixshow" || $att['type'] == "pixbox" || $att['type'] == 'pixdefault') {
					$output .= $this->flickr_getpixcode($att['pix'], $att['type'], $att['user'], $att['thumbsize'], $att['size']);
				}
				
				if($att['link'] == "true" || (!isset($att['link']) && $this->options['no_link'] == 'false')) {
					$output .= '<p class="WordPressImager_powered">('.__('Powered by').' <a href="http://www.wordpress-imager.de/">WordPress Imager</a>)</p>';
				}
				$output .= '</div>';
				return $output;
				if($this->error) {
					// TODO:: an error occured - fall back into displaying only a link to the gallery (or to each pic?)
				} else {
					return $output;
				}
			}
			
			function init() {
				$this->getOptions();
			}
			
			function adminPanel() {
				global $user_ID;
				echo '<div class="wrap">';
				if(isset($_POST['clear_auth'])) {
					$this->options['user_'.(string)$user_ID] = null;
					update_option($this->optname, $this->options);
				}
				if(isset($_POST['get_token'])) {
					$tok = $this->flickr_getToken();
				}
				echo "<h2>".$tok."</h2>";
				// Update if form was submitted
				if(isset($_POST['gallysubmit'])) {
					foreach($_POST[$this->optname] as $key => $value) {
						$this->options[$key] = $value;
					}
					update_option($this->optname, $this->options);
					?> <div class="updated"><p><strong><?php _e("Settings Updated.", "WordPressImager");?></strong></p></div> <?php
				}
				?><form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>"><?php
				?><h2>Authorisierung</h2>
				<p>For accessing your Images WordPress Imager needs to be authorized.</p>
				<p>Authorization only applies to the currently logged in user.</p>
				<p><?php if($this->options['user_'.(string)$user_ID]['token'] == null) { ?><a href="<?php echo $this->flickr_getAuthLink(); ?>" target="_blank">Start Authorization</a></p><p><input type="submit" name="get_token" value="Get Token" id="get_token" /></p></form><form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>"><p>Clear Authorization:<input type="submit" name="clear_auth" value="Clear Auth" id="clear_auth" /><?php } else { ?>Clear Authorization:<input type="submit" name="clear_auth" value="Clear Auth" id="clear_auth" /><?php } ?></p></form><?php
				?><form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>"><?php
				?><h2>Gallerie</h2>
					<p><label><?php _e('Theme'); ?>
						<select name="<?php echo $this->optname ?>[theme]">
							<option value="grey"<?php if($this->options['theme'] == 'grey') echo ' selected="selected"'; ?>><?php _e('grey'); ?></option>
							<option value="blue"<?php if($this->options['theme'] == 'blue') echo ' selected="selected"'; ?>><?php _e('blue'); ?></option>
							<option value="green"<?php if($this->options['theme'] == 'green') echo ' selected="selected"'; ?>><?php _e('green'); ?></option>
							<option value="gold"<?php if($this->options['theme'] == 'gold') echo ' selected="selected"'; ?>><?php _e('gold'); ?></option>
							<option value="orange"<?php if($this->options['theme'] == 'orange') echo ' selected="selected"'; ?>><?php _e('orange'); ?></option>
							<option value="red"<?php if($this->options['theme'] == 'red') echo ' selected="selected"'; ?>><?php _e('red'); ?></option>
						</select>
						</label></p>
						<p>Activate slideshow as default
<label><input type="radio" name="<?php echo $this->optname; ?>[default_type]" value="show"<?php if($this->options['default_type'] == 'show') echo 'checked="checked"'; ?> />yes</label>
<label><input type="radio" name="<?php echo $this->optname; ?>[default_type]" value="box"<?php if($this->options['default_type'] == 'box') echo ' checked="checked"'; ?> />no</label></p>
					<p><label><?php _e('Cache Time in Minutes'); ?><input type="text" name="<?php echo $this->optname ?>[cache_minutes]" value="<?php echo $this->options['cache_minutes']; ?>" /></label></p>
				<p><?php _e('Ad "Powered by WordPress Imager" to Galleries?'); ?>
					<label><input type="radio" name="<?php echo $this->optname; ?>[no_link]" value="false"<?php if($this->options['no_link'] == "false") echo 'checked="checked"'; ?> />yes</label>
					<label><input type="radio" name="<?php echo $this->optname; ?>[no_link]" value="true"<?php if($this->options['no_link'] == "true") echo 'checked="checked"'; ?> />no</label></p>
					
				</p>
				<p><input type="submit" name="gallysubmit" value="submit" id="gallysubmit" /></p>
				<?php echo '</form></div>';
			}
			
// Functions not called by wordpress directly

			function flickr_getFrob(){
				global $user_ID;
				if(empty($this->options['user_'.(string)$user_ID]['frob']) || $this->options['user_'.(string)$user_ID]['frob'] == 'false'){
					$params = array('method'=> 'flickr.auth.getFrob');
					$frob = $this->flickr_call($params);
					if($frob !== false) {
						$frob = unserialize($frob);
						$frob = $frob['frob'];
						$frob = $frob['_content'];
						$this->options['user_'.(string)$user_ID]['frob'] = $frob;
						update_option($this->optname, $this->options);
						return $frob;
					} else {
						return false;
					}
				} else {
					return $this->options['user_'.(string)$user_ID]['frob'];
				}
			}
			
			function flickr_getAuthLink(){
				$frob = $this->flickr_getFrob();
				$link = "http://flickr.com/services/auth/?api_key=".$this->api_key.'&perms=delete&frob='.$frob.'&api_sig=';
				$link .= md5($this->api_secret.'api_key'.$this->api_key.'frob'.$frob.'permsdelete');
				return $link;
			}
			
			function flickr_getToken() {
				global $user_ID;
				$params = array(
					'method' => 'flickr.auth.getToken',
					'frob' => $this->options['user_'.(string)$user_ID]['frob'],
				);
				$tok = $this->flickr_call($params);
				if ($tok !== false) {
					$tok = unserialize($tok);
					$this->options['user_'.(string)$user_ID]['username'] = $tok['auth']['user']['username'];
					$this->options['user_'.(string)$user_ID]['nsid'] = $tok['auth']['user']['nsid'];
					$this->options['user_'.(string)$user_ID]['token'] = $tok['auth']['token']['_content'];
					update_option($this->optname, $this->options);
				}
			}

			function getOptions() {
				global $user_ID;
				// get the default values - updateproof!
				$options = array(
					'theme' => 'grey',
					'user_apikey' => '',
					'cache_minutes' => 1440, // One day
					'plugin_apikey' => '36fb9d2742de586b9a6889d7c5a05e2b',
					'plugin_secret' => '6abbb5986780d2db',
					'default_type' => 'show',
					'no_link' => 'true',
					'size' => 'medium',
					'thumbsize' => 'square'
				);
				$loadOptions = get_option($this->optname);
				if (!empty($loadOptions)) {
                foreach ($loadOptions as $key => $option)
                    $options[$key] = $option;
            }
            if(!isset($options['user_'.(string)$user_ID])) {
            	global $user_ID;
            	if(isset($user_ID)) {
	            	$options['user_'.(string)$user_ID] = array(
	            		'frob' => null,
	            		'token' => null,
	            	);
         		}
            }
            update_option($this->optname, $options);
            if( $options['user_apikey'] != '') {
            	$this->api_key = $options['user_apikey'];
            	$this->api_secret = $options['user_secret'];
				} else {
					$this->api_key = $options['plugin_apikey'];
            	$this->api_secret = $options['plugin_secret'];
				}
            return $options;
			}
			
			function flickr_getpixcode($ids, $type, $user, $thumbsize, $size) {
				$ids = explode(',',$ids);
				$code = "";
				$galnum = rand(10000,50000);
				if($type = "pixshow") {
					$type_out = "show";
				} elseif($type = 'pixbox') {
					$type_out = "box";
				} else {
					$type_out = $this->options['default_type'];
				}
				if($thumbsize == "thumb") {
					$thumbext = '_t';
				} else {
					$thumbext = '_s';
				}
				foreach($ids as $id) {
					$params = array(
						'method' => 'flickr.photos.getInfo',
						'photo_id' => $id,
					);
					$img = $this->flickr_api($params);
					if( $img !== false) {
						$desc = htmlspecialchars($img['photo']['description']['_content']);
						$photo = $img['photo'];
						$flickr = "";
						if($img['photo']['visibility']['ispublic'] == '1') {
							$flickr = htmlspecialchars( '<br /><a href="http://www.flickr.com/photos/'.$photo['owner']['nsid'].'/'.$photo['id'].'/">Visit at flickr</a>' );
						}
						$code .= '<div class="photo"><a href="'
.'http://farm'.$photo['farm'].'.static.flickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'.jpg'.'" rel="lyte'.$type_out.'['.$galnum.']" title="'.$desc.$flickr.'" class="photolink"><span><img src="'
.'http://farm'.$photo['farm'].'.static.flickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].$thumbext.'.jpg'.'" class="WordPressImager_img" style="float:none;" /><br />'.$photo['title']['_content'].'</span></a></div>';
					}
				}
				$code .= '<div style="clear:both;"> </div>';
				return $code;
			}
			
			function flickr_getgallycode($id, $type = null, $user, $thumbsize, $size) {
				// get the gally headers array
				$params = array(
					'method'	=> 'flickr.photosets.getInfo',
					'photoset_id'	=> $id
				);
				$headers = $this->flickr_api($params);
				if ($headers !== false) {
					if($thumbsize == "thumb") {
						$thumbext = "_t";
					} else {
						$thumbext = "_s";
					}
					if(!isset($size) || ($size != 'medium' && $size != 'large'))
						$size = $this->options['size'];
					switch ($size) {
						case "medium": 
							$sizeext = '';
							break;
						case "large":
							$sizeext = '_b';
							break;
					}
					$headers = $headers['photoset']; // just one step closer...
					// get the gally pics array
					$params['method'] = 'flickr.photosets.getPhotos';
					$photos = $this->flickr_api($params, $user);
					$photos = $photos['photoset']['photo'];
					
					// now create the codes
					$code = '<h2>'.$headers['title']['_content'].'</h2>'
							. '<p>'.$headers['title']['description'].'</p>';
					foreach($photos as $photo) {
						$params = array(
							'method' => 'flickr.photos.getInfo',
							'photo_id' => $photo['id'],
						);
						if($type = "setslide") {
							$type_out = "show";
						} else {
							$type_out = "box";
						}
						$desc = $this->flickr_api($params);
						$flickr = '';
						if($desc['photo']['visibility']['ispublic'] == '1') {
							$flickr = htmlspecialchars( '<br /><a href="http://www.flickr.com/photos/'.$desc['photo']['owner']['nsid'].'/'.$photo['id'].'/">'.__('Visit at flickr', 'wordpressimager').'</a>' );
						}
						$desc = htmlspecialchars($desc['photo']['description']['_content']);
						$code .= '<div class="photo"><a href="'
.'http://farm'.$photo['farm'].'.static.flickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'.jpg'.'" rel="lyte'.$type_out.'['.$id.']" title="'.$desc.$flickr.'" class="photolink"><span><img src="'
.'http://farm'.$photo['farm'].'.static.flickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].$thumbext.'.jpg'.'" class="WordPressImager_img" style="float:none;" /><br />'.$photo['title'].'</span></a></div>';
					}
					$code .= '<div style="clear:both;"> </div>';
					return $code;
				} else {
					return false;
				}
			}

			function flickr_api($params, $user = null) {
				if ($params['method'] == 'flickr.photos.getInfo') {
					$filename = dirname(__FILE__) .'/cache/flickr.photos.getInfo/'.$params['photo_id'];
				} elseif ($params['method'] == 'flickr.photosets.getInfo') {
					$filename = dirname(__FILE__) .'/cache/flickr.photosets.getInfo/'.$params['photoset_id'];
				} elseif ($params['method'] == 'flickr.photosets.getPhotos') {
					$filename = dirname(__FILE__) .'/cache/flickr.photosets.getPhotos/'.$params['photoset_id'];
				}
				
				if (file_exists($filename) && ( filectime( $filename ) > time() - (60 * $this->options['cache_minutes']) || $params['method'] == 'flickr.photos.getInfo')) {
					$str = file_get_contents($filename);
				} else {
					$str = $this->flickr_call($params, $user);
					if ($str !== false) {
						file_put_contents($filename , $str);
					}
				}
				if ($str !== false) {
					return unserialize($str);
				} else {
					return false;
				}
			}
			
			function flickr_call($params, $user = null) {
				if($user == null) {
					$user = $GLOBALS['user_ID'];
				}
				$this->dbg .= "<h1>".$params['method']."</h1>";
				$this->dbg .= "<h2>Request:</h2>";
				$params = array_merge(
					array(
						'api_key' => $this->api_key,
						'format'	=> 'php_serial',
						'auth_token' => $this->options['user_'.(string)$user]['token'],
					) , $params
				);
				ksort($params);
				$auth = '';
				foreach($params as $k=>$v) {
					$auth .= $k.$v;
				}
				$auth = md5($this->api_secret . $auth);
				$enc = array();
				foreach ($params as $key=>$value) {
					$enc[] = urlencode($key).'='.urlencode($value);
				}
				$this->dbg .= "<pre>".var_export($params,true)."</pre>";
				$url = "http://api.flickr.com/services/rest/?".implode('&', $enc).'&api_sig='.$auth;
				$response = $this->get_file_contents($url);
				$this->dbg .= "<h2>Response:</h2>";
				$this->dbg .= "<pre>".var_export($response,true)."</pre>";
				$rsp = unserialize($response);
				if( $rsp['stat'] == 'ok') {
					return $response;
				} else {
					// TODO:: Include a Error Handling which depends upon the Status returned
					return false;
				}
			}
			
			function get_file_contents($URL) {
				if (!extension_loaded('curl')) {
					return @file_get_contents($URL);
				} else {
			        $c = @curl_init();
			        @curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			        @curl_setopt($c, CURLOPT_URL, $URL);
			        $contents = @curl_exec($c);
			        @curl_close($c);
			        if ($contents) return $contents;
			            else return FALSE;
		        }
			}
			
	} //End Class WordPressImager 
} 

if (class_exists("WordPressImager")) {
	$WordPressImager_obj = new WordPressImager();
}

//Initialize the admin panel
if (!function_exists("WordPressImager_ap")) {
	function WordPressImager_ap() {
		global $WordPressImager_obj;
		if (!isset($WordPressImager_obj)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('WordPress Imager', 'WordPress Imager', 9, 'wordpress_imager', array(&$WordPressImager_obj, 'adminPanel'));
		}
	}
}
 
function register_WordPressImager_button($buttons) {
   array_push($buttons, "separator", "WordPressImager");
   return $buttons;
}
 
function flickr_media(){
	global $user_ID;
	echo '<a href="../wp-content/plugins/wordpress-imager/editor.php?'."user={$user_ID}&amp;".'TB_iframe=true&amp;height=500&amp;width=640" class="thickbox" title="'.__('Add flickr images').'"><img src="../wp-content/plugins/wordpress-imager/imgr.png" /></a>';
}

//Actions and Filters   
if (isset($WordPressImager_obj)) {
	add_action('media_buttons','flickr_media',11);
	add_action('wp_head', array(&$WordPressImager_obj, 'addHeaderCode'));
	add_action('activate_WordPressImager/WordPressImager.php', array(&$WordPressImager_obj, 'init'));
	add_action('admin_menu', 'WordPressImager_ap');
	if(isset($_GET['WordPressImagerajax'])) {
		add_action('init', array(&$WordPressImager_obj , 'ajax'));
	}
	add_shortcode('WordPressImager', array(&$WordPressImager_obj, 'shortcode'));
}
 
?>