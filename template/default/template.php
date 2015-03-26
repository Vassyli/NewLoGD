<!DOCTYPE html>
<html lang="de">
	<head>
		<!-- Meta-Information -->
		<meta charset="utf-8">
		
		<!-- Ressources -->
		<link rel="stylesheet" type="text/css" href="<?=$this->get_template_uribasepath() ?>normalize.css" media="screen">
		<link rel="stylesheet" type="text/css" href="<?=$this->get_template_uribasepath() ?>css/<?=$this->get_cssfilename() ?>" media="screen">
		
		<!-- Title -->
		<title></title>
	</head>
	<!-- Displayed Content -->
	<body>
		<div id="container">
			<div id="row-top">
				<div id="logo-container"><img id="logo" src="<?=$this->get_template_uribasepath("ressource/logindragon.png") ?>" /></div>
				<nav id="main-nav"><div class="main-nav-item">{forum}</div></nav>
				<div id="charstats">{LOGIN}</div>
			</div>
			
			<div id="row-main">
				<div id="col-nav">
					<nav class="nav-container">
						<div class="nav-item nav-title"><span>Neu hier?</span></div>
						<div class="nav-item nav-link"><a href="blah">Charakter erstellen</a></div>
					</nav>
				</div>
				
				<div id="col-content">
					<div id="debug"><?=$this->get_debug() ?></div>
					
					<h1><?=$this->get_page()->get_title(); 
					if(!empty($this->get_page()->get_subtitle())): 
						?> <small><?=$this->get_page()->get_subtitle()?></small><?php 
					endif; ?></h1>
					
					<?=$this->get_page()->get_content() ?> 
				</div>
			</div>
			
			<div id="row-bottom">	
				<div id="row-bottom-copyright"><?=$this->get_copyright() ?></div>
				<div id="row-bottom-pagegen"><?=$this->get_pagegen(); ?></div>
			</div>
		</div>
	</body>
</html>