selectedFile = "";
selectedFileName = "";

state = {};

function onSelect(item) {
  selectedFileName = item.selectedOptions[0].innerHTML;
  selectedFile = item.value;
  document.getElementById("selected").innerHTML = this.selectedFileName;
}

function removeOptions(selectElement) {
  var i, L = selectElement.options.length - 1;
  for(i = L; i >= 0; i--) {
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
          ele = document.getElementById(key);
          if (ele != null) ele.innerHTML = updateItem[key];
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
  if ("status" in item)
    switch (item.status) {
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

function executeAsync(func) {
  setTimeout(func);
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function doTenSecondRefresh() {
  refreshFiles();
  await sleep(1000);
  getStatus();
  await sleep(1000);
  getSysInfo();
  await sleep(1000);

  executeAsync(doTenSecondRefresh());
}



executeAsync(doTenSecondRefresh);