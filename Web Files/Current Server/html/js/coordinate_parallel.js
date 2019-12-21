$(document).ready(function () {

  var url;

  if (location.protocol == 'https:'){
    url = "https://environment.concordiashanghai.org/assets"; 
  }

  if (location.protocol == 'http:'){
    url = "http://environment.concordiashanghai.org/assets"; 
  }

  //var url = "http://localhost/marchsmsdb/bdst";
  //var url = "http://sms.concordiashanghai.org/bdst";
  //var url = "http://localhost/bdst_april/assets";
  var resolution_input;
  var chosen_rooms = document.getElementById("chosen_rooms");
  var room_colors = document.getElementById("room_colors");
  var choices_left = document.getElementById("choices_left");
  choices_left.innerHTML = "5 choices left";



  new Picker(document.querySelector('.js-date-picker'), {
    format: 'MMM D, YYYY',
    text: {
      title: 'Pick a date',
    },
  });

  var selected_rooms = [];
  var num_selected = 0;

  $("button").click(function () {

    if (num_selected < 5 && $(this).val() != "btn-picker") {

      var fired_button = $(this).val();
      this.innerHTML = "Selected!!";
      selected_rooms.push(fired_button);
      var displayed_left_string = "";
      for (i = 0; i < selected_rooms.length; i++) {
        displayed_left_string += selected_rooms[i] + ", ";
      }
      num_selected++;
      choices_left.innerHTML = (5 - num_selected) + " choices left";
      console.log(selected_rooms);



      chosen_rooms.innerHTML = displayed_left_string;
    }
  });

  //document.getElementById("generate-btn").addEventListener("click", displayGraph);

  generate_Graph = document.getElementById("generate-btn");

  generate_Graph.onclick = function () {
    date_value = document.getElementById("date_js").value;
    if (document.getElementById("resolution_input").value) {
      resolution_input = document.getElementById("resolution_input").value;
    } else {
      resolution_input = 3;
      console.log("no resolution specified");
    }


    console.log(date_value);
    console.log(resolution_input);
    var date_month_word = date_value.slice(0, 3);
    var date_day = date_value.slice(4, 6);
    var date_year = date_value.slice(8, 12);
    var date_month = 0;

    if (date_month_word == 'Jan') {
      date_month = 1;
    } else if (date_month_word == 'Feb') {
      date_month = 2;
    } else if (date_month_word == 'Mar') {
      date_month = 3;
    } else if (date_month_word == 'Apr') {
      date_month = 4;
    } else if (date_month_word == 'May') {
      date_month = 5;
    } else if (date_month_word == 'Jun') {
      date_month = 6;
    } else if (date_month_word == 'Jul') {
      date_month = 7;
    } else if (date_month_word == 'Aug') {
      date_month = 8;
    } else if (date_month_word == 'Sep') {
      date_month = 9;
    } else if (date_month_word == 'Oct') {
      date_month = 10;
    } else if (date_month_word == 'Nov') {
      date_month = 11;
    } else if (date_month_word == 'Dec') {
      date_month = 12;
    } else {
      console.log("Invalid Month");
    }



    date = date_year + "-" + date_month + "-" + date_day;
    console.log(date);
    loadParallelChart(selected_rooms[0], selected_rooms[1], selected_rooms[2], selected_rooms[3], selected_rooms[4], date);

    var room_colors_string = "";
    for (i = 0; i < selected_rooms.length; i++) {
      var color;
      if (i == 0) {
        color = " Blue";
      } else if (i == 1) {
        color = " Green";
      } else if (i == 2) {
        color = "Red";
      } else if (i == 3) {
        color = " Orange";
      } else if (i == 4) {
        color = " Light Blue";
      }
      room_colors_string += selected_rooms[i] + ": " + color + "<br>";

    }

    room_colors.innerHTML = room_colors_string;

  }



  function loadParallelChart(room_1, room_2, room_3, room_4, room_5, date_value) {
    var json_url = url + "/graph_php_files/graph_hub_sql.php?room_1=" + room_1 + "&room_2=" + room_2 + "&room_3=" + room_3 + "&room_4=" + room_4 + "&room_5=" + room_5 + "&chooseDate=" + date_value + "&resolution=" + resolution_input;
    $.ajax({
      url: json_url,
      type: "GET",
      success: function (data) {
        console.log(json_url);


        //Distinct Location
        var distinctLocations = [...new Set(data.map(x => x.Location))];


        var distinctLocationsLength = distinctLocations.length;
        var large_room_data = [];
        var data_pack = {
          CO2: [],
          PM25: [],
          Temperature: [],
          Humidity: [],
          set_color: [],
          datetime: []
        };

        var room_data = {

          type: 'parcoords',
          pad: [80, 80, 80, 80],
          line: {
            showscale: false,
            reversescale: true,
            colorscale: 'Jet',
            cmin: -4000,
            cmax: -100,
            color: data_pack.set_color
          },

          dimensions: [{
              label: 'Time (Hrs)',
              range: [0, 24],
              values: data_pack.datetime
            },
            {
              label: 'CO2',
              range: [0, 1500],
              values: data_pack.CO2
            }, {
              label: 'PM25',
              range: [0, 30],
              values: data_pack.PM25
            }, {
              label: 'Temperature',
              range: [15, 30],
              values: data_pack.Temperature
            }, {
              label: 'Humidity',
              range: [15, 50],
              values: data_pack.Humidity
            }
          ]
        };

        var len = data.length;
        for (var j = 0; j < len; j++) {

          if (data[j].Location == room_1 ||
            data[j].Location == room_2 ||
            data[j].Location == room_3 ||
            data[j].Location == room_4 ||
            data[j].Location == room_5) {

            data_pack.CO2.push(data[j].CO2);
            data_pack.PM25.push(data[j].PM25);
            data_pack.Temperature.push(data[j].Temperature);
            data_pack.Humidity.push(data[j].Humidity);


            if (data[j].Location == room_1) {
              data_pack.set_color.push("-700");
              //dark blue
            }

            if (data[j].Location == room_2) {
              data_pack.set_color.push("-2200");
              //green
            }

            if (data[j].Location == room_3) {
              data_pack.set_color.push("-3900");
            } //red

            if (data[j].Location == room_4) {
              data_pack.set_color.push("-2500");
            } //orange

            if (data[j].Location == room_5) {
              data_pack.set_color.push("-1200");
            } //lightblue




            var temp_datetime = data[j].datetime;
            var hour = temp_datetime.slice(10, 13);
            var min = (temp_datetime.slice(14, 16)) / 60;
            var time = +hour + +min;
            data_pack.datetime.push(time);


          }
        }
        //push into the larger array
        large_room_data.push(room_data);


        //Now i should have an array with number of rooms (so 3 right now)
        //and then within each array there is Temperature and datetime
        //name of each unit has to be designated by order
        console.log(large_room_data);
        //console.log(room_data);
        //console.log(data_pack);

        var layout = {
          // width: auto,
          // showlegend: true
        };



        Plotly.plot('parallelDiv', large_room_data, layout);

      },
      error: function (data) {
        console.log("Failure to retrieve data");
        console.log(json_url);
      }


    });
  }

});