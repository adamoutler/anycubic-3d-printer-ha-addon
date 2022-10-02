selectedFile = "";
selectedFileName = "";
secondsRemaining = 0;
secondsElapsed = 0;
state = {};
lastLayer = "";
layerUpdate = false;

function onSelect(item) {
  document.getElementById("activeImage").setAttribute("src", "img/loading.gif");
  selectedFileName = item.selectedOptions[0].innerHTML;
  selectedFile = item.value;
  document.getElementById("selected").innerHTML = "▶️ <strong>"+this.selectedFileName+"</strong>";
  doImageCall(selectedFile, function (err, data) {
    document.getElementById("activeImage").setAttribute("src", "");
    if (data == null) {
      return;
    }
    value = data.replace(/([^w]*)}/g, "");
    if (value == null || !value.includes(".png")) {
     document.getElementById("activeImage").setAttribute("src", "");
     document.getElementById("activeImage").setAttribute("alt", "❌🖼️");
     
      return;
    }
    document.getElementById("activeImage").setAttribute("src", value);
    //do stuff here.
  });
}

function removeOptions(selectElement) {
  var i,
    L = selectElement.options.length - 1;
  for (i = L; i >= 0; i--) {
    selectElement.remove(i);
  }
}

var callback = function handleResults(err, data) {
  fadeInOffline(data);
  progbar = document.getElementById("progress-bar");
  mergeState(data);
  if (err !== null) {
    alert("Something went wrong: " + err);
  } else {
    if (data == null) {
      return;
    }
    if ("files" in data) {
      removeOptions(list);
      for (item in data.files) {
        if (item.startsWith("end")) {
          item = item.replace("end", "");
          break;
        }
        var opt = document.createElement("option");
        opt.value = data.files[item][1];
        opt.innerHTML = data.files[item][0];
        list.appendChild(opt);
      }
    }
    for (var item in data) {
      this.updateItem = null;
      if (item == "end") {
        continue;
      }
      if (item == "status" || "sysinfo") {

        this.updateItem = data[item];

        if (
          item == "status" &&
          (cur_layer = this.updateItem["current_layer"]) != null
        ) {
          if (this.lastLayer != cur_layer) {
            this.layerUpdate = true;
            lastLayer = cur_layer;
          }
        }
        for (key in updateItem) {
          switch (key){
              case "file":
                updateItem.internalName = updateItem[key].pop();
                break;
              case "monox":
                continue;
              case "status":
                manageStates(data[item]);
                break;
              case "percent_complete":
                ele = document.getElementById("progress-bar");
                ele.setAttribute("aria-valuenow", updateItem[key]);
                if (updateItem[key] < 10) {
                  ele.setAttribute("style", "width: 5%");
                } else {
                  ele.setAttribute("style", "width: " + updateItem[key] + "%");
                }
                if (ele != null) {
                  ele.innerHTML =
                    '<span class="sr-only">' +
                    updateItem[key] +
                    "% complete </span>";
                }
              case "seconds_remaining":
                if (this.layerUpdate){
                  secondsRemaining = updateItem[key];
                }
              case "elapsed":
                if (this.layerUpdate){
                  secondsElapsed = updateItem[key];
                }
              default: 
                ele = document.getElementById(key);
                if (ele != null) ele.innerHTML = updateItem[key];
          }
        }
      }
    }
    layerUpdate = false;
  }
};

var doImageCall = function (command, callback) {
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "getImage.php?server=" + ip + "&port=" + port + "&file=" + command,
    true
  );
  xhr.responseType = "";
  xhr.onload = function () {
    var status = xhr.status;
    if (status === 200) {
      callback(null, xhr.response);
    } else {
      callback(status, xhr.response);
    }
  };
  xhr.send();
};

var doApiCall = function (command, callback) {
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "api.php?server=" + ip + "&port=" + port + "&cmd=" + command,
    true
  );
  xhr.responseType = "json";
  xhr.onload = function () {
    var status = xhr.status;
    if (status === 200) {
      callback(null, xhr.response);
    } else {
      callback(status, xhr.response);
    }
  };
  xhr.send();
};

