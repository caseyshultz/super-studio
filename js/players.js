var KeyCheckActive = true;

function loadList(listId){
  var dname = "/list/?id=" + listId;
  xhttp=new XMLHttpRequest();
  xhttp.open("get",dname,false);
  xhttp.send();
  if(xhttp.readyState==4){
    var htmlDoc= xhttp.responseText;
    parser=new DOMParser();
    xmlDoc=parser.parseFromString(htmlDoc,"text/xml");
    // These are the fields in db for each clip.
    var element = new Array("cid","keybind","type","file","duration","title",
                              "size","lid","delta","prev","next");
    var elementCount = element.length;
    // {i} is the clip key and {j} is the element key
    for(var i = 0; i < xmlDoc.getElementsByTagName("clip").length; i++){
      if(i==0){ // Make new clip Array();
        var clip = Array();
      }
      for(var j = 0; j < elementCount; j++){
        if(j==0){ // Make new element Array()
          var elements = Array();
        }
        elements.push(xmlDoc.getElementsByTagName(element[j])[i].childNodes[0].nodeValue);
      }
      clip.push(elements);
    }
  }
  return clip;
}

// Sends a defined message to a status area.
function logUpdate(message){
  var time = new Date();
  var current = document.getElementById("log-output").innerHTML;
  var timestamp = zeroPad(time.getHours(),2) + ":" +
                  zeroPad(time.getMinutes(),2) + "." +
                  zeroPad(time.getMilliseconds(),3);
  var newmessage = timestamp + " - " + message + "\n" + current;
  document.getElementById("log-output").innerHTML = newmessage;
}

// Sends a rendering of the list to the screen
function dumpList(listId){
  var clip = loadList(listId);
  for(var i=0;i<clip.length;i++){
    for(var j=0;j<clip[0].length;j++){
      document.write('<div>clip[' + i + "][" + j + "] = " + clip[i][j] + '</div>');
    }
  }
}

function loadPlayers(clip){
  var player = Array("zero");
  for(var i=1;i<clip.length;i++){ // Creates the player objects.
    var list = clip[i][7];
    player[i] = new Audio();
    player[i].segue = false; // default
    player[i].volume = 0.0;
    player[i].under = false;
    // the "audio" directory needs to be located directly under the document root
    player[i].setAttribute('src', '/audio/' + clip[i][3]);
    player[i].preload = "auto";
    player[i].sp_cid = clip[i][0];
    player[i].sp_keybind = clip[i][1];
    player[i].sp_type = clip[i][2];
    player[i].sp_file = clip[i][3];
    player[i].sp_duration = clip[i][4];
    player[i].sp_title = clip[i][5];
    player[i].sp_size = clip[i][6];
    player[i].lid = clip[i][7];
    player[i].sp_delta = clip[i][8];
    player[i].sp_prev = clip[i][9];
    player[i].sp_next = clip[i][10];
  }
  return player;
}


// Turns numbers like 3 into 03.
function zeroPad(num,count){
  var numZeropad = num + '';
  while(numZeropad.length < count){
    numZeropad = "0" + numZeropad;
  }
  return numZeropad;
}

function formatTimeTenths(seconds){
  //This calculates the time to the nearest 0.1 second.
  var rounded = Math.floor(seconds*Math.pow(10,1))/Math.pow(10,1);
  var m = parseInt(((rounded) / 60) % 60);
  var s = (rounded % 60);
  formatted = zeroPad(m,2) + ':' + zeroPad(s.toFixed(1),4);
  return formatted;
}

function formatTimeSeconds(seconds){
  //This calculates the time to the nearest second.
  var rounded = Math.floor(seconds);
  var m = parseInt(((rounded) / 60) % 60);
  var s = parseInt(((rounded) % 60));
  formatted = zeroPad(m,2) + ':' + zeroPad(s,2);
  return formatted;
}

function updateRemaining(player){
  player.remaining = (player.sp_duration - player.currentTime);
  if(player.remaining <= 0){
    player.remaining = 0;
  }
  player.remainingID.innerHTML = formatTimeSeconds(player.remaining);
}



