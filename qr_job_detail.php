<?php

//include only that one, rest required files will be included from it
include "phpqrcode/qrlib.php";

QRcode::png('http://www.sjs.co.nz/job/'.(int)$_GET['id'], false, 'L');

