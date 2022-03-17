ip = "192.168.1.254"
port = "6000"

selectedFile = "";
selectedFileName = "";

state = {}

function print() {
    document.getElementById("print").classList.add("disabled")
    if (selectedFile != "") {
        doApiCall('goprint,' + selectedFile + ',end', function(err, data) {
            //do stuff here.
        })
    }

}

function pause() {
    document.getElementById("pause").classList.add("disabled")
    doApiCall('gopause,' + selectedFile + ',end', function(err, data) {
        //do stuff here.
    })
}

function stop() {
    document.getElementById("stop").classList.add("disabled")
    doApiCall('gostop,' + selectedFile + ',end', function(err, data) {
        //do stuff here.
    })
}

function onSelect(item) {
    selectedFileName = item.selectedOptions[0].innerHTML;
    selectedFile = item.value;
    document.getElementById("selected").innerHTML = this.selectedFileName
}





function action(ele) {
    var id = ele.id;
    switch (id) {
        case "print":
            console.log('print');
            print();
            break;
        case "pause":
            console.log('pause');
            pause();
            break;
        case "stop":
            console.log("stop");
            stop();
            break;
        case "printlist":
            console.log("printlist");
            break;
    }

}

function dragElement(elmnt) {
    var pos1 = 0,
        pos2 = 0,
        pos3 = 0,
        pos4 = 0;
    if (document.getElementById(elmnt.id + "header")) {
        // if present, the header is where you move the DIV from:
        document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
    } else {
        // otherwise, move the DIV from anywhere inside the DIV:
        elmnt.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        console.log(e)
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position:


        elmnt.parentElement.style.top = (elmnt.parentElement.offsetTop - pos2) + "px";
        elmnt.parentElement.style.left = (elmnt.parentElement.offsetLeft - pos1) + "px";
        elmnt.parentElement.classList.remove("right")
        elmnt.parentElement.classList.remove("bottom")

    }

    function closeDragElement() {
        // stop moving when mouse button is released:
        document.onmouseup = null;
        document.onmousemove = null;
    }
}


function mergeState(newstates) {
    const result = {};
    let key;

    for (key in state) {
        if (state.hasOwnProperty(key)) {
            result[key] = state[key];
        }
    }

    for (key in newstates) {
        if (newstates.hasOwnProperty(key)) {
            result[key] = newstates[newstates];
        }
    }
}


var doApiCall = function(command, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'api.php?server=' + ip + '&port=' + port + '&cmd=' + command, true);
    xhr.responseType = 'json';
    xhr.onload = function() {
        var status = xhr.status;
        if (status === 200) {
            callback(null, xhr.response);
        } else {
            callback(status, xhr.response);
        }
    };
    xhr.send();
};

function refreshFiles() {
    list = document.getElementById("printlist");
    doApiCall('getfile',
        function(err, data) {
            mergeState(data)
            if (err !== null) {
                alert('Something went wrong: ' + err);
            } else {
                for (var item in data.files) {
                    if (item == "end") {
                        continue;
                    }
                    var opt = document.createElement('option');
                    opt.value = data.files[item][1];
                    opt.innerHTML = data.files[item][0];
                    list.appendChild(opt);
                }
            }
        });
}

function enablePrint(value) {
    enableButton("print", value)
}

function enablePause(value) {
    enableButton("pause", value)
}

function enableStop(value) {
    enableButton("stop", value)
}

function enableButton(buttonName, value) {
    ele = document.getElementById(buttonName);
    if (value) {
        ele.classList.remove("disabled");
    } else {
        ele.classList.add("disabled");
    }
}

function manageStates(item) {
    switch (item) {
        case ("print"):
            enablePrint(true)
            enablePause(true)
            enableStop(true)
            break;
        case ("stop"):
            enablePrint(true)
            enablePause(false)
            enableStop(false)
            break;
        case ("pause"):
            enablePrint(true)
            enablePause(false)
            enableStop(true)
            break;
        case ("finished"):
            enablePrint(false)
            enablePause(false)
            enableStop(false)
            break;
    }
}

function getStatus() {
    doApiCall('getstatus',
        function(err, data) {
            mergeState(data)
            if (err !== null) {
                alert('Something went wrong: ' + err);
            } else {
                for (var item in data) {
                    if (item == "end") {
                        continue;
                    }
                    if (item == "status")
                        manageStates(data[item])
                    ele = document.getElementById(item)
                    if (ele != null) ele.innerHTML = data[item];
                }
            }
        });
}

function getSysInfo() {
    doApiCall('sysinfo',
        function(err, data) {
            mergeState(data)
            if (err !== null) {
                alert('Something went wrong: ' + err);
            } else {
                for (var item in data) {
                    if (item == "end") {
                        continue;
                    }
                    if (item == "status")
                        manageStates(data[item])
                    ele = document.getElementById(item)
                    if (ele != null) ele.innerHTML = data[item];
                }
            }
        });
}

// Make the DIV element draggable:
dragElement(document.getElementById("box1"));
dragElement(document.getElementById("box2"));
dragElement(document.getElementById("box3"));


function executeAsync(func) {
    setTimeout(func, 0);
}

function doTenSecondRefresh() {
    console.log("status");
    getSysInfo()
    sleep(500)
    getStatus();
    setTimeout(doTenSecondRefresh, 15000);
}

function doSixtySeocondRefresh() {
    // do whatever you like here
    console.log("files");
    refreshFiles()
    setTimeout(doFifteenSecondRefresh, 60000);
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function doSlowInitial() {
    getStatus();
    sleep(500);

    getSysInfo();
    sleep(500);
    getSysInfo();
    sleep(500);
    refreshFiles()
    sleep(500);
    getStatus()


}
executeAsync(doTenSecondRefresh());
executeAsync(doSixtySeocondRefresh());