function action(ele) {
  var id = ele.id;
  switch (id) {
    case "print":
      console.log("print");
      print();
      break;
    case "pause":
      console.log("pause");
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

function mergeState(newstates) {
  for (key in newstates) {
    state[key] = newstates[key];
  }
}

function print() {
  document.getElementById("print").classList.add("disabled");
  if (selectedFile != "") {
    doApiCall("goprint," + selectedFile + ",end", function (err, data) {
      //do stuff here.
    });
  }
}

function pause() {
  document.getElementById("pause").classList.add("disabled");
  doApiCall("gopause," + selectedFile + ",end", function (err, data) {
    //do stuff here.
  });
}

function stop() {
  document.getElementById("stop").classList.add("disabled");
  doApiCall("gostop," + selectedFile + ",end", function (err, data) {
    //do stuff here.
  });
}

function enableButton(buttonName, value) {
  ele = document.getElementById(buttonName);
  if (value) {
    ele.classList.remove("disabled");
  } else {
    ele.classList.add("disabled");
  }
}

function enablePrint(value) {
  enableButton("print", value);
}

function enablePause(value) {
  enableButton("pause", value);
}

function enableStop(value) {
  enableButton("stop", value);
}

function fadeInOffline(item) {
  action = document.getElementById("lastaction");
  if (item == null) {
    return;
  }
  item[(action.textContent = Object.keys(item)[1])];
  offline = document.getElementById("offline");
  offline.classList.remove("fade-in-div");
  offline.parentNode.replaceChild(offline, offline);
  offline.classList.add("fade-in-div");
}
function manageStates(item) {
  switch (item.status) {
    case "print":
      enablePrint(false);
      enablePause(true);
      enableStop(true);
      break;
    case "stop":
      progbar.setAttribute("aria-valuenow", "-1");
      progbar.setAttribute("style", "width: 0%");
      enablePrint(true);
      enablePause(false);
      enableStop(false);
      break;
    case "pause":
      enablePrint(true);
      enablePause(false);
      enableStop(true);
      break;
    case "finish":
      enablePrint(false);
      enablePause(false);
      enableStop(false);
      progbar.setAttribute("aria-valuenow", "100");
      progbar.setAttribute("style", "width: 100%");
      break;
  }
}

function refreshFiles() {
  list = document.getElementById("printlist");
  doApiCall("getfile", callback);
}

function getStatus() {
  doApiCall("getstatus", callback);
}

function getSysInfo() {
  doApiCall("sysinfo", callback);
}

async function executeAsync(func) {
  setTimeout(func);
}

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function doTenSecondRefresh() {
  await sleep(1000);
  if (!state.sysinfo) {
    getSysInfo();
  }
  await sleep(1000);
  if (!state.files) {
    refreshFiles();
  }
  await sleep(1000);
  getStatus();
  await sleep(6000);

  executeAsync(doTenSecondRefresh);
}

async function doTimerUpdates() {
  progbar = document.getElementById("progress-bar");

  if (state.status != null && state.status.status == "print") {
    if (secondsRemaining != null) {
      var date = new Date(null);
      secondsRemaining--;
      date.setSeconds(secondsRemaining); // specify value for SECONDS here
      remainingele = document.getElementById("seconds_remaining");
      remainingele.innerHTML = date.toISOString().substring(11, 19);
    }
    if (secondsElapsed != null) {
      var date = new Date(null);
      secondsElapsed++;
      date.setSeconds(secondsElapsed); // specify value for SECONDS here
      elapsedele = document.getElementById("elapsed");
      elapsedele.innerHTML = date.toISOString().substring(11, 19);
    }
  }
  await sleep(1000);
  executeAsync(doTimerUpdates);
}
getStatus();

executeAsync(doTenSecondRefresh);
executeAsync(doTimerUpdates);
