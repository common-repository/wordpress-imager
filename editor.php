<?php
chdir('../../../wp-admin');
require_once('admin.php');
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));
$webdir = dirname($_SERVER['REQUEST_URI']);
$options = get_option('WordPressImager'); ?>
<html>
<head>
	<title>Foo</title>
	<script type="text/javascript" charset="utf-8" src="<?php echo $webdir.'/js/jquery.js' ?>">
	</script>
	<style type="text/css" media="screen">
		html {
			margin:0;
			padding:0;
		}
		p {
			margin:0;
		}
		#tabs {
			list-style:none;
			margin:0;
			padding:0;
			position:absolute;
			left:20;
			top:4px;
			z-index:11;
		}
		#tabs li {
			margin:0;
			padding:0;
			display:block;
			float:left;
		}
		#tabs li a {
			border:1px solid gray;
			display:block;
			height:20px;
			padding-left:3px;
			padding-right:3px;
			margin-left:3px;
			text-decoration:none;
			color:black;
			background-color:white;
			font-variant:small-caps;
			font-family:Garamond;
		}
		#tabs li a.active {
			border-bottom:1px solid white;
			height:17px;
			margin-top:3px;
		}
		#tabs li a:hover {
			background-color: #DDDDFF;
		}
		#tabs li a.active:hover {
			background-color:white;
		} 
		#tabs li.active {
			border-bottom:1px solid white;
		}
		#panels {
			position:absolute;
			left:23px;
			top:25px;
			border:1px solid gray;
			width:460px;
			height:275px;
			padding:5px;
			overflow:auto;
		}
		#preview {
			position:absolute;
			top:25px;
			right:20px;
			width:150px;
			height:450px;
			border:1px solid gray;
		}
		#preview h1 {
			background:gray;
			color:white;
			font-variant:small-caps;
			margin:0;
			padding:0;
			font-size:15px;
			text-align:center;
			font-family:Garamond;
		}
		#preview_scroll {
			display:block;
			overflow:auto;
			height:434px;
			margin:0 auto;
		}
		#preview #preview_scroll #preview_list {
			width: 110px;
			margin:auto;
		}
		#settings {
			position:absolute;
			left:20px;
			top:320px;
		}
		#settings h1 {
			background:gray;
			color:white;
			font-variant:small-caps;
			margin:0;
			padding:0;
			font-size:15px;
			text-align:center;
			font-family:Garamond;
		}
		#buttons {
			position:absolute;
			bottom:20px;
			left:0;
		}
		#buttons a {
			text-decoration:none;
			color:black;
			font-variant:small-caps;
			display:block;
			padding-left:3px;
			padding-right:3px;
			float:left;
			border:1px solid gray;
			margin-left: 20px;
		}
		#buttons a:hover {
			background: #DDDDFF;
		}
		#galleries, #pix, #manage, #search {
			display:none;
		}
		#galleries.show, #pix.show, #manage.show, #search.show {
			display:block;
		}
		#gallerie_list, #pix_list, #preview_list {
			margin:0;
			padding:0; /*
			height:350px;
			width:350px;
			overflow:auto; */
			list-style:none;
			text-align:center;
		}
		#gallerie_list li, #pix_list li, #preview_list li {
			width:100px;
			height:102px;
			float:left;
			margin:5px;
			text-align:center;
			overflow:hidden;
		}
		#gallerie_list a, #pix_list a, #preview_list a {
			color: black;
			text-decoration:none;
			display:block;
			height:100;
			border:1px dotted #CCCCFF;
			text-align:center;
		}
		#gallerie_list a.hover, #pix_list a.hover {
			border:1px dashed #CCCCFF;
		}
		#gallerie_list a.choosen, #pix_list a.included {
			border:1px solid black;
		}
		.check {
			visibility:hidden;
		}
		img.preview {
			height:75px;
			width:75px;
			margin:auto;
			display:block;
		}
		img {
			border:none;
		}
	</style>
	<script type="text/javascript" charset="utf-8">
	// Send to Editor Function
		function send_to_editor(h) {
			var win = window.opener ? window.opener : window.dialogArguments;
			if ( !win )
				win = top;
			tinyMCE = win.tinyMCE;
			if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.getInstanceById('content') ) && !ed.isHidden() ) {
				tinyMCE.selectedInstance.getWin().focus();
				tinyMCE.execCommand('mceInsertContent', false, h);
			} else {
				win.edInsertContent(win.edCanvas, h);
			}
			win.tb_remove();
		}
	// Add the hover and click functions to Photoset-Links
		function add_photoset_funtions(){
			$(".photoset_link").click(function(){
   				$(".photoset_link").removeClass("choosen");
   				$(this).addClass("choosen");
   				this.blur();
   				return false;
   			});
   			$(".photoset_link").hover(function(){
   				$(this).addClass("hover");
   			},function(){
   				$(this).removeClass("hover");
   			});
		}
	// Add the hover and click functions to Image-Links
		function add_pix_functions(){
			$(".pix_link").click(function(){
				$(this).removeClass("hover");
   				$('#preview_list').append( '<li class="preview_pic">'+$(this).parent().html()+'</li>' );
   				$(this).remove();
   				add_preview_pix_functions();
   				return false;
   		});
   		$(".pix_link").hover(function(){
   			$(this).addClass("hover");
   		},function(){
   			$(this).removeClass("hover");
   		});
		}
	// Add the hover and click functions to Links in the selected Pix
		function add_preview_pix_functions(){
			$(".preview_pic").children("a").click(function(){
				$(this).removeClass("hover");
				$('.pix_item[@rel*='+$(this).attr('rel')+']').html( $(this).parent().html() );
				$(this).parent().remove();
				add_pix_functions();
				return false;
			});
			$(".preview_spic").children("a").click(function(){
				$(this).removeClass("hover");
				$('.search_item[@rel*='+$(this).attr('rel')+']').html( $(this).parent().html() );
				$(this).parent().remove();
				add_search_pix_functions();
				return false;
			});
			$(".pix_link").hover(function(){
   				$(this).addClass("hover");
   			},function(){
   				$(this).removeClass("hover");
   			});
		}
	// Add the hover and click fnctions to search images
		function add_search_pix_function(){
			$(".search_link").click(function(){
				$(this).removeClass("hover");
   				$('#preview_list').append( '<li class="preview_spic">'+$(this).parent().html()+'</li>' );
   				$(this).remove();
   				add_preview_pix_functions();
   				return false;
   		});
   		$(".search_link").hover(function(){
   			$(this).addClass("hover");
   		},function(){
   			$(this).removeClass("hover");
   		});
		}
	// Do what needs to be done when dom is ready!
		$(document).ready(function() {
			$("#search_button").click(function(){
				$("#search_settings").hide('slow');
				$("#search_pix").text(' ').show('slow');
				searchatts='&tags='+escape($("#tags").attr('value'));
				i=0;
				lic = '';
				$("#license").children('option').each(function(i){
					if( this.selected) {
						lic = lic + '&license[]=' + $(this).attr("value");
					}
				});
				searchatts = searchatts + lic;
				alert(searchatts);
				$.get('../../../index.php?WordPressImagerajax=true&action=search'+searchatts,function(xml){
					alert(xml);
				});
				$("#search").prepend('<a id="new_search" href="#new_search"><?php _e('new Search'); ?></a>');
				$("#new_search").click(function(){
					$(this).remove();
					$("#search_settings").show('slow');
					$("#search_pix").hide('slow');
				});
			});
		// Tab Navigation:
			$("#tabs").children().children().click(function(){
				 target = $(this).attr('href');
				 $('#tabs').children().children().removeClass("active");
				 $(this).addClass("active");
				 $('#panels').children().removeClass("show");
				 $(""+target+"").addClass("show");
			});
		// Insert Buttons:
			$('#insert_pix').click(function(){
				pix_ids = '<?php _e('undefined'); ?>';
				$("#preview_list").children().children("a").each(function(i){
					if( i > 0) {
						pix_ids = pix_ids + ',' + $(this).attr('rel');
					} else {
						pix_ids = $(this).attr('rel');
					}
				});
				show = $('#slide').attr('value');
				if(show == 'true') {
					show = 'show'
				}
				if(show == 'false') {
					show = 'box'
				} 
				 // No need to take care for "default" - this will be handled within the gallery itselfe.
				link = $('#link').attr('value');
				if(link == 'default') {
					link = '';
				} else {
					link = ' link="'+link+'"';
				}
				// Thumb size
				thumb = $('#thumbsize').attr('value');
				thumbsize = "";
				if(thumb == 'default') {
					thumbsize = '';
				} else {
					thumbsize = ' thumbsize="'+thumb+'"';
				}
				size = $('#largesize').attr('value');
				if(size == 'default') {
					size = '';
				} else {
					size = ' size="'+size+'"';
				}
				send_to_editor('[WordPressImager pix="'+pix_ids+'" type="'+'pix'+show+'"'+link+thumbsize+size+' /]');
			});
			$('#insert_set').click(function(){
				set_id = "<?php _e('undefined'); ?>";
				set_id = $('.photoset_link.choosen').attr('rel');
				show = $('#slide').attr('value');
				if(show == 'true') {
					show = 'show'
				}
				if(show == 'false') {
					show = 'box'
				} 
				 // No need to take care for "default" - this will be handled within the gallery itselfe.
				link = $('#link').attr('value');
				if(link == 'default') {
					link = '';
				} else {
					link = ' link="'+link+'"';
				}
				// Thumb size
				thumb = $('#thumbsize').attr('value');
				thumbsize='';
				if(thumb == 'default') {
					thumbsize = '';
				} else {
					thumbsize = ' thumbsize="'+thumb+'"';
				}
				size = $('#largesize').attr('value');
				if(size == 'default') {
					size = '';
				} else {
					size = ' size="'+size+'"'; 
				}
				send_to_editor('[WordPressImager id="'+set_id+'" type="'+'set'+'"'+link+thumbsize+size+' /]');
			});
		// Load the photosets
   		$.get("../../../index.php?WordPressImagerajax=true&action=galleries", function(xml){
	   			$("photoset",xml).each( function(){
	   				$('#gallerie_list').append( '<li class="photoset_item"><a class="photoset_link" rel="'+$(this).attr('id')+'" href="" ><p>'+ $(this).attr('name') +'</p><img src="'+$(this).attr('url')+'" class="prev" /></a></li>');
	   			});
	   			add_photoset_funtions();
			});
		// Load the Pix
			$.get("../../../index.php?WordPressImagerajax=true&action=pix", function(xml){
				$("img",xml).each(function(){
					$("#pix_list").append( '<li class="pix_item" rel="'+$(this).attr("id")+'"><a class="pix_link" rel="'+$(this).attr("id")+'" href="" ><p>'+ $(this).attr('name') +'</p><img src="'+$(this).attr("url")+'" class="prev" /></a></li>');
				});
				add_pix_functions();
			});
			$.get("../../../index.php?WordPressImagerajax=true&action=license", function(xml){
				$("license",xml).each(function(){
					$("#license").append('<option value="'+$(this).attr("id")+'">'+$(this).attr("name")+'</option>');
				});
			});

		// Killing all "Focus-Borders" at "a" tags!
			$("a").focus(function(){this.blur()});
		});
		
	</script>
