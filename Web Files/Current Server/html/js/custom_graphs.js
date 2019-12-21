$(document).ready(function () {

  var url;

  if (location.protocol == 'https:'){
    url = "https://environment.concordiashanghai.org/assets"; 
  }

  if (location.protocol == 'http:'){
    url = "http://environment.concordiashanghai.org/assets"; 
  }

  //var send_url = "http://localhost/marchsmsdb/bdst/";
  //var send_url = "http://sms.concordiashanghai.org/bdst/";
  //var url = "http://localhost/bdst_april/assets";

  //first generating the graphs

  loadChart("CO2");

  loadChart("Humidity");

  loadChart("PM25");

  loadChart("Temperature");

  loadViolinChart("CO2");

  loadViolinChart("Humidity");

  loadViolinChart("PM25");

  loadViolinChart("Temperature");


  //function to load a graph
  function loadChart(graph_type) {
    var json_url = url + "/graph_php_files/custom_graphs_sql.php";

    $.ajax({
      url: json_url,
      type: "GET",
      success: function (data) {
        console.log(data);



        //Distinct Location
        var distinctLocations = [...new Set(data.map(x => x.Location))];
        // console.log(distinctLocationsRaw);

        var distinctLocationsLength = distinctLocations.length;
        //console.log(distinctLocationsLength); //should print out 55 or something

        var large_room_data = [];

        for (var i = 0; i < distinctLocationsLength; i++) {

          var room_data = {
            x: [],
            y: [],
            mode: 'lines+markers',
            name: distinctLocations[i],
          };

          var len = data.length;
          for (var j = 0; j < len; j++) {
            if (data[j].Location == distinctLocations[i]) {
              //incrementing

              if (graph_type == "CO2") {

                room_data.y.push(data[j].CO2);

              } else if (graph_type == "PM25") {

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
            text: graph_type + ' Graph',
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
      error: function (data) {
        console.log(json_url);
      }


    });
  }

  function loadViolinChart(graph_type) {

    var json_url = url + "/graph_php_files/custom_graphs_sql.php";

    $.ajax({
      url: json_url,
      type: "GET",
      success: function (data) {





        //Distinct Location
        var distinctLocations = [...new Set(data.map(x => x.Location))];
        // console.log(distinctLocationsRaw);



        var colors = ['red', 'blue', 'green', 'magenta', 'lightblue', 'purple', 'orange', 'blueViolet',
          'brown', 'crimson', 'cyan', 'DarkGreen', 'SlateBlue', 'Navy', 'Grey', 'Olive', 'Lime', 'Teal',
          'Fuchsia', 'Silver', 'RGB(240, 128, 128)', 'RGB(88, 214, 141)', 'RGB(231, 76, 60)', 'RGB(39, 55, 70)',
          'RGB(244, 208, 63)', 'RGB(174, 214, 241)', 'RGB(255, 87, 51)', 'RGB(211, 51, 80)'
        ]



        var distinctLocationsLength = distinctLocations.length;
        //console.log(distinctLocationsLength); //should print out 55 or something

        var large_room_data = [];

        for (var i = 0; i < distinctLocationsLength; i++) {

          var room_data = {
            type: 'violin',
            x: [],
            y: [],
            mode: 'markers',
            points: 'none',
            name: distinctLocations[i],
            box: {
              visible: true
            },
            line: {
              color: colors[i],
            },
            meanline: {
              visible: true
            }
          };

          var len = data.length;
          for (var j = 0; j < len; j++) {
            if (data[j].Location == distinctLocations[i]) {
              //incrementing

              if (graph_type == "CO2") {

                room_data.y.push(data[j].CO2);



              } else if (graph_type == "PM25") {

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
              //   room_data.x.push(moment().format(data[j].datetime));
              room_data.x.push(distinctLocations[i]);
            }
          }
          //push into the larger array
          large_room_data.push(room_data);

        }
        //Now i should have an array with number of rooms (so 3 right now)
        //and then within each array there is Temperature and datetime
        //name of each unit has to be designated by order
        //console.log(large_room_data);



        Plotly.newPlot('ViolinPlot-' + graph_type, large_room_data);

      },
      error: function (data) {
        //console.log(data);
      }


    });
  }


});