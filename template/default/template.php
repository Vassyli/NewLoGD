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
				<?php if($this->get_model()->get("Session")->is_loggedin() === false): ?>
				<div id="loginform"><form action="<?=get_gameuri("login") ?>" method="post">
					<fieldset>
						<label><span class="sr-only">E-Mail</span><input placeholder="E-Mail" type="email" name="email" /></label>
						<label><span class="sr-only">E-Mail</span><input placeholder="Passwort" type="password" name="password" /></label>
						<label><button type="submit">Einloggen</button></label>
					</fieldset>
					<a href="<?=get_gameuri("register") ?>">Registrieren</a>
					<a href="<?=get_gameuri("pw_forgotten") ?>">Passwort vergessen?</a>
				</form></div>
				<?php else: ?>
				<div id="charstats">
					ID: <?=$this->get_model()->get("Accounts")->get_active()->get_id() ?><br />
					Name: <?=$this->get_model()->get("Accounts")->get_active()->get_name() ?><br />
					E-Mail: <?=$this->get_model()->get("Accounts")->get_active()->get_email() ?><br />
					<a href="<?=get_gameuri("logout") ?>">Ausloggen</a>
				</div>
				<?php endif; ?>
			</div>
			
			<div id="row-main">
				<div id="col-nav">
					<nav class="nav-container">
						<?php 
						foreach($this->get_page()->get_navigation() as $k=>$v): 
							// Nav-Points with an $k = 0 have no parent, they do not need a header.
							if($k !== 0):
						?>
							<div class="nav-item nav-title"><span><?=$v["item"]->get_title()?></span></div>
						<?php
							endif;
							foreach($v["childs"] as $l=>$w):
								// Actual Nav-Points
						?>
							<div class="nav-item nav-link"><a href="<?=get_gameuri($w["item"]->get_action()) ?>"><?=$w["item"]->get_title()?></a></div>
						<?php
							endforeach;
						endforeach;
						?>
					</nav>
				</div>
				
				<div id="col-content">
					<?php if(!empty($this->get_debug())): ?>
					<div id="debug"><?=$this->get_debug() ?></div>
					<?php endif; ?>
					
					<h1><?=$this->get_page()->get_title(); 
					if(!empty($this->get_page()->get_subtitle())): 
						?> <small><?=$this->get_page()->get_subtitle()?></small><?php 
					endif; ?></h1>
					
					<?=$this->get_page()->output() ?> 
				</div>
			</div>
			
			<div id="row-bottom">	
				<div id="row-bottom-copyright"><?=$this->get_copyright() ?></div>
				<div id="row-bottom-pagegen"><?=$this->get_pagegen(); ?></div>
			</div>
		</div>
	</body>
</html>