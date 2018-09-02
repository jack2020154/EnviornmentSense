/*
Before I begin with documenation for this code, I'll briefly outline what occurs from begining to the end of the data upload
Step 1: The Arduino creates what's known as an HTTP POST request. This is basiclly a glorified form that contains all the data that's being uploaded to the server. This form can be parsed(interpreted) by PHP, which is why its nessecary.
Step 2: The file "insertv2.php" is responsible for parsing the HTTP POST request, and uploading it to the MySQL server. This file is located in the folder with the web server, and should be found at localhost/insertv2.php
Step 3: The file "display_mysql.php" querys the MySQL table, and draws it onto a webpage. I DID NOT CREATE THIS FILE, CREDIT GOES TO ericbenwa ON GITHUB


This section of code gets uploaded to the arduino, which has the task of uploading the data to PHP page.

*/

// REMEMBER TO MODIFY THE IP OF THE WEB SERVER IF YOU WANT TO SEND IT TO ME OR YOURSELF
// 99.230.201.165 FOR ME
// PROBABLY 192.168.X.XXX FOR YOU (CHECK WHAT THE IP ADDRESS OF THE COMPUTER HOSTING THE SERVER IS
#include <ESP8266HTTPClient.h>                                                    //These are the libraries that we're using in order to send the data to the PHP page. We really only need *this* library
#include <WiFiClientSecure.h>
#include <ESP8266WiFi.h>                                                          //*this* library
#include <ESP8266WiFiMulti.h>
#include <WiFiUdp.h>
#include <ESP8266WiFiType.h>
#include <ESP8266WiFiAP.h>
#include <WiFiClient.h>                                                           //and *this* library
#include <WiFiServer.h>
#include <ESP8266WiFiScan.h>
#include <ESP8266WiFiGeneric.h>
#include <ESP8266WiFiSTA.h>

const char* location = "21Widmer";
const int temperature = 28;
const int hI = 9000;
const int co2_avg2 = 500;
const int pm100_avg2 = 900;
const int correctedPM25 = 50;
const int pm10_avg2 = 60;
const int aqi = 70;
const int ir = 80;
const int lux_avg2 = 90;
// The list of variables being declared is the data being uploaded to the PHP page.
// This is just a framework, as the actual processes that collect data will be implemented
// since I don't have a working sensor built.

static String data;
// This is the body data of the HTTP POST request we'll be sending. Lines 54-73 contain the actual code that add data to this string


const char* ssid = "ShaqsShowers";
const char* pwd = "bubbles3";
//These character array stores the SSID (name) and password of the WiFi network that you want to connect to.


void phpUpload() {                                                            //This function is responsible for creating the HTTP POST request and sending it to the PHP page
  if ( WiFi.status() == WL_CONNECTED ) {                                      //If statement checks if you're actually connected to the WiFi network before generating the POST request
      HTTPClient http;                                                        //Honestly, idk what this does but its nessecary
      http.begin("http://192.168.0.12/insertv2.php"); //HTTP                  //Tells the ESP to connect to this PHP page, and send the POST request there
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Builds the headers of the HTTP POST request. These POST requests have to be in a very specific format, and this does the heading of it
      data = "location=";                                                     //These lines append strings to the 'data' string that was declared earlier. The final result looks something like this:
      data += location;                                                       //location=21Widmer&temperature=24.2&humidity=23&....
      data += "&temperature=";
      data += temperature;
      data += "&heatIndex=";
      data += hI;
      data += "&co2=";
      data += co2_avg2;
      data += "&pm100=";
      data += pm100_avg2;
      data += "&pm25=";
      data += correctedPM25;
      data += "&pm10=";
      data += pm10_avg2;
      data += "&aqi=";
      data += aqi;
      data += "&ir=";
      data += ir;
      data += "&light=";
      data += lux_avg2;
      int httpCode = http.POST(data);                                         // After all the strings are appended to the 'data' variable, the ESP uses the http.POST method to actually POST the data (send it to the PHP page)
      Serial.println(data);                                                   // Helps debugging by printing the data being sent to the PHP page
      String payload = http.getString();                                      // Gets a response from the PHP page. In this case, its the SQL command being executed in order to add the data to the database.
      Serial.println(payload);                                                // Prints the SQL command from the previous line

      Serial.println(httpCode);                                               // Prints the HTTP code that the PHP page returns. Code 2xx represents success (The PHP got, understood, and processed the request), and code -1 represents failure.
      http.end();                                                             // Ends connection
  }
}

void connecttoWifi() {                                                        // Connects to Wifi.
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);
  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, pwd);

  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.println("WiFi connected");
}

void setup() {
  Serial.begin(9600);
  connecttoWifi();
  phpUpload();
}

void loop() {
// Loop is blank (for now) because I was just debugging and testing the program, only intending to send one POST request. Since nothing is in loop, everything is executed only once.
}
