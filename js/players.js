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

function formatTime(seconds){
    Math.round(seconds*Math.pow(10,1))/Math.pow(10,1);
    var m = parseInt(((seconds) / 60) % 60);
    var s = parseInt(((seconds) % 60));
    formatted = zeroPad(m,2) + ':' + zeroPad(s,2);
    return formatted;
}

// This is launched from an onclick="playButton(player[i])" attribute
// defined in makeHTML.php.
function playButton(player){
  
  if(player.paused == true){
    player.addEventListener("timeupdate", function(){
      player.remaining = (player.sp_duration - player.currentTime);
      if(player.paused == true){
        player.timebox.innerHTML = formatTime(player.sp_duration);
        player.row.setAttribute('class', 'clip-stopped');
        player.playbutton.innerHTML = '<i class="icon-play"></i>';
        player.playbutton.setAttribute('class','play btn btn-success');
        setVolume(player,0);
      }
      else if(player.ended == true){
        outButton(player);
        player.timebox.innerHTML = formatTime(player.sp_duration);
      }
      else{
        player.timebox.innerHTML = formatTime(player.remaining);
        player.row.setAttribute('class', 'clip-playing');
        player.playbutton.innerHTML = '<i class="icon-stop"></i>';
        player.playbutton.setAttribute('class','play btn btn-warning');
      }
      /*
      This is how deciding to continue to the next clip is decided. It is an
      "if ladder" to make room for comments explaining the process.
      */

      /*
      The next thing to check is if the current clip is near the end. This
      value is currently hard coded to 1 seconds but in the future there could
      be a value assigned by a flag in the current clip.
      */
      if(player.remaining < 1){
        /*
        No sense in trying to segue if seque is false.
        */
        if(player.segue == true){
          /*
          We need to make sure the next clip is not playing before we click on
          the button because we would end up stopping it. The check is to get
          the class of the clip row.
          */
          if(document.getElementById(String('clip-' + player.sp_next)).getAttribute('class') == 'clip-stopped'){
            /*
            We only want to click a play button once so the segue button
            is toggled. It is toggled here because we want to keep trying
            to segue in case the clip stops looping.
            */
            if(player.loop == false){
              document.getElementById(String('play-' + player.sp_next)).click();
              player.seguebutton.click();
            }
          }
        }
      }
    }, false);
    player.play();
    upButton(player);
  }
  else{
    outButton(player);
  }
}

// This is launched from an onclick="loopButton(player[i])" attribute
// defined in makeHTML.php.
function loopButton(player){
  if(player.loop == false){
    player.loop=true;
    player.loopbutton.setAttribute('class', 'loop btn btn-warning');
    player.loopbutton.innerHTML = "Loop'd";
  }
  else{
    player.loop=false;
    player.loopbutton.setAttribute('class', 'loop btn btn-inverse');
    player.loopbutton.innerHTML = "Loop";
  }
}

// This is launched from an onclick="segueButton(player[i])" attribute
// defined in makeHTML.php.
function segueButton(player){
  if(player.segue == false){
    player.segue=true;
    player.seguebutton.setAttribute('class', 'segue segue-true');
    player.seguebutton.setAttribute('value', "Segue'd");
  }
  else{
    player.segue=false;
    player.seguebutton.setAttribute('class', 'segue segue-false');
    player.seguebutton.setAttribute('value', "Segue");
  }
}

// This is launched from an onclick="underButton(player[i])" attribute
// defined in makeHTML.php.
function underButton(player){
  if(player.under == false){
    player.under = true;
    player.underbutton.setAttribute('class', 'under under-true');
    player.underbutton.setAttribute('value', "Under");
    player.volume = 0.4;
  }
  else{
    player.under = false;
    player.underbutton.setAttribute('class', 'under under-false');
    player.underbutton.setAttribute('value', "Under 1");
    player.volume = 1.0;
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
  player.volumebox.innerHTML = dv;
}

function stopButton(player){
  player.ended=true;
  player.pause();
  player.currentTime = 0;
}

function outButton(player){
  var fadetime = 2000;
  var steps = 25;
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
      stop = setTimeout(function(){stopButton(player); setVolume(player,0)}, fadetime);
    }
  }
}
function upButton(player){
  var initdelay = 10;
  var fadetime = 0;
  var steps = 1;
  var granularity = 1/steps;
  var delay = new Array(steps);
  var tv = new Array(steps);
  /*
  This is slightly different from the outButton() loop in that the first
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
  // Listens for key presses and routes accordingly.
  document.onkeydown = KeyCheck;
  keybinds = Array();
  for(var i=1;i<player.length;i++){
    // Include all the stuff needed for the players in the player objects
    player[i].timebox =   document.getElementById(String('duration-' + i));
    player[i].row =       document.getElementById(String('clip-' + i));
    player[i].remaining = (player[i].sp_duration - player[i].currentTime);
    player[i].volumebox = document.getElementById(String('out-' + i));
    
    // Send any markup with default values to display to get started
    // The play and loop buttons are not part of the database so they are
    // created here and put into the "file" column. 
    player[i].playbutton = document.getElementById(String('play-' + i));
    player[i].loopbutton = document.getElementById(String('loop-' + i));
    player[i].seguebutton = document.getElementById(String('segue-' + i));
    player[i].underbutton = document.getElementById(String('under-' + i));
    player[i].timebox.innerHTML = formatTime(player[i].remaining);
    // Creates an array of keybinds to player ids.
    if(player[i].sp_keybind){
      keybind = player[i].sp_keybind.toUpperCase();
      KeyID=keybind.charCodeAt(0);
      keybinds[KeyID] = player[i].playbutton;
    }
  }
}

function KeyCheck(e){
  var KeyID = (window.event) ? event.keyCode : e.keyCode;
  var BindID = String.fromCharCode(KeyID);
  if(KeyCheckActive == true){
    keybinds[KeyID].click();
  }
}

function getTableLabels(){
  var table = document.getElementById("main-table");
  var output = "";
  for(var i = 0, row; row = table.rows[i]; i++){
    if(i<1){
      for(var j = 0, col; col = row.cells[j]; j++){
        if(j>0){
          output = output + " - " + row.cells[j].innerHTML;
        }
        else{
          output = row.cells[j].innerHTML;
        }
      }
    }
  }
  alert(output);
}
