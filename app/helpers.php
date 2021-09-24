<?php

use Carbon\Carbon;


    function convertDateToMysql($date)
    {
        return Carbon::parse($date)->toDateString();
    }
