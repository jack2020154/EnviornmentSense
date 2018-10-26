
/*
      This code is intented to test basic ESP8266 functionality
      This code tests:
      Serial Monitor
      Wifi Connection
      Network Connection through Ping

      Upload master code to ESP8266 in order to test components


*/

#include "ESP8266WiFi.h"
#include "ESP8266Ping.h"


const char* ssid = "CISS_Visitors";
const char* pwd = "";
const char* remote_host = "concordiashanghai.org";
const char* remote_host2 = "baidu.com";
int i = 0;

void setup() {
  Serial.begin(115200);
  WiFi.mode(WIFI_STA);
  WiFi.disconnect();
  delay(100);
  WiFi.begin(ssid, pwd);
  Serial.print("Connecting to ");
  Serial.print(ssid);
  while (WiFi.status() != WL_CONNECTED) {
    delay(100);
    Serial.print(" .");
    i++;
    if (i > 100) {
      Serial.print("Unable to connect to ");
      Serial.print(ssid);
      Serial.println(" in 10 seconds");
      Serial.println();
      Serial.print("MAC Address: ");
      Serial.println(WiFi.macAddress());
      while (true) {} //Hang if unable to connect to wifi
    }
  }
  Serial.println();
  Serial.print("WiFi connected with ip ");
  Serial.println(WiFi.localIP());
  Serial.print("MAC Address: ");
  Serial.println( WiFi.macAddress() );
  Serial.println("Setup done");
}

void loop() {
  if (Ping.ping(remote_host)) {
    int avg_time_ms = Ping.averageTime();
    Serial.print("Pinged ");
    Serial.print(remote_host);
    Serial.print(" in ");
    Serial.print(avg_time_ms);
    Serial.println(" ms");
  } else {
    Serial.print("Unable to ping ");
    Serial.println(remote_host);
    Serial.println();
  }
  if (Ping.ping(remote_host2)) {
    int avg_time_ms = Ping.averageTime();
    Serial.print("Pinged ");
    Serial.print(remote_host2);
    Serial.print(" in ");
    Serial.print(avg_time_ms);
    Serial.println(" ms");
  } else {
    Serial.print("Unable to ping ");
    Serial.println(remote_host2);
  }
}
