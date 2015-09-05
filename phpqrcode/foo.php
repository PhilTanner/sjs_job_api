<?php
//include only that one, rest required files will be included from it
include "qrlib.php";

//write code into file, Error corection lecer is lowest, L (one form: L,M,Q,H)
//each code square will be 4x4 pixels (4x zoom)
//code will have 2 code squares white boundary around 

//QRcode::png('PHP QR Code :)', 'test.png', 'L', 4, 2);
QRcode::png('http://www.sjs.co.nz/job/1010210101/apply/', false, 'L', 4, 2);