function playStop(player){
  if(player.paused == true && player.currentTime == 0){
    if(player.playedOnce == false){
      player.addEventListener("timeupdate", timeListener = function (){
        player.playedOnce=true;
        if(player.ended == true){
          volumeOut(player);
          return;
        }
        else{
          updateRemaining(player);
          // Fix this. Hard coded to 1 second from the end of clip.
          if(player.remaining < 1){
            // Check if the segue option is toggled.
            if(player.segue == true){
              // Check if the next player is not already playing.
              if(document.getElementById(String('row-' + player.sp_next)).getAttribute('class') == 'clip-stopped'){
                // Segue only if this player is not looping.
                if(player.loop == false){
                  document.getElementById(String('play-button-' + player.sp_next)).click();
                  player.seguebutton.click();
                }
              }
            }
          }
        }
      },false); // end of player.addEventListener().
    }
    fireButton(player);
  }
  else{
    // Check if the segue option is toggled.
    if(player.segue == true){
      // Check if the next player is not already playing.
      if(document.getElementById(String('row-' + player.sp_next)).getAttribute('class') == 'clip-stopped'){
        // Segue
        document.getElementById(String('play-button-' + player.sp_next)).click();
        player.seguebutton.click();
        // Turn off the loop if it's on.
        if(player.loop == true){
          loopButton(player);
        }
      }
    }
    volumeOut(player);
  }
}

function loopButton(player){
  if(player.loop == false){
    player.loop=true;
    player.loopbutton.setAttribute('class', 'loop btn btn-warning');
    player.loopbutton.innerHTML = '<i class="icon-repeat icon-white"></i>&rsquo;d';
  }
  else{
    player.loop=false;
    player.loopbutton.setAttribute('class', 'loop btn btn-inverse');
    player.loopbutton.innerHTML = '<i class="icon-repeat icon-white"></i>';
  }
}

function segueButton(player){
  if(player.segue == false){
    player.segue=true;
    player.seguebutton.setAttribute('class', 'segue btn btn-warning');
    player.seguebutton.innerHTML = '<i class="icon-play-circle icon-white"></i>&rsquo;d';
  }
  else{
    player.segue=false;
    player.seguebutton.setAttribute('class', 'segue btn btn-inverse');
    player.seguebutton.innerHTML = '<i class="icon-play-circle icon-white"></i>';
  }
}


function underButtonReset(player,reset){
  if(reset == "deactivate"){
    player.underbutton.setAttribute('class', 'under btn disabled');
    player.underbutton.innerHTML = "Under";
    player.under = false;
  }
  if(reset == "activate"){
    player.underbutton.setAttribute('class', 'under btn btn-inverse');
    player.underbutton.innerHTML = "Under";
    player.under = false;
  }
}

function formStateModify(state){
  var list = loadList(player[1].lid);
  if(state == "start"){
    var result = true;
  }
  if(state == "stop"){
    var result = false;
  }
  for(var i=1;i<list.length;i++){
    if(player[i].keybindentry != undefined){
      player[i].keybindentry.disabled = result;
    }
    if(player[i].titleentry != undefined){
      player[i].titleentry.disabled = result;
    }
    if(player[i].moveupentry != undefined){
      player[i].moveupentry.disabled = result;
    }
    if(player[i].movedownentry != undefined){
      player[i].movedownentry.disabled = result;
    }
    if(player[i].sendtooptionsentry != undefined){
      player[i].sendtooptionsentry.disabled = result;
    }

  }
}


function underButton(player){
  if(player.paused == false){
    
    if(player.under == false){
      player.under = true;
      player.underbutton.setAttribute('class', 'under btn btn-warning');
      player.underbutton.innerHTML = "Over";
      setVolume(player,0.4);
    }
    else{
      player.under = false;
      player.underbutton.setAttribute('class', 'under btn btn-inverse');
      player.underbutton.innerHTML = "Under";
      setVolume(player,1.0);
    }
  }
  else{
    player.underbutton.setAttribute('class', 'under btn disabled');
    player.underbutton.innerHTML = "Under";
  }
}

