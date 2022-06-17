<!DOCTYPE html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
        <div class="toast-header">
            <svg class="rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                <rect fill="#007aff" width="100%" height="100%" />
            </svg>
            <strong class="mr-auto">Bootstrap</strong>
            <small class="text-muted">just now</small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
        </div>
    </div>
    <div class="position-static">
        <row class="d-inline-flex align-items-baseline">
                <h1 class="align-bottom" id="model"> Anycubic </h1>
                <p class="align-bottom" id="firmware"></p>
        </row>

    </div>
    <div class="block">
        <div id="box2" class='title'><span>status: <span id="status"></span> <span id="file"></span> <span id="total_volume"></span></span></div>
        <div><span>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" id="progress-bar"><span id="percent_complete"></span></progress></div>
            </span>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button type="button" onclick="action(this)" id='print' class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play" viewBox="0 0 16 16">
                        <path d="M10.804 8 5 4.633v6.734L10.804 8zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696l6.363 3.692z">
                        </path>
                    </svg>
                </button>
                <button type="button" id='pause' onclick="action(this)" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pause" viewBox="0 0 16 16">
                        <path d="M6 3.5a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5zm4 0a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5z">
                        </path>
                    </svg>
                </button>
                <button type="button" id='stop' onclick="action(this)" class="btn btn-danger">
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

            <div class="col-md-4 text-center">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">layers</th>
                            <td> <span><span id="current_layer"></span>/<span id="total_layers"></span></span> </td>

                        </tr>
                        <tr>
                            <th scope="col">layer height</th>
                            <td id="layer_height"></td>

                        </tr>
                        <tr>

                            <th scope="col">remaining</th>
                            <td id="seconds_remaining"></td>

                        </tr>
                        <tr>

                            <th scope="col">elapsed</th>
                            <td id="elapsed"></td>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>

                        </tr>
                    <tbody>
                </table>
            </div>
        </div>
    </div>
    



    <div id='window1' class='window'>
        <div id="box1" class='title'>camera feed</div>
        <img id="camerabox" src=http://192.168.1.251:8080/?action=stream&1647305721063 alt="Your browser may be blocking insecure content on a secure page.  You can allow insecure content, or disable the camera." />

    </div>
    </div>
</body>

<script>


</script>
<script>
    <?php
    $config = [];
    include_once('config.inc.php');
    print "const ip=\"" . $config['MONO_X_IP'] . "\";\n";
    print "const port=\"" . $config['MONO_X_PORT'] . "\";\n";
    print "const camera=\"" . $config['MONO_X_CAMERA'] . "\";\n";
    print "const usecamera=\"" . $config['MONO_X_USE_CAMERA'] . "\";\n";
    ?>
    if (usecamera != "true" && usecamera != "1"){
        document.getElementById("window1").hidden=true;

    }
    document.getElementById("camerabox").setAttribute("src", camera);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="js/script.js"></script>
<script src="js/ui.js"></script>