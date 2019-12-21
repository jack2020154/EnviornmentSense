$(document).ready(function() {

  var url;

  if (location.protocol == 'https:'){
    url = "https://environment.concordiashanghai.org/assets"; 
  }

  if (location.protocol == 'http:'){
    url = "http://environment.concordiashanghai.org/assets"; 
  }

//resolution value for purely the client side loop
var resolution_value = 1;
//Resolution value for the serverside query. use this one:
var json_resolution_value = 10;

//How many days back
var load_num_days = 1;

//Buttons to show what data to load. 
var all_data = document.getElementById("all-btn");
var all_school = document.getElementById("all-school-btn");
var Highschool = document.getElementById("Highschool-btn");
var PC = document.getElementById("PC-btn");
var Rittman = document.getElementById("Rittman-btn");
var Intermediate = document.getElementById("Intermediate-btn");
var Elementary = document.getElementById("Elementary-btn");
var suspicious_button = document.getElementById("suspicious-btn");
var live_button = document.getElementById("live-btn");
//Buttons to alter the resolution of the loaded graphs
var res_1 = document.getElementById("resolution_1");
var res_3 = document.getElementById("resolution_3");
var res_5 = document.getElementById("resolution_5");
var res_10 = document.getElementById("resolution_10");

//Past 1, 3 or 5 days
var past_1_day = document.getElementById("past_1_day");
var past_3_day = document.getElementById("past_3_day");
var past_5_day = document.getElementById("past_5_day");

//By default do not include suspicious data
var include_suspicious_data = false
//The initial graphs loaded in
var current_graphs = "allschool";

//Default Loading in all the graphs
updateAllGraphs();

suspicious_button.innerHTML = "Include Suspicious Data (?)";
live_button.innerHTML = "status: Static Graph";

//Past 24 Hr data
past_1_day.onclick = function(){
  console.log("setting day value to 1")
  load_num_days = 1;
  updateAllGraphs();
}

//Past 3 days data
past_3_day.onclick = function(){
  console.log("setting day value to 3")
  load_num_days = 3;
  updateAllGraphs();
}

//Past 5 days data
past_5_day.onclick = function(){
  console.log("setting day value to 5")
  load_num_days = 5;
  updateAllGraphs();
}


//100% Resolution. all data points are loaded
res_1.onclick = function(){
  console.log("setting resolution to: 1")
  json_resolution_value = 1;
  updateAllGraphs();
}

//33% of datapoints are loaded in. Every third datapoint is loaded in
res_3.onclick = function(){
  console.log("setting resolution to: 3")
  json_resolution_value = 3;
  updateAllGraphs();
}

//20% of all datapoints are loaded in. 
res_5.onclick = function(){
  console.log("setting resolution to: 5")
  json_resolution_value = 5;
  updateAllGraphs();
}

//10% of all datapoints are loaded in
res_10.onclick = function(){
  console.log("setting resolution to: 10")
  json_resolution_value = 10;
  updateAllGraphs();
}


//whether or not to filter suspicious Data button
var suspicion_button_cnt = 2;
suspicious_button.onclick = function(){
  
  if(suspicion_button_cnt % 2 == 0){
    suspicious_button.innerHTML = "Including Suspicious Data. . . (may take a couple seconds)";
    include_suspicious_data = true;
  } else {
    suspicious_button.innerHTML = "Filtering Out Suspicious Data. . . (may take a couple seconds)";
    include_suspicious_data = false;
  }
  suspicion_button_cnt++

loadChart("Humidity", current_graphs);

loadChart("CO2", current_graphs);

loadChart("PM25", current_graphs);

loadChart("Temperature", current_graphs);

loadChart("VOC", current_graphs);
}

//Whether or not to have a live updating graph. 
var loading_live_graph = false;
var live_button_cnt = 1;
live_button.onclick = function(){
  
  if(live_button_cnt % 2 == 0){
    live_button.innerHTML = "status: Static Graph";
    loading_live_graph = false;
  } else {
    live_button.innerHTML = "status: Live Graph (5 second interval)";
    loading_live_graph = true;
  }
  live_button_cnt++
}


  //loading the data
  all_data.onclick = function(){
    console.log("loading all data...");
    current_graphs = "alldata";
    updateAllGraphs();
  }

  all_school.onclick = function(){
    console.log("loading all school data...");
    current_graphs = "allschool";
    updateAllGraphs();
  }

  Highschool.onclick = function(){
    console.log("loading HS data...");
    current_graphs = "highschool";
    updateAllGraphs();
  }

  PC.onclick = function(){
    console.log("loading PC data...");
    current_graphs = "PC";
    updateAllGraphs();
  }

  Rittman.onclick = function(){
    console.log("loading school data...");
    current_graphs = "rittman";
    updateAllGraphs();
  }

  Intermediate.onclick = function(){
    console.log("loading school data...");
    current_graphs = "intermediate";
    updateAllGraphs();
  }

  Elementary.onclick = function(){
    console.log("loading school data...");
    current_graphs = "elementary";
    updateAllGraphs();
  }


function updateAllGraphs(){

  console.log("reloading the graphs");

  loadChart("Humidity", current_graphs);

  loadChart("CO2", current_graphs);

  loadChart("PM25", current_graphs);

  loadChart("Temperature", current_graphs);

  loadChart("VOC", current_graphs);

}


//reloading the graphs after a certain interval
if(loading_live_graph){

    setInterval(function() {

      loadChart("Humidity", current_graphs);

      loadChart("CO2", current_graphs);
      
      loadChart("PM25", current_graphs);
      
      loadChart("Temperature", current_graphs);
      
      loadChart("VOC", current_graphs);

      }, 5000);
    }

    //function to load a graph
    function loadChart(graph_type,filter_type){
      var json_url;


      if(include_suspicious_data){
        var sus_url = "?resolution=" + json_resolution_value + "&load_num_days=" + load_num_days;
      } else {
        sus_url = "?suspiciousFilter=suspicious&resolution=" + json_resolution_value  + "&load_num_days=" + load_num_days;
      }

      if(graph_type == "VOC"){
        json_url = url + "/graph_php_files/graph_hub_sql.php?isVOC=yes&resolution=" + json_resolution_value  + "&load_num_days=" + load_num_days;
      } else {
        json_url = url + "/graph_php_files/graph_hub_sql.php" + sus_url;
      }

      
      $.ajax({
          url : json_url,
          type : "GET",
          success : function(data){
              console.log(data);

              //The potential Graph Types

              var elementary_location_array = [//Elementary
                'E101B', 'E103', 'E104', 'E105', 'E106',
                            'E107', 'ef1stair', 'E111', 'E121', 'E117',
                            'E115', 'E114', 'E113', 'E108', 'E109', 'esf1hallway', 'E122',
        
                            'esf2stairs', 'E207', 'E206', 'E205', 'E204',
                            'E203', 'E202', 'E201', 'E209' ,'E208', 'E221',
                            'E216', 'E215', 'E214', 'E213', 'E220', 'esf2hall',
        
                            'esf3stairs', 'E307', 'E306', 'E305', 'E304',
                            'E303', 'E302', 'E301', 'E309', 'E308', 'E321',
                            'E316', 'E315', 'E314', 'E313', 'E320', 'E311',
                            'E322', 'E310',
        
                            'esf4stairs', 'E407', 'E406', 'E405', 'E404', 'E403',
                            'E402', 'E401', 'E409', 'E408', 'E421', 'E416', 'E415',
                            'E414', 'E413', 'E422', 'E410', 'esf4hall', 'esf3hall'];

              var intermediate_location_array = [
                //Intermediate Building
                'M110', 'M111', 'M115' ,'Intermediate_hallway',
                            'M109', 'M108', 'M102', 'M104', 'M105', 'M106',
                            'M107', 'M101A', 'M101', 'M101B', 'M101C',
                            'M101D', 'M124','M123A', 'M123B', 'M123C',
                            'M121B', 'M121A', 'M121', 'M123', 'M119', 'M117',
                            'M120', 'M116', 'M122', 'M124A',
        
                            'intermediate_f2_hallways', 'M211', 'M210', 'M209', 'M208', 'M207', 'M206',
                            'M205', 'M204', 'M202', 'M201', 'M225', 'M224',
                            'M223', 'M216', 'M219', 'M220', 'M217'];

              var highschool_location_array = ['H036', 'H034', 'H032', 'H030', 'H026', 'H020', 'H018',
                            'H015B', 'H015A', 'H108', 'H107', 'H006', 'H005', 'H004',
                            'H003', 'H041', 'H040', 'H015', 'L1stair',
        
                            'H127', 'H117', 'F1stairs', 'f1hall', 'H105', 'H104', 'H103',
                            'H102', 'H115', 'H115A', 'H116', 'H118', 'H116A', 'f1stair2',
                            'welcomecenter',
        
                            'H227', 'H227C', 'H227A', 'H227B','H229', 'H230', 'H231', 'H232',
                            'H233', 'H234', 'H235', 'H236', 'H237', 'H240', 'H216', 'H218', 'H218C',
                            'H218A', 'pclounge', 'pccafe', 'H206', 'H205', 'H204', 'H203', 'H201',
        
                            'operations', 'H301','H306', 'H305', 'H304', 'H303', 'H318', 'f3hall1', 'f3hall2',
        
                            'H405', 'H404', 'H403', 'H401', 'f4hallA', 'f4hallB', 'f4hallC',
                            'H429', 'H418', 'H416', 'H417', 'H429A',
        
                            'H506', 'H505', 'H504', 'H503', 'H501', 'f5HallC', 'f5HallA',
                            'f5HallB', 'H529', 'H518', 'H518A', 'H516', 'H517',
        
                            'f6hallB', 'H606', 'H605', 'H604', 'H603', 'f6hallA', 'H601'];

              var pc_location_array = ['PC_entrance', 'PC_commons',
                  'P106', 'stairs','P103', 'P107',
                  'P108', 'P111', 'P113b', 'P113a',
                  'P116', 'P117', 'P121', 'P128',
                  'P132', 'P133', 'P134', 'P135',
                  'P137', 'P138',
        
                  'P235', 'P234', 'P233' , 'P232', 'P230', 'P201', 'P202A',
                  'P202', 'P203', 'P204', 'PChallA', 'PCstairs', 'PCoverhead',
                   'P207', 'H208', 'P209', 'P211',
                  'PCac'];

              var rittman_location_array = ['R101', 'R102', 'R110', 'R103', 'lobby_hallway', 'R104',
                            'R105', 'R107', 'R106', 'R108', 'R109', 'f2_hallway',
                            'R220', 'R221', 'practice_rooms', 'R228', 'R229',
                            'f3_hallway', 'R302', 'R301', 'R303', 'R305', 'R306',
                            'R307', 'R308', 'R401', 'R402', 'R403', 'R407', 'R404',
                            'R405', 'R406', 'roof_garden', 'f4_hallway'];

              var all_school_array = [
                //Elementary
                'E101B', 'E103', 'E104', 'E105', 'E106',
                            'E107', 'ef1stair', 'E111', 'E121', 'E117',
                            'E115', 'E114', 'E113', 'E108', 'E109', 'esf1hallway', 'E122',
        
                            'esf2stairs', 'E207', 'E206', 'E205', 'E204',
                            'E203', 'E202', 'E201', 'E209' ,'E208', 'E221',
                            'E216', 'E215', 'E214', 'E213', 'E220', 'esf2hall',
        
                            'esf3stairs', 'E307', 'E306', 'E305', 'E304',
                            'E303', 'E302', 'E301', 'E309', 'E308', 'E321',
                            'E316', 'E315', 'E314', 'E313', 'E320', 'E311',
                            'E322', 'E310',
        
                            'esf4stairs', 'E407', 'E406', 'E405', 'E404', 'E403',
                            'E402', 'E401', 'E409', 'E408', 'E421', 'E416', 'E415',
                            'E414', 'E413', 'E422', 'E410', 'esf4hall', 'esf3hall',
        
                //Intermediate Building
        
                'M110', 'M111', 'M115' ,'Intermediate_hallway',
                            'M109', 'M108', 'M102', 'M104', 'M105', 'M106',
                            'M107', 'M101A', 'M101', 'M101B', 'M101C',
                            'M101D', 'M124','M123A', 'M123B', 'M123C',
                            'M121B', 'M121A', 'M121', 'M123', 'M119', 'M117',
                            'M120', 'M116', 'M122', 'M124A',
        
                            'intermediate_f2_hallways', 'M211', 'M210', 'M209', 'M208', 'M207', 'M206',
                            'M205', 'M204', 'M202', 'M201', 'M225', 'M224',
                            'M223', 'M216', 'M219', 'M220', 'M217',
        
                //Highschool
                         'H036', 'H034', 'H032', 'H030', 'H026', 'H020', 'H018',
                            'H015B', 'H015A', 'H108', 'H107', 'H006', 'H005', 'H004',
                            'H003', 'H041', 'H040', 'H015', 'L1stair',
        
                            'H127', 'H117', 'F1stairs', 'f1hall', 'H105', 'H104', 'H103',
                            'H102', 'H115', 'H115A', 'H116', 'H118', 'H116A', 'f1stair2',
                            'welcomecenter',
        
                            'H227', 'H227C', 'H227A', 'H227B','H229', 'H230', 'H231', 'H232',
                            'H233', 'H234', 'H235', 'H236', 'H237', 'H240', 'H216', 'H218', 'H218C',
                            'H218A', 'pclounge', 'pccafe', 'H206', 'H205', 'H204', 'H203', 'H201',
        
                            'operations', 'H301','H306', 'H305', 'H304', 'H303', 'H318', 'f3hall1', 'f3hall2',
        
                            'H405', 'H404', 'H403', 'H401', 'f4hallA', 'f4hallB', 'f4hallC',
                            'H429', 'H418', 'H416', 'H417', 'H429A',
        
                            'H506', 'H505', 'H504', 'H503', 'H501', 'f5HallC', 'f5HallA',
                            'f5HallB', 'H529', 'H518', 'H518A', 'H516', 'H517',
        
                            'f6hallB', 'H606', 'H605', 'H604', 'H603', 'f6hallA', 'H601',
        
                //PC Building
                            'PC_entrance', 'PC_commons',
                  'P106', 'stairs','P103', 'P107',
                  'P108', 'P111', 'P113b', 'P113a',
                  'P116', 'P117', 'P121', 'P128',
                  'P132', 'P133', 'P134', 'P135',
                  'P137', 'P138',
        
                  'P235', 'P234', 'P233' , 'P232', 'P230', 'P201', 'P202A',
                  'P202', 'P203', 'P204', 'PChallA', 'PCstairs', 'PCoverhead',
                   'P207', 'H208', 'P209', 'P211',
                  'PCac',
        
                //Rittman
        
                'R101', 'R102', 'R110', 'R103', 'lobby_hallway', 'R104',
                            'R105', 'R107', 'R106', 'R108', 'R109', 'f2_hallway',
                            'R220', 'R221', 'practice_rooms', 'R228', 'R229',
                            'f3_hallway', 'R302', 'R301', 'R303', 'R305', 'R306',
                            'R307', 'R308', 'R401', 'R402', 'R403', 'R407', 'R404',
                            'R405', 'R406', 'roof_garden', 'f4_hallway'
        
                                 ];
                           
        //copied function from stack overflow on how to return an array with the matches
        //of the two given arrays...
        function getMatch(a, b) {
          var matches = [];

          for ( var i = 0; i < a.length; i++ ) {
              for ( var e = 0; e < b.length; e++ ) {
                  if ( a[i] === b[e] ) matches.push( a[i] );
              }
          }
          return matches;
      }
       //Distinct Location
       var distinctLocationsRaw = [...new Set(data.map(x => x.Location))];
      // console.log(distinctLocationsRaw);

      if(filter_type == "highschool"){
        dataKeys = highschool_location_array;
      } else if(filter_type == "PC"){
        dataKeys = pc_location_array;
      } else if(filter_type == "intermediate"){
        dataKeys = intermediate_location_array;
      } else if(filter_type == "rittman"){
        dataKeys = rittman_location_array;
      } else if(filter_type == "elementary"){
        dataKeys = elementary_location_array;
      } else if(filter_type == "allschool"){
        dataKeys = all_school_array;
      } else if(filter_type == "alldata"){
        dataKeys = distinctLocationsRaw;
      } else {
        dataKeys = distinctLocationsRaw;
        console.log("no filter type specified");
      }

      var distinctLocations = getMatch(distinctLocationsRaw, dataKeys);
      var distinctLocationsLength = distinctLocations.length;
      var large_room_data = [];

        for (var i = 0; i < distinctLocationsLength; i++){

          var room_data = {
            x : [],
            y : [],
            mode : 'lines+markers',
            name : distinctLocations[i], 
          };

                  var len = data.length;
                  var res = 0;
                  for (var j = 0; j < len; j++) {
                  
                  //resolution
                  
                  if (data[j].Location == distinctLocations[i]) {
                  //incrementing
                  if(res % resolution_value == 0){

                  if(graph_type == "CO2"){

                      room_data.y.push(data[j].CO2);

                  } else if (graph_type == "PM25"){

                      room_data.y.push(data[j].PM25);

                  } else if (graph_type == "Temperature") {

                      room_data.y.push(data[j].Temperature);

                  } else if (graph_type == "Humidity") {

                      room_data.y.push(data[j].Humidity);

                  } else if (graph_type == "VOC") {

                    room_data.y.push(data[j].VOC);

                } else {
                      console.log("Invalid Graph Creation");
                }


                  //incrementing
                  room_data.x.push(moment().format(data[j].datetime));
                  res += 1;
                  
                } else {
                  res += 1;
                 
                }
              }

          }
  //push into the larger array
            large_room_data.push(room_data);

        }
  //Now i should have an array with number of rooms (so 3 right now)
  //and then within each array there is Temperature and datetime
  //name of each unit has to be designated by order
  //console.log(large_room_data);



var layout = {
  dragmode: false,
  title: {
    text:graph_type + ' Graph',
    font: {
      family: 'Courier New, monospace',
      size: 24
    },
    xref: 'paper',
    x: 0.05,
  },
  xaxis: {
    title: {
      text: 'Date Time',
      font: {
        family: 'Courier New, monospace',
        size: 18,
        color: '#7f7f7f'
      }
    },
  },
  yaxis: {
    title: {
      text: graph_type,
      font: {
        family: 'Courier New, monospace',
        size: 18,
        color: '#7f7f7f'
      }
    }
  }
};
  Plotly.newPlot('myDiv-' + graph_type, large_room_data, layout);

          },
          error : function(data) {
          }


      });
  }

});