function setVolume(player,volume){
  var dv = (volume * 100).toFixed(0);
  if(volume <= 0){
    player.volume = 0;
    dv = '000';
    player.row.setAttribute('class', 'clip-stopped');
    player.playbutton.setAttribute('value', 'Play');
  }
  else if(volume >= 1){
    player.volume = 1;
    dv = 100;
  }
  else{
    player.volume = volume;
    if(dv > 9){
      dv = '0' + dv;
    }
    else{
      dv = '00' + dv;
    }
  }
  player.volumebox.innerHTML = 
  
  '<meter value="' + dv + '" min="000" max="100" optimum="100" low="090">' + dv + '</meter>'
  
  ;
}

function fireButton(player){
  upButton(player);
  player.play();
        player.row.setAttribute('class', 'clip-playing');
        player.playbutton.innerHTML = '<i class="icon-stop"></i>';
        player.playbutton.setAttribute('class','play btn btn-warning');
  underButtonReset(player,"activate");
  formStateModify("start");
}

function stopButton(player){
        player.row.setAttribute('class', 'clip-stopped');
        player.playbutton.innerHTML = '<i class="icon-play"></i>';
        player.playbutton.setAttribute('class','play btn btn-success');
  underButtonReset(player,"deactivate");
  formStateModify("stop");
  player.ended=true;
  player.pause();
  player.currentTime = 0;
}

function volumeOut(player){
  var fadetime = 2000; //2000
  var steps = 25; //25
  var smoothness = 1.5;
  var delay = new Array(steps);
  var tv = new Array(steps);
  var stop;
  
  for(var i = 0;i<=steps;i++){
// The total time and steps remain constant
// The volume will be halved on each step
    delay[i] = fadetime/steps * i;
    tv[i] = setTimeout(function(){setVolume(player,player.volume / smoothness)}, delay[i]);
    
    if(i == steps){
      stop = setTimeout(function(){stopButton(player,"volumeOut"); setVolume(player,0)}, fadetime);
    }
  }
}

function upButton(player){
  var initdelay = 10;
  var fadetime = 500;
  var steps = 5;
  var granularity = 1/steps;
  var delay = new Array(steps);
  var tv = new Array(steps);
  /*
  This is slightly different from the volumeOut() loop in that the first
  iteration is an extra bit of silence.
  */
  for(var i = 0;i<=steps;i++){
    if(i==0){
      delay[i] = initdelay;
      tv[i] = setTimeout(function(){setVolume(player,player.volume + granularity)}, delay[i]);
    }
    else{
      delay[i] = (fadetime/steps * i) + initdelay;
      tv[i] = setTimeout(function(){setVolume(player,player.volume + granularity)}, delay[i]);
    }
  }
}


