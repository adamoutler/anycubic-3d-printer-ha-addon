#!/bin/bash
###
#Inputs - inputs from Home Assistant Config
###
echo "config";
cat /data/options.json;
ip=$(jq -r '."Mono X IP Address"' </data/options.json);
port=$(jq -r '."Mono X Port"' </data/options.json);
useCam=$(jq -r '."Use a Camera"' </data/options.json);
camera=$(jq -r '."Camera URL"' </data/options.json);

###
#Config - adjust server config at startup configuration
###
configFile="/var/www/localhost/htdocs/config.inc.php";
cat << EOF > ${configFile};
<?php
\$config['MONO_X_IP'] = '${ip}';
\$config['MONO_X_PORT'] = '${port}';
\$config['USE_CAMERA'] = '${useCam}';
\$config['MONO_X_USE_CAMERA'] = '${camera}';

EOF
chmod 755 ${configFile};

###
#Start - adjust configuration
###

/usr/sbin/httpd -DFOREGROUND;
