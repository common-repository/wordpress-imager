tinyMCEPopup.requireLangPack();

var wordpress-imagerDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.linktext.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		$(document).ready(function() {
   		// do stuff when DOM is ready
   		// Tab Navigation
   		$("#gal_tab_link").click(function(){
   			$("#pix_tab").removeClass("courent");
   			$("#gallerie_tab").addClass("courent");
   			$("#pix_panel").removeClass("current");
   			$("#gallerie_panel").addClass("current");
   		});
   		$("#pix_tab_link").click(function(){
   			$("#gallerie_tab").removeClass("current");
   			$("#pix_tab").addClass("current");
   			$("#gallerie_panel").removeClass("current");
   			$("#pix_panel").addClass("current");
   		});
   		$.get("../../../../index.php?wordpress-imagerajax=true&action=galleries", function(xml){
   			$("photoset",xml).each( function(){
   				$('#gallerie_list').append( '<li class="photoset_item"><a class="photoset_link" rel="'+$("id",this).text()+'" href="" ><p>'+ $("name",this).text() +'</p><img src="'+$("url",this).text()+'" class="prev" /></a></li>');
   			});
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
			});
			$.get("../../../../index.php?wordpress-imagerajax=true&action=pix", function(xml){
				$("img",xml).each(function(){
					$("#pix_list").append( '<li class="pix_item"><a class="pix_link" rel="'+$("id",this).text()+'" href="" ><p>'+ $("name",this).text() +'</p><img src="'+$("url",this).text()+'" class="prev" /></a></li>');
				});
				$(".pix_link").toggle(function(){
   				$(this).addClass("included");
   				this.blur();
   				return false;
   			},function(){
   				$(this).removeClass("included");
   				this.blur();
   				return false;
   			});
   			$(".pix_link").hover(function(){
   				$(this).addClass("hover");
   			},function(){
   				$(this).removeClass("hover");
   			});
			});
   		//$('#galleries_panel').html('Foo<em>Boo</em>bar');
   		$("#link_text").click(function(){this.focus();this.select();});
 		});
	},

	insert : function() {
		// Insert the contents from the input into the document
		insert = "";
		if($(".panel_wrapper .current").attr('id') == 'gallerie_panel') {
			insert = '[wordpress-imager type="'+'set'+'" id="'+$(".choosen").attr('rel')+'" /]'; 
		}
		if($(".panel_wrapper .current").attr('id') == 'pix_panel') {
			pix = "";
			i = 0;
			$(".included").each(function(){
				if ( i > 0) {
					pix = pix + ",";
				}
				i = 1;
				pix = pix + $(this).attr('rel');
			});
			insert = '[wordpress-imager type="'+'pix'+'" pix="'+pix+'" /]'
		}
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, insert);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(wordpress-imagerDialog.init, wordpress-imagerDialog);