function playerControl(listId){
  var clip = loadList(listId);
  player = loadPlayers(clip);
  // Checks for a delta change and adds a class
  if(clip[0][8] != 0 && clip[0][8] != undefined){
    if(document.getElementById(String('move-up-'+clip[0][8]))){
      document.getElementById(String('move-up-'+clip[0][8])).setAttribute('class', 'move-up btn delta-changed btn-info');
      setTimeout(function(){document.getElementById(String('move-up-'+clip[0][8])).setAttribute('class', 'move-up btn')},2000);
    }
    if(document.getElementById(String('move-down-'+clip[0][8]))){
      document.getElementById(String('move-down-'+clip[0][8])).setAttribute('class', 'move-down btn delta-changed btn-info');
      setTimeout(function(){document.getElementById(String('move-down-'+clip[0][8])).setAttribute('class', 'move-down btn')},2000);
    }
  }
  // Listens for key presses and routes accordingly.
  document.onkeydown = KeyCheck;
  keybinds = Array();
  for(var i=1;i<player.length;i++){
    // Include all the stuff needed for the players in the player objects
    player[i].remainingID =   document.getElementById(String('duration-' + i));
    player[i].row =       document.getElementById(String('row-' + i));
    player[i].remaining = (player[i].sp_duration - player[i].currentTime);
    player[i].volumebox = document.getElementById(String('volume-' + i));
    // Send any markup with default values to display to get started
    // The play and loop buttons are not part of the database so they are
    // created here and put into the "file" column. 
    player[i].playbutton = document.getElementById(String('play-button-' + i));
    player[i].playbutton.setAttribute('onclick','playStop(player['+i+'])');
    player[i].loopbutton = document.getElementById(String('loop-button-' + i));
    player[i].loopbutton.setAttribute('onclick','loopButton(player['+i+'])');
    player[i].seguebutton = document.getElementById(String('segue-button-' + i));
    player[i].seguebutton.setAttribute('onclick','segueButton(player['+i+'])');
    player[i].underbutton = document.getElementById(String('under-button-' + i));
    player[i].underbutton.setAttribute('onclick','underButton(player['+i+'])');
    player[i].remainingID.innerHTML = formatTimeSeconds(player[i].remaining);
    setVolume(player[i],0);
    // Creates an array of keybinds to player ids.
    if(player[i].sp_keybind){
      keybind = player[i].sp_keybind.toUpperCase();
      KeyID=keybind.charCodeAt(0);
      keybinds[KeyID] = player[i].playbutton;
    }
    // Creates an array of forms that can be activated or deactivated.
    player[i].lid = listId;
    player[i].keybindentry = document.getElementById(String('keybind-entry-' + i));
    player[i].titleentry = document.getElementById(String('title-entry-' + i));
    if(document.getElementById(String('move-up-' + i))){
      player[i].moveupentry = document.getElementById(String('move-up-' + i));
    }
    if(document.getElementById(String('move-down-' + i))){
      player[i].movedownentry = document.getElementById(String('move-down-' + i));
    }
    player[i].sendtooptionsentry = document.getElementById(String('send_to_options-' + i));
    player[i].playedOnce = false;
  }
}

function highlight_moved(){
  
}

function KeyCheck(e){
  var KeyID = (window.event) ? event.keyCode : e.keyCode;
  var BindID = String.fromCharCode(KeyID);
  if(KeyCheckActive == true){
    keybinds[KeyID].click();
  }
}
//===================================================================
function move_clip(delta,direction){
  
}


function getTableLabels(json_list){
  var list = [json_list];
  console.log(list);
  for(var i = 0;i<list.length;i++){
    var list_item = list[i];
  }
  var table = document.getElementById(list_item[0].alias + "-table");
  for(var i = 0, row; row = table.rows[i]; i++){
    //table.rows[i].id
    var rtmp = new Array();
    rtmp = table.rows[i].id.split("-");
    cid=rtmp[2];
    var output = "";
    for(var j = 0, col; col = row.cells[j]; j++){
      //row.cells[j].id
      var clip = list[i];
      var ctmp = new Array();
      ctmp = row.cells[j].id.split("-");
      //ctmp[0] : list alias
      //ctmp[1] : delta
      //ctmp[2] : function
      if(ctmp[1] != "0"){
        
        var instruction ="";
        switch(ctmp[2])
        {
        case "delta_up":
          instruction = list_item[i].cid  + " UP";
          break;
        case "delta_down":
          instruction = list_item[i].cid  + " DOWN";
          break;
        case "title":
          instruction = list_item[i].title;
          break;
        case "keybind":
          instruction = list_item[i].keybind;
          break;
        case "play":
          instruction = list_item[i].cid  + " PLAY";
          break;
        case "duration":
          instruction = list_item[i].duration;
          break;
        case "under":
          instruction = list_item[i].cid  + " UNDER";
          break;
        case "volume":
          instruction = list_item[i].cid  + " VOL";
          break;
        case "loop":
          instruction = list_item[i].cid  + " LOOP";
          break;
        case "segue":
          instruction = list_item[i].cid  + " SEGUE";
          break;
        case "send_to_list":
          instruction = list_item[i].cid  + " SENDTO";
          break;
        case "delete":
          instruction = list_item[i].cid  + " X";
          break;
        default:
          
        }
        document.getElementById(row.cells[j].id).innerHTML = instruction;
        
      }
    }
  }
}

