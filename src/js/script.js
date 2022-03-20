selectedFile = "";
selectedFileName = "";

state = {};

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

function onSelect(item) {
  selectedFileName = item.selectedOptions[0].innerHTML;
  selectedFile = item.value;
  document.getElementById("selected").innerHTML = this.selectedFileName;
}

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

function refreshFiles() {
  list = document.getElementById("printlist");
  doApiCall("getfile", function (err, data) {
    mergeState(data);
    if (err !== null) {
      alert("Something went wrong: " + err);
    } else {
      for (var item in data.files) {
        if (item == "end") {
          continue;
        }
        var opt = document.createElement("option");
        opt.value = data.files[item][1];
        opt.innerHTML = data.files[item][0];
        list.appendChild(opt);
      }
    }
  });
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
    case "print":
      enablePrint(true);
      enablePause(true);
      enableStop(true);
      break;
    case "stop":
      enablePrint(true);
      enablePause(false);
      enableStop(false);
      break;
    case "pause":
      enablePrint(true);
      enablePause(false);
      enableStop(true);
      break;
    case "finished":
      enablePrint(false);
      enablePause(false);
      enableStop(false);
      break;
  }
}

var callback = function handleResults(err, data) {
  mergeState(data);
  if (err !== null) {
    alert("Something went wrong: " + err);
  } else {
    for (var item in data) {
      if (item == "end") {
        continue;
      }
      if (item == "status") {
        manageStates(data[item]);
      }
      ele = document.getElementById(item);
      if (ele != null) ele.innerHTML = data[item];
    }
  }
};

function getStatus() {
  doApiCall("getstatus", callback);
}

function getSysInfo() {
  doApiCall("sysinfo", callback);
}

function executeAsync(func) {
  setTimeout(func, 0);
}

function doTenSecondRefresh() {
  console.log("status");
  refreshFiles();
  sleep(1000);
  getSysInfo();
  sleep(500);
  getStatus();
  setTimeout(doTenSecondRefresh, 15000);
}



function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

function doSlowInitial() {
  refreshFiles();
  sleep(1000);
  getSysInfo();

  sleep(1000);
  getStatus();
  sleep(1000);
  getSysInfo();
  executeAsync(doTenSecondRefresh());
}
doSlowInitial();
