selectedFile = "";
selectedFileName = "";
secondsRemaining = 0;
secondsElapsed = 0;
state = {};

function onSelect(item) {
  selectedFileName = item.selectedOptions[0].innerHTML;
  selectedFile = item.value;
  document.getElementById("selected").innerHTML = this.selectedFileName;
  doApiCall("getPreview2," + "60.pwmb" + ",end", function (err, data) {
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
        for (key in updateItem) {
          if (key == "status") manageStates(data[item]);
          if (key == "file") {
            updateItem.internalName = updateItem[key].pop();
          }
          if (key == "monox") {
            continue;
          }
          if (key == "percent_complete") {
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
          } else if (key == "seconds_remaining" || key == "elapsed") {
            var date = new Date(null);
            date.setSeconds(updateItem[key]); // specify value for SECONDS here
            ele = document.getElementById(key);
            if (ele != null)
              ele.innerHTML = date.toISOString().substring(11, 19);
          } else {
            ele = document.getElementById(key);
            if (ele != null) ele.innerHTML = updateItem[key];
          }
        }
      }
    }
  }
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
function manageStates(item) {
  progbar = document.getElementById("progress-bar");
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
  refreshFiles();
  await sleep(3000);
  getStatus();
  await sleep(3000);
  getSysInfo();
  await sleep(3000);

  executeAsync(doTenSecondRefresh);
}

async function doTimerUpdates() {
  progbar = document.getElementById("progress-bar");
  var date = new Date(null);
  if (state.status != null &&  state.status.status == "print") {
   
    if (secondsRemaining != null) {
      secondsRemaining--;
      date.setSeconds(secondsRemaining); // specify value for SECONDS here
      elapsedele = document.getElementById("remaining");
      remainingele = document.getElementById("seconds_remaining");
      remainingele.innerHTML = date.toISOString().substring(11, 19);

    }
    if (secondsElapsed != null) {
      secondsElapsed++;
      date.setSeconds(secondsElapsed); // specify value for SECONDS here
      elapsedele = document.getElementById("elapsed");
      elapsedele.innerHTML = date.toISOString().substring(11, 19);
    }
  } 
  await sleep(1000);
  executeAsync(doTimerUpdates);
}

executeAsync(doTenSecondRefresh);
executeAsync(doTimerUpdates);
