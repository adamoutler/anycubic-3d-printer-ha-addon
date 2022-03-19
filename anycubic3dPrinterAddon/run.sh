#!/bin/bash
###
#Inputs - inputs from Home Assistant Config
###
echo "config";
cat /data/options.json;
ips=$(jq -r '."Mono X IP Addresses"' </data/options.json);
ports=$(jq -r '."Mono X Ports"' </data/options.json);
useCam=$(jq -r '."Use a Camera"' </data/options.json);
cameras=$(jq -r '."Camera URLs"' </data/options.json);

###
#Config - adjust server config at startup configuration
###
configFile="/var/www/localhost/htdocs/config.inc.php";
cat << EOF > ${configFile};
<?php
\$config['MONO_X_IP'] = '${ips}';
\$config['MONO_X_PORT'] = '${ports}';
\$config['USE_CAMERA'] = '${useCam}';
\$config['MONO_X_CAMERA'] = '${cameras}';

EOF
chmod 755 ${configFile};

###
#Start - adjust configuration
###

/usr/sbin/httpd -DFOREGROUND;
