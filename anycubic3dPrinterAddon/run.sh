#!/bin/bash
###
#Inputs - inputs from Home Assistant Config
###
ips=$(bashio::config 'Mono X IP Addresses')
ports=$(bashio::config 'Mono X Ports')
useCam=$(bashio::config 'Use a Camera')
cameras=$(bashio::config 'Camera URLs')

###
#Config - adjust server config at startup configuration
###
configFile="/var/www/localhost/htdocs/config.inc.php"
cat << EOF > ${configFile}
<?php
\$config['MONO_X_IP'] = '${ips}';
\$config['MONO_X_PORT'] = '${ports}';
\$config['USE_CAMERA'] = '${useCam}';
\$config['MONO_X_CAMERA'] = '${cameras}';

EOF
chmod 755 ${configFile}

###
#Start - adjust configuration
###
while true; do sleep 600; done;