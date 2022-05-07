out="/app/config.inc.php";

echo '<?php'>${out};

if [ -z ${MONO_X_IP} ]; then
    echo "\$config['MONO_X_IP'] = '192.168.1.254';" >>${out}
else
    echo "\$config['MONO_X_IP'] = '"${MONO_X_IP}"';" >>${out}
fi
if [ -z ${MONO_X_PORT} ]; then
    echo "\$config['MONO_X_PORT'] = '6000';" >>${out}
else
    echo "\$config['MONO_X_PORT'] = '"${MONO_X_PORT}"';" >>${out}
fi
if [ -z ${MONO_X_CAMERA} ]; then
    echo "\$config['MONO_X_CAMERA'] = '';" >>${out}
else
    echo "\$config['MONO_X_CAMERA'] = '"${MONO_X_CAMERA}"';" >>${out}
fi
