<?php
        /* Based on code by Grant Burton @ BURTONTECH.COM (11-30-2008): IP-Proxy-Cluster Fix */
        // http://www.rantburton.com/2008/11/30/fix-for-incorrect-ip-addresses-in-wordpress-comments/
        function checkIP($ip) {
                if (!empty($ip) && ip2long($ip)!== -1 && ip2long($ip)!==false) { // PT added strict return type checking 2012-11-19
                        $private_ips = array (
                                array('0.0.0.0','2.255.255.255'),
                                array('10.0.0.0','10.255.255.255'),
                                array('127.0.0.0','127.255.255.255'),
                                array('169.254.0.0','169.254.255.255'),
                                array('172.16.0.0','172.31.255.255'),
                                array('192.0.2.0','192.0.2.255'),
                                array('192.168.0.0','192.168.255.255'),
                                array('255.255.255.0','255.255.255.255')
                        );

                        foreach ($private_ips as $r) {
                                $min = ip2long($r[0]);
                                $max = ip2long($r[1]);
                                if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
                        }
                        return true;
                } else {
                        return false;
                }
        }

        // Added lots of "isset()"s around checking functions, and a default fallback to false, PT 2012-11-19
        function determineIP() {
                if( isset($_SERVER["HTTP_CLIENT_IP"]) && checkIP($_SERVER["HTTP_CLIENT_IP"])) {
                        return $_SERVER["HTTP_CLIENT_IP"];
                }
                if( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )
                {
                        foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
                                if (checkIP(trim($ip))) {
                                        return $ip;
                                }
                        }
                }
                if(isset($_SERVER["HTTP_X_FORWARDED"]) && checkIP($_SERVER["HTTP_X_FORWARDED"])) {
                        return $_SERVER["HTTP_X_FORWARDED"];
                } elseif (isset($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"]) && checkIP($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"])) {
                        return $_SERVER["HTTP_X_CLUSTER_CLIENT_IP"];
                } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]) && checkIP($_SERVER["HTTP_FORWARDED_FOR"])) {
                        return $_SERVER["HTTP_FORWARDED_FOR"];
                } elseif (isset($_SERVER["HTTP_FORWARDED"])&& checkIP($_SERVER["HTTP_FORWARDED"])) {
                        return $_SERVER["HTTP_FORWARDED"];
                } elseif( isset($_SERVER["REMOTE_ADDR"]) ) {
                        return $_SERVER["REMOTE_ADDR"];
                } else {
                        return false;
                }
        }

        $userIp = determineIP();
        define('USER_IP', $userIp);
        define('USER_IPADDRESS', $userIp);
        unset($userIp);

?>
