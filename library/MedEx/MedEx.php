<?php

/**
 * /library/MedEx/MedEx.php
 *
 * This file is the callback service for MedEx
 *
 * Copyright (C) 2017 MedEx <support@MedExBank.com>
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package LibreHealth EHR
 * @author MedEx <support@MedExBank.com>
 * @link http:LibreHealth.io
 */
error_reporting(0);

$ignoreAuth=true;
$_SERVER['REQUEST_URI'] = '';
$_SERVER['SERVER_NAME'] = 'example.tdl'; //PUT your fqdn - server name here
$_SERVER['HTTP_HOST']   = 'default'; //for multi-site i believe

require_once(dirname(__FILE__)."/../../interface/globals.php");
require_once(dirname(__FILE__)."/../patient.inc");
require_once(dirname(__FILE__)."/../log.inc");
require_once(dirname(__FILE__)."/../formatting.inc.php");
require_once(dirname(__FILE__)."/API.php");

$hb = new MedExApi\MedEx('MedExBank.com');
$logged_in = $hb->login();
$log = "/tmp/myhipaa.log" ;
$stdlog = fopen($log, 'a');
$timed = date(DATE_RFC2822);
fputs($stdlog,"\n".$timed."\nMedEx.php fired\n");        
if ($logged_in) {
    if (!empty($_POST['callback'])) {
        $data = json_decode($_POST,true);
        $response = $hb->callback->receive($data);
        echo $response;
        exit;
    }
    $token      = $logged_in['token'];
    $response   = $hb->practice->sync($token);
    $campaigns  = $hb->campaign->events($token);
    $response   = $hb->events->generate($token,$campaigns['events']);
} else {
    echo "not logged in";
    echo $hb->getLastError();
}
echo "200";
exit;
?>