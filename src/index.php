<!DOCTYPE html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/32x32.webp" type="image/webp" />
</head>

<body>
    <div id="header" class="position-static heading-box">
        <row class="d-inline-flex">
            <img class="header-image" src="img/anycubic.jpg" />
            <div class="d-flex-column">
                <span>
                    <span class="header-title align-bottom display-5" id="model"> Anycubic </span>
                </span>
                <div>
                    <span class="header-text align-bottom lead" id="firmware"></span>
                    <span class="align-bottom" id="wifi"></span>
                </div>
            </div>
        </row>

    </div>
    <div class="block">
        <div id="box2" class='title'><span><span class="lead"> Status:</span> <span id="status"></span> <span id="file"></span> <span id="total_volume"></span></span></div>
        <div><span>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" id="progress-bar"><span id="percent_complete"></span></progress></div>
            </span>
        </div>
        <div class="row">
            <div class="col-sm">
                <button type="button" onclick="doAction(this)" id='print' class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play" viewBox="0 0 16 16">
                        <path d="M10.804 8 5 4.633v6.734L10.804 8zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696l6.363 3.692z">
                        </path>
                    </svg>
                </button>
                <button type="button" id='pause' onclick="doAction(this)" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pause" viewBox="0 0 16 16">
                        <path d="M6 3.5a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5zm4 0a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5z">
                        </path>
                    </svg>
                </button>
                <button type="button" id='stop' onclick="doAction(this)" class="btn btn-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stop" viewBox="0 0 16 16">
                        <path d="M3.5 5A1.5 1.5 0 0 1 5 3.5h6A1.5 1.5 0 0 1 12.5 5v6a1.5 1.5 0 0 1-1.5 1.5H5A1.5 1.5 0 0 1 3.5 11V5zM5 4.5a.5.5 0 0 0-.5.5v6a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5V5a.5.5 0 0 0-.5-.5H5z">
                        </path>
                    </svg>
                </button>
                <div>
                    <span id='selected'></span>
                </div>
                <div>
                    <select id="printlist" name="toprint" onchange="onSelect(this);" size="10">
                    </select>
                </div>
            </div>
            <div class="col justify-content-sm-center">
                <br>
                <img id="activeImage" style="max-height:180px;"></img>
            </div>
            <div class="col-sm text-center p-3 w-100">
                <div class="tab">
                    <button class="tablinks" onclick="openInfoTab(event, 'cur')">Current Info</button>
                    <button class="tablinks" onclick="openInfoTab(event, 'current_params')">Current Parameters</button>
                    <button class="tablinks" onclick="openInfoTab(event, 'previous_history')">History</button>

                </div>
                <div>
                    <table id="current_params" class="table tabcontent">
                        <tr>
                            <th>Bottom Layer Count</th>
                            <td id="BottomLayerCount"></td> <td>#</td>
                        </tr>
                        <tr>
                            <th>Bottom Exposure</th>
                            <td id="BottomExposureSeconds"></td><td>s</td>
                        </tr>
                        <tr>
                            <th>Off Time</th>
                            <td id="ExposureOffTime"></td><td>s</td>
                        </tr>
                        <tr>
                            <th>Bottom Layer Rising Height</th>
                            <td id="BottomLayer0RisingHeightMM"></td><td>mm</td>
                        </tr>
                        <tr>
                            <th>Bottom Layer Rising Speed</th>
                            <td id="BottomLayer0RisingSpeedMMperSec"></td><td>mm/s</td>
                        </tr>
                        <tr>
                            <th>Bottom layer Retract Speed</th>
                            <td id="BottomLayer0RetractSpeedMMperSec"></td><td>mm/s</td>
                        </tr>
                        <tr>
                            <th>Bottom Layer 2nd Rising Height</th>
                            <td id="BottomLayer1RisingHeightMM"></td><td>mm</td>
                        </tr>
                        <tr>
                            <th>Bottom Layer 2nd Rising Speed</th>
                            <td id="BottomLayer1RisingSpeedMMperSec"></td><td>mm/s</td>
                        </tr>
                        <tr>
                            <th>Bottom Layer 2nd Retract Speed</th>
                            <td id="BottomLayer1RetractSpeedMMperSec"></td><td>mm/s</td>
                        </tr>
                        <tr>
                            <th>Transition Layer Count</th>
                            <td id="TransitionLayerCount"></td><td>#</td>
                        </tr>
                        <tr>
                            <th>Normal Exposure Time</th>
                            <td id="NormalExposureSeconds"></td><td>s</td>
                        </tr>
                        <tr>
                            <th>Normal Layer Rising Height</th>
                            <td id="NormalLayer0RisingHeightMM"></td><td>mm</td>
                        </tr>
                        <tr>
                            <th>Normal Layer Rising Speed</th>
                            <td id="NormalLayer0RisingSpeedMMperSec"></td><td>mm/s</td>
                        </tr>
                        <tr>
                            <th>Normal Layer Retract Speed</th>
                            <td id="NormalLayer0RetractSpeedMMperSec"></td><td>mm/s</td>
                        </tr>
                        <tr>
                            <th>Normal Layer 2nd Rising Height</th>
                            <td id="NormalLayer1RisingHeightMM"></td><td>mm</td>
                        </tr>
                        <tr>
                            <th>Normal Layer 2nd Rising Speed</th>
                            <td id="NormalLayer1RisingSpeedMMperSec"></td><td>mm/s</td>
                        </tr>
                        <tr>
                            <th>Normal Layer 2nd Retract Speed</th>
                            <td id="NormalLayer1RetractSpeedMMperSec"></td><td>mm/s</td>
                        </tr>
                    </table>
                    
                    <table id="cur" class="table tabcontent ">
                        <thead class="thead-dark ">
                            <tr>
                                <th class="lead" scope="col">layers</th>
                                <td> <span><span id="current_layer"></span>/<span id="total_layers"></span></span> </td>

                            </tr>
                            <tr>
                                <th class="lead" scope="col">layer height</th>
                                <td id="layer_height"></td>

                            </tr>
                            <tr>

                                <th class="lead" scope="col">remaining</th>
                                <td id="seconds_remaining"></td>

                            </tr>
                            <tr>

                                <th class="lead" scope="col">elapsed</th>
                                <td id="elapsed"></td>

                            </tr>
                        </thead>
                    </table>
                    <table id="previous_history" class="table tabcontent ">
                        <tr>
                            <th class="lead" scope="col">afesasdfasd</th>
                            <th class="lead" scope="col">afesasdfasd</th>
                            <th class="lead" scope="col">afesasdfasd</th>
                            <th class="lead" scope="col">afesasdfasd</th>
                            <th class="lead" scope="col">afesasdfasd</th>
                            <th class="lead" scope="col">afesasdfasd</th>
                            <th class="lead" scope="col">afesasdfasd</th>
                            <th class="lead" scope="col">afesasdfasd</th>
                            <th class="lead" scope="col">afesasdfasd</th>

                        </tr>
                       
                    </table>
                </div>
            </div>
        </div>
    </div>




    <div id='window1' class='window'>
        <div id="box1" class='title'><span class="lead">Live Feed</span></div>
        <img id="camerabox" src=http://192.168.1.251:8080/?action=stream&1647305721063 alt="Your browser may be blocking insecure content on a secure page.  You can allow insecure content, or disable the camera." />

    </div>
    </div>
    <div style="z-index:100; position: relative;  bottom: 0; width: 100%; height: auto;">
        <div style="position: absolute; bottom: 0;" id="online">🟢<span id=lastaction>connecting...</span></div>
        <span style="position: absolute; bottom: 0;" id="offline">🔴</span>
        <div class="text p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        </div>



    </div>
</body>
<script async src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script async src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script async defer>
    <?php
    $config = [];
    include_once 'config.inc.php';
    print "const ip=\"" . $config['MONO_X_IP'] . "\";\n";
    print "const port=\"" . $config['MONO_X_PORT'] . "\";\n";
    print "const camera=\"" . $config['MONO_X_CAMERA'] . "\";\n";
    print "const usecamera=\"" . $config['MONO_X_USE_CAMERA'] . "\";\n";
    ?>
    if (usecamera.toLowerCase() != "true" &&  usecamera != "1") {
        document.getElementById("window1").hidden = true;
    }
    document.getElementById("camerabox").setAttribute("src", camera);
</script>

<script async src="js/ui.js"></script>
<script src="js/script.js"></script>