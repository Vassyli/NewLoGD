<!DOCTYPE html>
<html lang="de">
    <head>
        <!-- Meta-Information -->
        <meta charset="utf-8">

        <!-- Ressources -->
        <link rel="stylesheet" type="text/css" href="<?=$this->getTemplateUribasepath() ?>normalize.css" media="screen">
        <link rel="stylesheet" type="text/css" href="<?=$this->getTemplateUribasepath() ?>css/<?=$this->getCssFilename() ?>" media="screen">

        <!-- External ressources -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

        <!-- JavaScript -->
        <script src="<?=$this->getTemplateUribasepath() ?>js/script.js"></script>

        <!-- Title -->
        <title>NewLoGD - <?=$this->getPage()->getTitle(); ?></title>
    </head>
	<!-- Displayed Content -->
    <body>
        <div id="container">
            <div id="row-top">
                <div id="logo-container"><img id="logo" src="<?=$this->getTemplateUribasepath("ressource/logindragon.png") ?>" /></div>
                <nav id="main-nav"><div class="main-nav-item">{forum}</div></nav>
                <?php if($this->getModel()->get("Session")->is_loggedin() === false): ?>
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
                    ID: <?=$this->getModel()->get("Accounts")->get_active()->getId() ?><br />
                    Name: <?=$this->getModel()->get("Accounts")->get_active()->getName() ?><br />
                    E-Mail: <?=$this->getModel()->get("Accounts")->get_active()->getEmail() ?><br />
                    <a href="<?=get_gameuri("logout") ?>">Ausloggen</a>
                </div>
                <?php endif; ?>
            </div>

            <div id="row-main">
                <div id="col-nav">
                    <nav class="nav-container">
                        <?php 
                        foreach($this->getPage()->getNavigation() as $k=>$v): 
                            // Nav-Points with an $k = 0 have no parent, they do not need a header.
                            if($k !== 0):
                        ?>
                            <div class="nav-item nav-title"><span><?=$v["item"]->getTitle()?></span></div>
                        <?php
                            endif;
                            foreach($v["childs"] as $l=>$w):
                                // Actual Nav-Points
                        ?>
                            <div class="nav-item nav-link"><a href="<?=$w["item"]->getParsedAction(); ?>"><?=$w["item"]->getTitle()?></a></div>
                        <?php
                            endforeach;
                        endforeach;
                        ?>
                    </nav>
                </div>

                <div id="col-content">
                    <?php if(!empty($this->getDebug())): ?>
                    <div id="debug"><?=$this->getDebug() ?></div>
                    <?php endif; ?>

                    <h1><?=$this->getPage()->getTitle(); 
                    if(!empty($this->getPage()->getSubtitle())): 
                        ?> <small><?=$this->getPage()->getSubtitle()?></small><?php 
                    endif; ?></h1>

                    <?=$this->getPage()->output() ?> 
                </div>
            </div>

            <div id="row-bottom">	
                <div id="row-bottom-copyright"><?=$this->getCopyright() ?></div>
                <div id="row-bottom-pagegen"><?=$this->getPagegen(); ?></div>
            </div>
        </div>
    </body>
</html>