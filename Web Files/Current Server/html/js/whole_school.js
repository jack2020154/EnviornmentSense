window.onload = function () {
  var url;

  if (location.protocol == 'https:'){
    url = "https://environment.concordiashanghai.org/"; 
  }

  if (location.protocol == 'http:'){
    url = "http://environment.concordiashanghai.org/"; 
  }
  //var url = "http://localhost/bdst_april/";
  //var url = "http://sms.concordiashanghai.org/bdst/";
  //var url = "localhost/marchsmsdb/bdst/";


  var maxValue;
  var units;
  var heatmap;
  var displayed_data = document.getElementById("displayed_data");

  var button1 = document.getElementById("1btn");
  var button2 = document.getElementById("2btn");
  var button3 = document.getElementById("3btn");
  var button4 = document.getElementById("4btn");
  var button5 = document.getElementById("5btn");
  var button6 = document.getElementById("6btn");
  var button7 = document.getElementById("7btn");
  var button8 = document.getElementById("8btn");
  var button9 = document.getElementById("9btn");
  var button10 = document.getElementById("10btn");
  var button11 = document.getElementById("11btn");
  var button12 = document.getElementById("12btn");
  var button13 = document.getElementById("13btn");
  var button14 = document.getElementById("14btn");
  var button15 = document.getElementById("15btn");
  var button16 = document.getElementById("16btn");
  var button17 = document.getElementById("17btn");
  var button18 = document.getElementById("18btn");
  var button19 = document.getElementById("19btn");
  var button20 = document.getElementById("20btn");
  var button21 = document.getElementById("21btn");
  var button22 = document.getElementById("22btn");
  var button23 = document.getElementById("23btn");
  var button24 = document.getElementById("0btn");

  //Determining what heatmap it is:
  var heatmap_type;
  var displayType = document.getElementById("heatmap_type_displayed");
  var CO2Map = document.getElementById("CO2-btn");
  var AQIMap = document.getElementById("PM25-btn");
  var TempMap = document.getElementById("Temperature-btn");
  var HumidityMap = document.getElementById("Humidity-btn");

  displayType.innerHTML = "<strong>Please select a Heatmap Type and Time:</strong>";

  CO2Map.onclick = function () {
    heatmap_type = "CO2";
    displayType.innerHTML = "HeatMap: " + heatmap_type;
    console.log("Loading CO2...");
    maxValue = 2500;
  }

  AQIMap.onclick = function () {
    heatmap_type = "PM25";
    displayType.innerHTML = "HeatMap: " + heatmap_type;
    console.log("Loading AQI...");
    maxValue = 30;
  }

  TempMap.onclick = function () {
    heatmap_type = "Temperature";
    displayType.innerHTML = "HeatMap: " + heatmap_type;
    console.log("Loading Temperature...");
    maxValue = 30;
  }

  HumidityMap.onclick = function () {
    heatmap_type = "Humidity";
    displayType.innerHTML = "HeatMap: " + heatmap_type;
    console.log("Loading Humidity...");
    maxValue = 70;
  }


  var display_time = document.getElementById('display_time');

  //a function to return the average value for each room since each heatmap is in 1 hr intervals
  function roomAverage(data, distinctname) {
    var nums = 0;
    var oldvalue = 0;
    var len = data.length;

    for (var j = 0; j < len; j++) {
      if (data[j].Location == distinctname) {
        if (nums == 0) {
          oldvalue = data[j].CO2;
        } else {
          oldvalue = oldvalue + data[j].CO2
          console.log(oldvalue);
          nums++;
          console.log(nums);
        }
      }
    }
    
    var average = oldvalue / nums;
    return average;
  }
  //loading the data. This function took the most time....
  function loadHeatMap(time) {
    //clearing out the old data first
    var dataKeys = [


      //Elementary
      'E101B', 'E103', 'E104', 'E105', 'E106',
      'E107', 'ef1stair', 'E111', 'E121', 'E117',
      'E115', 'E114', 'E113', 'E108', 'E109', 'esf1hallway', 'E122',

      'esf2stairs', 'E207', 'E206', 'E205', 'E204',
      'E203', 'E202', 'E201', 'E209', 'E208', 'E221',
      'E216', 'E215', 'E214', 'E213', 'E220', 'esf2hall',

      'esf3stairs', 'E307', 'E306', 'E305', 'E304',
      'E303', 'E302', 'E301', 'E309', 'E308', 'E321',
      'E316', 'E315', 'E314', 'E313', 'E320', 'E311',
      'E322', 'E310',

      'esf4stairs', 'E407', 'E406', 'E405', 'E404', 'E403',
      'E402', 'E401', 'E409', 'E408', 'E421', 'E416', 'E415',
      'E414', 'E413', 'E422', 'E410', 'esf4hall', 'esf3hall',

      //Intermediate Building

      'M110', 'M111', 'M115', 'Intermediate_hallway',
      'M109', 'M108', 'M102', 'M104', 'M105', 'M106',
      'M107', 'M101A', 'M101', 'M101B', 'M101C',
      'M101D', 'M124', 'M123A', 'M123B', 'M123C',
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

      'H227', 'H227C', 'H227A', 'H227B', 'H229', 'H230', 'H231', 'H232',
      'H233', 'H234', 'H235', 'H236', 'H237', 'H240', 'H216', 'H218', 'H218C',
      'H218A', 'pclounge', 'pccafe', 'H206', 'H205', 'H204', 'H203', 'H201',

      'operations', 'H301', 'H306', 'H305', 'H304', 'H303', 'H318', 'f3hall1', 'f3hall2',

      'H405', 'H404', 'H403', 'H401', 'f4hallA', 'f4hallB', 'f4hallC',
      'H429', 'H418', 'H416', 'H417', 'H429A',

      'H506', 'H505', 'H504', 'H503', 'H501', 'f5HallC', 'f5HallA',
      'f5HallB', 'H529', 'H518', 'H518A', 'H516', 'H517',

      'f6hallB', 'H606', 'H605', 'H604', 'H603', 'f6hallA', 'H601',

      //PC Building
      'PC_entrance', 'PC_commons',
      'P106', 'stairs', 'P103', 'P107',
      'P108', 'P111', 'P113b', 'P113a',
      'P116', 'P117', 'P121', 'P128',
      'P132', 'P133', 'P134', 'P135',
      'P137', 'P138',

      'P235', 'P234', 'P233', 'P232', 'P230', 'P201', 'P202A',
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

    clearingData = [];
    for (var x = 0; x < dataKeys.length; x++) {
      clearingData.push({
        id: dataKeys[x],
        value: -1
      });
    }
    heatmap.setData({
      max: 2500,
      min: 0,
      data: clearingData,
    });


    link = url + "/assets/graph_php_files/getLiveHeatmap.php?timeVal=" + time;
    console.log(link);
    $.ajax({
      url: link,
      type: "GET",
      success: function (data) {
        console.log(data);

        //Distinct Location
        var distinctLocationsRaw = [...new Set(data.map(x => x.Location))];
        //console.log(distinctLocationsRaw);


        //copied function from stack overflow on how to return an array with the matches
        //of the two given arrays...
        function getMatch(a, b) {
          var matches = [];

          for (var i = 0; i < a.length; i++) {
            for (var e = 0; e < b.length; e++) {
              if (a[i] === b[e]) matches.push(a[i]);
            }
          }
          return matches;
        }

        //use this to clean stuff
        var distinctLocations = getMatch(distinctLocationsRaw, dataKeys);
        var distinctLocationsLength = distinctLocations.length;



        var newDataPoints = [];

        for (var i = 0; i < distinctLocationsLength; i++) {
          var len = data.length;
          var nums = 0;
          var oldvalue = 0;
          for (var j = 0; j < len; j++) {
            if (data[j].Location == distinctLocations[i]) {
              if (nums == 0) {

                if (heatmap_type == "Temperature") {
                  oldvalue = data[j].Temperature;
                  console.log("Temperature added...");
                } else if (heatmap_type == "Humidity") {
                  oldvalue = data[j].Humidity;
                  console.log("Humidity added...");
                } else if (heatmap_type == "CO2") {
                  oldvalue = data[j].CO2;
                  console.log("CO2 added...");
                } else if (heatmap_type == "PM25") {
                  oldvalue = data[j].PM25;
                  console.log("pm25 added...");

                } else {
                  //pm25 by default
                  // oldvalue = data[j].PM25;
                  console.log("nothing selected...");
                }

                nums++;
              } else {
                if (heatmap_type == "Temperature") {
                  oldvalue = +oldvalue + +data[j].Temperature
                  units = "Celcius";
                } else if (heatmap_type == "Humidity") {
                  oldvalue = +oldvalue + +data[j].Humidity
                  units = "%";
                } else if (heatmap_type == "CO2") {
                  oldvalue = +oldvalue + +data[j].CO2
                  units = "ppm CO2";
                } else if (heatmap_type == "PM25") {
                  oldvalue = +oldvalue + +data[j].PM25
                  units = "ppm 2.5";
                } else {
                  //pm25 by default
                  console.log("nothing selected...");
                }
                console.log(oldvalue);
                nums++;
                console.log(nums);
              }
            }
          }
          var rawAverage = oldvalue / nums;
          console.log(distinctLocations[i] + " " + rawAverage);
          console.log("Max Value: " + maxValue);

          //var scaledAverage = rawAverage / 20;
          newDataPoints.push({
            id: distinctLocations[i],
            value: rawAverage
          });
          console.log(newDataPoints);
        }

        heatmap.setData({
          max: maxValue,
          min: 1,
          data: newDataPoints
        });

        if (newDataPoints[0].value == 0) {
          displayType.innerHTML = "<strong>Please choose a Heatmap Type</strong>";

        }
        //cleaning out with innerHTML
        displayed_data.innerHTML = "";
        for (var i = 0; i < newDataPoints.length; i++) {
          //console.log(newDataPoints[i].id);
          data_string = "<strong>" + newDataPoints[i].id + "</strong>: " + Math.round(newDataPoints[i].value) + " " + units + "<br>";
          //inserAdjacentHTML does not destroy child remnants inside the HTML object
          displayed_data.insertAdjacentHTML('beforeend', data_string);
        }



        //Ending the code for the graph


      },
      //Just the followup for the error message for the AJAX
      error: function (data) {
        console.log(data);
      }
    });
  }


  //the buttons. 1 is 1AM, 5 is 5AM, 12 is 12PM...should go up to 24
  button1.onclick = function () {
    var timeVal = "1am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button2.onclick = function () {
    var timeVal = "2am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button3.onclick = function () {
    var timeVal = "3am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button4.onclick = function () {
    var timeVal = "4am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button5.onclick = function () {
    var timeVal = "5am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button6.onclick = function () {
    var timeVal = "6am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button7.onclick = function () {
    var timeVal = "7am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button8.onclick = function () {
    var timeVal = "8am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button9.onclick = function () {
    var timeVal = "9am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button10.onclick = function () {
    var timeVal = "10am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button11.onclick = function () {
    var timeVal = "11am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button12.onclick = function () {
    var timeVal = "12m";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button13.onclick = function () {
    var timeVal = "1pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button14.onclick = function () {
    var timeVal = "2pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button15.onclick = function () {
    var timeVal = "3pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button16.onclick = function () {
    var timeVal = "4pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button17.onclick = function () {
    var timeVal = "5pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button18.onclick = function () {
    var timeVal = "6pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button19.onclick = function () {
    var timeVal = "7pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button20.onclick = function () {
    var timeVal = "8pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button21.onclick = function () {
    var timeVal = "9pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button22.onclick = function () {
    var timeVal = "10pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button23.onclick = function () {
    var timeVal = "11pm";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }
  button24.onclick = function () {
    var timeVal = "12am";
    display_time.innerHTML = "Displaying for " + timeVal;
    loadHeatMap(timeVal);
  }


  //creating the initial map to be shown. Once a button is clicked then the actual heatmap
  //will be created
  heatmap = h337.create({
    container: document.getElementById('heatmapContainer'),
    svgUrl: 'whole_school.svg',
    plugin: 'SvgAreaHeatmap'
  });
  window.heatmap = heatmap;


  heatmap.setData({
    max: 2500,
    min: 1,
    data: 0,
  });



};