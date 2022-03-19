###
#Inputs - inputs from Home Assistant Config
###
cat options.json
ips=$(jq -r '."Mono X IP Addresses"' <options.json)
ports=$(jq -r '."Mono X Ports"' <options.json)
useCam=$(jq -r '."Use a Camera"' <options.json)
cameras=$(jq -r '."Camera URLs"' <options.json)

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
