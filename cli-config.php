<?php

require_once "bootstrap/bootstrap.php";


return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(app('database.manage'));
