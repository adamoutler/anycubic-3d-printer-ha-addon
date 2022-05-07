#Define out output file. this is where the app gets information from. 
out="/app/config.inc.php";
echo '<?php'>${out};

#test each variable and write an output to the output file
if [ -z "${MONO_X_IP+x}" ]; then
    echo "\$config['MONO_X_IP'] = '192.168.1.254';" >>"${out}"
else
    echo "\$config['MONO_X_IP'] = ${MONO_X_IP};" >>"${out}"
fi
if [ -z "${MONO_X_PORT+x}" ]; then
    echo "\$config['MONO_X_PORT'] = '6000';" >>"${out}"
else
    echo "\$config['MONO_X_PORT'] = ${MONO_X_PORT};" >>"${out}"
fi
if [ -z "${MONO_X_CAMERA+x}" ]; then
    echo "\$config['MONO_X_CAMERA'] = '';" >>${out}
else
    echo "\$config['MONO_X_CAMERA'] = ${MONO_X_CAMERA};" >>${out}
fi
