<?php

require "bootstrap/bootstrap.php";

return Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);