<?php

namespace App\Console;


class Kernel extends \Core\Console\Kernel
{

    function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
