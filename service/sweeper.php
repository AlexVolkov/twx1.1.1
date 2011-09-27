<?php
$days = 7; //days to keep logs
exec ("find /var/www/planet7/tmp/* -mtime +" .$days. " -exec rm {} \;");
exec ("find /var/www/planet7/cookies/* -mtime +" .$days. " -exec rm {} \;");
exec ("find /var/www/planet7/cronjobs/* -mtime +" .$days. " -exec rm {} \;");
exec ("find /var/www/planet7/uploads/* -mtime +" .$days. " -exec rm {} \;");

?>