<html>
<head>
	<meta name="medium" content="mult" />
	<meta name="video_height" content="480"></meta>
	<meta name="video_width" content="640"></meta>
	<link rel="image_src" href="/assets/airbus/indexdata/thumbnail.jpg" />
	<!-- <meta name="directory" content="PATH/"></meta> -->
	<!-- <link rel="target_url" href="index.html" /> -->

	<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui"/>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<style type="text/css">
		@-ms-viewport { width: device-width; }

		* { padding: 0; margin: 0; }
		html { height: 100%; }
		body { height: 100%; }
		div#container { height: 100%; min-height: 100%; width: 100%; margin: 0 auto; }
		div#tourDIV {
			height:100%;
			position:relative;
			overflow:hidden;
			margin-top: 10px;
		}
		div#panoDIV {
			height:100%;
			position:relative;
			overflow:hidden;
			-webkit-user-select: none;
			-khtml-user-select: none;
			-moz-user-select: none;
			-o-user-select: none;
			user-select: none;
		}
	</style>

	<style type="text/css">
		div#panoDIV.cursorMoveMode {
			cursor: move;
			cursor: url(/assets/airbus/indexdata/graphics/cursors_move_html5.cur), move;
		}
		div#panoDIV.cursorDragMode {
			cursor: grab;
			cursor: -moz-grab;
			cursor: -webkit-grab;
			cursor: url(/assets/airbus/indexdata/graphics/cursors_drag_html5.cur), default;
		}
	</style>

	<script type="text/javascript">
		function readDeviceOrientation() {
			// window.innerHeight is not supported by IE
			var winH = window.innerHeight ? window.innerHeight : jQuery(window).height();
			var winW = window.innerWidth ? window.innerWidth : jQuery(window).width();
			//force height for iframe usage
			if(!winH || winH == 0){
				winH = '100%';
			}
			// set the height of the document
			jQuery('html').css('height', winH);
			// scroll to top
			//			window.scrollTo(0,0);
		}
		jQuery( document ).ready(function() {
			if (/(iphone|ipod|ipad|android|iemobile|webos|fennec|blackberry|kindle|series60|playbook|opera\smini|opera\smobi|opera\stablet|symbianos|palmsource|palmos|blazer|windows\sce|windows\sphone|wp7|bolt|doris|dorothy|gobrowser|iris|maemo|minimo|netfront|semc-browser|skyfire|teashark|teleca|uzardweb|avantgo|docomo|kddi|ddipocket|polaris|eudoraweb|opwv|plink|plucker|pie|xiino|benq|playbook|bb|cricket|dell|bb10|nintendo|up.browser|playstation|tear|mib|obigo|midp|mobile|tablet)/.test(navigator.userAgent.toLowerCase())) {
				if(/iphone/.test(navigator.userAgent.toLowerCase()) && window.self === window.top){
					jQuery('body').css('height', '100.18%');
				}
				// add event listener on resize event (for orientation change)
				if (window.addEventListener) {
					window.addEventListener("load", readDeviceOrientation);
					window.addEventListener("resize", readDeviceOrientation);
					window.addEventListener("orientationchange", readDeviceOrientation);
				}
				//initial execution
				setTimeout(function(){readDeviceOrientation();},10);
			}
		});

		/*function accessWebVr(curScene){
			unloadPlayer();

			setTimeout(function(){ loadPlayer(true, curScene); }, 100);
		}
		function accessStdVr(curScene){
			unloadPlayer();

			setTimeout(function(){ loadPlayer(false, curScene); }, 100);
		}
		function loadPlayer(isWebVr, curScene) {
			if (isWebVr) {
				embedpano({
					id:"krpanoSWFObject"
					,xml:"/assets/airbus/indexdata/index_vr.xml"
					,target:"panoDIV"
					,passQueryParameters:true
					,bgcolor:"#000000"
					,html5:"only+webgl"
					,focus: false
					,vars:{skipintro:true,norotation:true,startscene:curScene}
				});
			} else {
				var isBot = /bot|googlebot|crawler|spider|robot|crawling/i.test(navigator.userAgent);
				embedpano({
					id:"krpanoSWFObject"
					,swf:"/assets/airbus/indexdata/index.swf"
					,target:"panoDIV"
					,passQueryParameters:true
					,bgcolor:"#000000"
					,focus: false
					,html5:isBot ? "always" : "prefer"
					,vars:{startscene:curScene}

				});
			}
		}
		function unloadPlayer(){
			if(jQuery('#krpanoSWFObject')){
				removepano('krpanoSWFObject');
			}
		}
		function isVRModeRequested() {
			var querystr = window.location.search.substring(1);
			var params = querystr.split('&');
			for (var i=0; i<params.length; i++){
				if (params[i].toLowerCase() == "vr"){
					return true;
				}
			}
			return false;
		}*/
	</script>
</head>
<body>
	<div id="panoDIV">
		<noscript>
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="100%" height="100%" id="/assets/airbus/indexdata/index">
				<param name="movie" value="/assets/airbus/indexdata/index.swf"/>
				<param name="allowFullScreen" value="true"/>
				<!--[if !IE]>-->
				<object type="application/x-shockwave-flash" data="/assets/airbus/indexdata/index.swf" width="100%" height="100%">
					<param name="movie" value="/assets/airbus/indexdata/index.swf"/>
					<param name="allowFullScreen" value="true"/>
					<!--<![endif]-->
					<a href="http://www.adobe.com/go/getflash">
						<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player to visualize the Virtual Tour : AirBus320 (Virtual tour generated by Panotour)"/>
					</a>
					<!--[if !IE]>-->
				</object>
				<!--<![endif]-->
			</object>
		</noscript>
	</div>

	<script type="text/javascript" src="/assets/airbus/indexdata/index.js"></script>
	<script type="text/javascript">
		embedpano({
			swf: "/assets/airbus/indexdata/index.swf",
			target: "panoDIV",
			passQueryParameters: true
		});
	</script>
</body>
</html>