</head>
<body id="editor" onload="">
	<ul id="tabs">
		<li id="galleries_tab">
			<a href="#galleries" class="active"><?php _e('Photosets'); ?></a>
		</li>
		<li id="pix_tab">
			<a href="#pix"><?php _e('Pix'); ?></a>
		</li>
		<li id="manage_tab">
			<a href="#manage"><?php _e('Manage'); ?></a>
		</li>
		<li id="search_tab">
			<a href="#search"><?php _e('Search'); ?></a>
		</li>
	</ul>
	<div id="panels">
		<div id="galleries" class="show">
			<ul id="gallerie_list">
			</ul>
		</div>
		<div id="pix">
			<ul id="pix_list">
			</ul>
		</div>
		<div id="manage">
			<?php _e('Comming soon. <br />Sorry, this feature isn\'t included now.'); //TODO: Upload, Describe, etc. capabilities ?>
		</div>
		<div id="search">
			<div id="search_settings">
				<p><?php _e('Tags:'); ?><input type="text" name="tags" value="" id="tags"> <?php _e('Match'); ?> <select id="tag_mode"><option value="any"><?php _e('at least one'); ?></option><option value="and"><?php _e('all'); ?></option></select></p>
				<p><?php _e('Text (Title, Description and Tags are searched)'); ?><input type="text" name="text" value="" id="text"></p>
				<p><?php _e('Licence (you may choose more then one)'); ?><select id="license" size="5" multiple="multiple"></select></p>
				<p><a href="#search" id="search_button"><?php _e('Search Flickr'); ?></a></p>
			</div>
			<div id="search_pix">
				
			</div>
		</div>
	</div>
	<div id="preview">
		<h1><?php _e('Pix choosen'); ?></h1>
		<div id="preview_scroll">
			<ul id="preview_list">
			</ul>
		</div>
	</div>
	<div id="settings">
		<h1>Settings</h1>
		<p><?php _e('Slideshow'); ?>
			<select id="slide">
				<option value="default"><?php _e("Default") ?></option>
				<option value="true"><?php _e('on'); ?></option>
				<option value="false"><?php _e('off'); ?></option>
			</select>
		</p>
		<p><?php _e('Display Link to WordPressImager-Homepage?'); ?>
			<select id="link">
				<option value="default"><?php _e("Default") ?></option>
				<option value="true"><?php _e('yes'); ?></option>
				<option value="false"><?php _e('no'); ?></option>
			</select>
		</p>
		<p><?php _e('Small Images in which size?'); ?>
			<select id="thumbsize">
				<option value="default"><?php _e('Default'); ?></option>
				<option value="square"><?php _e('Square'); ?></option>
				<option value="thumb"><?php _e('Thumbnail'); ?></option>
			</select>
		</p>
		<p><?php _e('Large Images in which size?'); ?>
			<select id="largesize">
				<option value="default"><?php _e('Default'); ?></option>
				<option value="medium"><?php _e('Medium'); ?></option>
				<option value="large"><?php _e('Large'); ?></option>
			</select>
		</p>
	</div>
	<div id="buttons">
		<a href="#" id="insert_set"><?php _e('Insert Photoset'); ?></a>
		<a href="#" id="insert_pix"><?php _e('Insert Images'); ?></a>
	</div>
</body>	