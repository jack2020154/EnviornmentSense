$(document).ready(function() {
  var url;

  if (location.protocol == 'https:'){
    url = "https://environment.concordiashanghai.org/assets"; 
  }

  if (location.protocol == 'http:'){
    url = "http://environment.concordiashanghai.org/assets"; 
  }



    //var url = "http://environment.concordiashanghai.org/assets";  
    //var url = "http://sms.concordiashanghai.org/bdst/";
    //var url = "http://localhost/marchsmsdb/bdst/";
  
    //declaring global variables
    var date;
    var all_data = document.getElementById("all-btn");
    var all_school = document.getElementById("all-school-btn");
    var Highschool = document.getElementById("Highschool-btn");
    var PC = document.getElementById("PC-btn");
    var Rittman = document.getElementById("Rittman-btn");
    var Intermediate = document.getElementById("Intermediate-btn");
    var Elementary = document.getElementById("Elementary-btn");
    var suspicious_button = document.getElementById("suspicious-btn");
    var generate_scatter_btn = document.getElementById("generate_scatter_btn");
  
  var include_suspicious_data = true;
  var current_graphs = "allschool";
  
suspicious_button.innerHTML = "Include Suspicious Data";
generate_scatter_btn.innerHTML = "Generate Graph";


generate_scatter_btn.onclick = function(){
  //Parsing and getting the datetime in Y-M-D format from date.js plugin
      date_value = document.getElementById("date_js").value;
      console.log(date_value);
      var date_month_word = date_value.slice(0,3);
      var date_day = date_value.slice(4,6);
      var date_year = date_value.slice(8,12);
      var date_month = 0;
    
      if(date_month_word == 'Jan'){
        date_month = 1;
      } else if(date_month_word == 'Feb'){
        date_month = 2;
      } else if(date_month_word == 'Mar'){
        date_month = 3;
      } else if(date_month_word == 'Apr'){
        date_month = 4;
      } else if(date_month_word == 'May'){
        date_month = 5;
      } else if(date_month_word == 'Jun'){
        date_month = 6;
      } else if(date_month_word == 'Jul'){
        date_month = 7;
      } else if(date_month_word == 'Aug'){
        date_month = 8;
      } else if(date_month_word == 'Sep'){
        date_month = 9;
      } else if(date_month_word == 'Oct'){
        date_month = 10;
      } else if(date_month_word == 'Nov'){
        date_month = 11;
      } else if(date_month_word == 'Dec'){
        date_month = 12;
      } else {
        console.log("Invalid Month");
      }
      
      date = date_year + "-" + date_month + "-" + date_day;

      loadChart("1", current_graphs);
  
      loadChart("2", current_graphs);
      
      loadChart("3", current_graphs);
      
      loadChart("4", current_graphs);
      
      loadChart("5", current_graphs);
}


  
  //Whether or not to include potentially spurious datavalues
  var suspicion_button_cnt = 1;
  suspicious_button.onclick = function(){
    
    if(suspicion_button_cnt % 2 == 0){
      suspicious_button.innerHTML = "Including Suspicious Data. . . (may take a couple seconds)";
      include_suspicious_data = true;
    } else {
      suspicious_button.innerHTML = "Filtering Out Suspicious Data. . . (may take a couple seconds)";
      include_suspicious_data = false;
    }
    suspicion_button_cnt++
  
  loadChart("1", current_graphs);
  
  loadChart("2", current_graphs);
  
  loadChart("3", current_graphs);
  
  loadChart("4", current_graphs);
  
  loadChart("5", current_graphs);
   
  }
  
    //loading the data
    all_data.onclick = function(){
      console.log("loading all data...");
      current_graphs = "alldata";
      updateAllGraphs()
    }
  
    all_school.onclick = function(){
      console.log("loading all school data...");
      current_graphs = "allschool";
      updateAllGraphs()
    }
  
    Highschool.onclick = function(){
      console.log("loading HS data...");
      current_graphs = "highschool";
      updateAllGraphs()
    }
  
    PC.onclick = function(){
      console.log("loading PC data...");
      current_graphs = "PC";
      updateAllGraphs()
    }
  
    Rittman.onclick = function(){
      console.log("loading school data...");
      current_graphs = "rittman";
      updateAllGraphs()
    }
  
    Intermediate.onclick = function(){
      console.log("loading school data...");
      current_graphs = "intermediate";
      updateAllGraphs()
    }
  
    Elementary.onclick = function(){
      console.log("loading school data...");
      current_graphs = "elementary";
      updateAllGraphs()
    }
  

    function updateAllGraphs(){
      loadChart("1", current_graphs);
  
      loadChart("2", current_graphs);
  
      loadChart("3", current_graphs);
  
      loadChart("4", current_graphs);
  
      loadChart("5", current_graphs);
    }


      //Loading a 3dScatter Graph
      function loadChart(graph_type,filter_type){
                            
        var json_url = url + "/graph_php_files/graph_hub_sql.php?chooseDate=" + date;
          $.ajax({
              url : json_url,
              type : "GET",
              success : function(data){
                  //console.log(data);
    
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

          var colors = ['red', 'blue', 'green', 'magenta', 'lightblue', 'purple', 'orange', 'blueViolet',
'brown', 'crimson', 'cyan', 'DarkGreen', 'SlateBlue', 'Navy', 'Grey', 'Olive', 'Lime', 'Teal',
'Fuchsia', 'Silver', 	'RGB(240, 128, 128)', 'RGB(88, 214, 141)', 'RGB(231, 76, 60)', 'RGB(39, 55, 70)',
'RGB(244, 208, 63)', 'RGB(174, 214, 241)', 'RGB(255, 87, 51)', 'RGB(211, 51, 80)']
    
          var distinctLocations = getMatch(distinctLocationsRaw, dataKeys);
    
    
            var distinctLocationsLength = distinctLocations.length;
            //console.log(distinctLocationsLength); //should print out 55 or something
    
            var large_room_data = [];
    
            for (var i = 0; i < distinctLocationsLength; i++){
    
              var room_data = {
                x : [],
                y : [],
                z: [],
                mode: 'markers',
                name : distinctLocations[i], 
marker: {
size: 12,
line: {
//outline the datapoints
color: "red",
width: 0.5
},
opacity: 0.8
},
type: 'scatter3d',
              };
    
                        var len = data.length;
                        for (var j = 0; j < len; j++) {
                            if (data[j].Location == distinctLocations[i]) {
                      //incrementing
    
                      if(graph_type == "1"){
                        //y axis is time, x is co2, z is pm25
                          room_data.z.push(data[j].PM25);
                          room_data.x.push(data[j].CO2);
                          room_data.y.push(moment().format(data[j].datetime));

                          x_axis_title = "CO2";
                          y_axis_title = "Time";
                          z_axis_title = "PM2.5";
    
                      } else if (graph_type == "2"){
                        //y axis is time, x is Temperature, z is Humidity
                        room_data.z.push(data[j].Humidity);
                        room_data.x.push(data[j].Temperature);
                        room_data.y.push(moment().format(data[j].datetime));
                        x_axis_title = "Temperature";
                        y_axis_title = "Time";
                        z_axis_title = "Humidity";
                        
    
                      } else if (graph_type == "3") {
                        //y axis is time, x is Temperature, z is pm25
                        room_data.z.push(data[j].PM25);
                        room_data.x.push(data[j].Temperature);
                        room_data.y.push(moment().format(data[j].datetime));
                        x_axis_title = "Temperature";
                        y_axis_title = "Time";
                        z_axis_title = "PM2.5";
    
                      } else if (graph_type == "4") {
                        //y axis is time, x is Temperature, z is CO2
                        room_data.z.push(data[j].CO2);
                        room_data.x.push(data[j].Temperature);
                        room_data.y.push(moment().format(data[j].datetime));
                        x_axis_title = "Temperature";
                        y_axis_title = "Time";
                        z_axis_title = "CO2";
    
                      } else if (graph_type == "5") {
                        //y axis is co2, x is Temperature, z is pm25
                        room_data.z.push(data[j].PM25);
                        room_data.x.push(data[j].Temperature);
                        room_data.y.push(data[j].CO2);
                        
                        x_axis_title = "Temperature";
                        y_axis_title = "CO2";
                        z_axis_title = "PM25";
    
                    } else {
                          console.log("Invalid Graph Creation");
                      }
    
                      //incrementing
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
scene: {
xaxis:{title: x_axis_title},
yaxis:{title: y_axis_title},
zaxis:{title: z_axis_title},
}, legend: {
x: 1,
y: 0.5
},

title: {
text:'Plot Title',
font: {
family: 'Courier New, monospace',
size: 24
},
xref: 'paper',
x: 0.05,
},

margin: {
l: 0,
r: 0,
b: 0,
t: 0
}};
    
      Plotly.newPlot('3dscatter-' + graph_type, large_room_data, layout);
    
              },
              error : function(data) {
                  console.log(json_url);
              }
          });
      }
});
  