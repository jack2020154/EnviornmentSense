//******************************
//Crashbranch/LiveVers
//Version used for the CO2 Calibration on Jan27, Feb 1, Feb 17
//*The TX pin on the PM sensor connects to pin D7 (GPIO 13)
//*The RX pin on the PM sensor connects to pin D8 (GPIO 15)
//*PM SET pin = 1, the module works in continuous sampling mode, it will upload the sample data after the end of each sampling. (The sampling response time is 1s)
//*PM SET pin = 0, the module enters a low-power standby mode.
//
//*The TX pin on the CO2 sensor connects to pin D5 (GPIO 14)
//*The RX pin on the CO2 sensor connects to pin D6 (GPIO 12)
//
//*The Data pin on the DHT22 sensor connects to pin D4 (GPIO 2)
//
//*The SDA pin on the OLED Display connects to D2 (GPIO4)
//*The SCL pin on the OLED Display connects to D1 (GPIO5)
//
//*The SDA pin on the Light Sensor connects to D2 (GPIO4)
//*The SCL pin on the Light Sensor connects to D1 (GPIO5)
//
//*Version：V2.31
//*Author：Joel Klammer, Jack W, Nick H
//*Date：Feb 20, 2018
//******************************
//*****  Revision History  *****
//******************************

// v2.31 Forcing ESP to reboot when VOC is reading values less than 10
// v2.3 Added Documentation and fix for DHT22 sensor zeroing out
// v2.1 Making sure that outrageous CO2 values are not added to the average. Light Filter applied.
// v0.9-1.9 Added HTTP GET for pm25 and CO2 correction curve values, added VOC and TSL2591 Sensor, added EEPROM save to
//the correction curves so after an update the curves work.
// v0.9 Eliminated local wifi router - changed WIFI_AP_STA to WIFI_STA due to library updates
// v0.8 Added JSON page at port 8080
// v0.7 Added support for Light Sensor, switched SDA & SCL for Wire library
// v0.6 Added asynchronous webpage support, graphic welcome screen
// v0.5


#include <SoftwareSerial.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
//#include <FS.h>
#include <DHT.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include "Adafruit_TSL2591.h"
//#include <DNSServer.h>
#include <EEPROM.h>
#include "concordia2.h"
#include "SparkFunCCS811.h"
extern "C" {
#include "user_interface.h"
}
#define CPU    80          // CPU clock in MHz (valid 80, 160)
#define CCS811_ADDR 0x5A

// Initialize the OLED display using Wire library
//#define offset 0x00
#include "SH1106.h" // alias for `#include "SH1106Wire.h"
SH1106 display(0x3c, 4, 5);
//#include "SSD1306.h" // alias for `#include "SSD1306Wire.h"
//SSD1306  display(0x3c, 4, 5);

// OLED display Connections
// D2 (GPIO4) --> SDA
// D1 (GPIO5) --> SCL
// Vcc --> 3V3
// GND --> GND

#define DHTTYPE DHT22        // DHT 22  (AM2302), AM2321

//Test viable DHT Pin connections. Test in this order
//D4/GPIO2 --> 2
//D3/GPIO0 --> 0
//SD3/GPIO10 --> 10
//RX/GPIO3 --> 3
 
#define DHTPIN 2             
DHT dht(DHTPIN, DHTTYPE);

Adafruit_TSL2591 light_sensor = Adafruit_TSL2591(2591); // pass in a number for the sensor identifier (for your use later)
CCS811Core::status errorStatus;

SoftwareSerial pmSerial(13, 15, false, 256);    // PM RX, TX
SoftwareSerial co2Serial(14, 12, false, 256);   // CO2 RX, TX

//Change for each ESP upload
const String espId = "40";
const String dataUrl = "sms.concordiashanghai.org/bdst"; //Just the IP address ex. 172.18.80.11 //older one:  sms.concordiashanghai.org/bdst
const String firmwareVers = "Version 2.31";



//wifi connection, can be used to bypass the need to connect to wifi if false
bool activeConnection = true;

bool wifiConnection = true;

bool vocConnected = true;

//this pertains to the VOC baseline. As the ESP boots up these values are false until confirmed.
bool baselineAvailable = false;
bool baselineLoaded = false;
byte eeprom0, eeprom1, eeprom4, eeprom5;
unsigned int eeprom2, eeprom3;
unsigned int result;
String VOClevels;
int del = 2100;

//default values for VOC
int vocLevels = -1;
int vocCO2 = -1;
int vocTVOC = -1;
String macAddr;

//Login credentials for the ESP. This will be moved to a more efficient/effective method later.
const char* ssid = "TP-Link288";
const char* password = "50308888HO";
//CISS_Employees_Students

//The location that the sensor represents. Ex: H529
String location;

//Password to be entered into the SQL Database
String server_password = "tetrahedron";

//The sensor will use HTTP GET to obtain a series of correction values. These are the names of the php files on the webserver
String phpPages[7] = {"getLocation" , "getPMA", "getPMB", "getPMC", "getCO2A", "getCO2B", "getCO2C"};
//empty array to receive data
String receivedData[7];

//The upload interval for the sensor. The ESP will average the data obtained over this upload Interval and upload it.
static unsigned long uploadInterval = 1000 * 60 * 1;//ms between uploads
//The interval at which the sensor obtains the correction curve values.
static unsigned long receiveDataInterval = 1000 * 60 * 60;
//How long is needed for the VOC to warm up
static unsigned long vocWarmup = 1000 * 60 * 20;
static unsigned long vocBurnin = 48 * 60; // Time for VOC burnin, 2880 minutes
const byte DNS_PORT = 53;
String webpage = "", JSON = "";

//Correction to the PM2.5 sensor of the form: Corrected = a*Raw^2 + b*Raw + c
//default correction values
static double a_pm25 = 0.0060;
static double b_pm25 = 0.0692;
static double c_pm25 = 1.6286;
static double a_co2, b_co2, c_co2;
float a_pm25_temp, b_pm25_temp, c_pm25_temp, a_co2_temp, b_co2_temp, c_co2_temp;

static unsigned char ucRxBuf[50];
static unsigned char ucCO2RxBuf[50];
static unsigned char ucCO2RxCnt = 0, displayCount = 0, PostError = 0;

static byte PMstartup[]  = {0x42, 0x4D, 0xE4, 0x00, 0x01, 0x01, 0x74};
static byte CO2startup[] = {0x42, 0x4D, 0xE3, 0x00, 0x00, 0x01, 0x72};


static unsigned int pm10_raw, pm25_raw, pm100_raw, pm10, pm25, pm100;
static unsigned int part003, part005, part010, part025, part050, part100;
static unsigned int pm10_avg = 0, pm100_avg = 0, co2_avg = 0, aqi = 0, lux_avg = 0;
static unsigned int hcho = 0, co2 = 0;
static double temp = 0, rh = 0, hIndex = 0, lux = 0;
static double temp_avg = 0, rh_avg = 0, pm25_avg = 0, pm25_corrected = 0;
static unsigned char loopCnt = 0;
static float h, t;
static boolean light_sensor_found;
static uint16_t ir, vis, full;

static char temperature[7];
static char humidity[7];
static char correctedPM25[7];
static String IP, data;

static long lastTime = millis();
static long lastTime_receive = millis();

//address for VOC Sensor
CCS811 vocSensor(CCS811_ADDR);

void setup() {
  ESP.wdtDisable();
  ESP.wdtEnable(WDTO_8S);
  //initializing temperature sensor
  dht.begin();
  //initializing the light sensor
  light_sensor_found = light_sensor.begin();

  //If there is a light sensor, set certain parameters
  if (light_sensor_found)
  {
    light_sensor.setGain(TSL2591_GAIN_MED);      // 25x gain
    light_sensor.setTiming(TSL2591_INTEGRATIONTIME_300MS);
  }
  
  //Upon startup, will display the concordia logo. Initiating OLED display
  display.init();
  display.flipScreenVertically();
  display.setTextAlignment(TEXT_ALIGN_CENTER);

  display.drawXbm(0, 0, concordia2_width, concordia2_height, concordia2_bits);
  display.setFont(ArialMT_Plain_16);
  display.drawString(64, 40, firmwareVers);
  display.display();
  display.setTextAlignment(TEXT_ALIGN_LEFT);

//Starting Serial
  Serial.begin(115200);
//Starting PM sensor Serial
  pmSerial.begin(9600);
//Starting CO2 Sensor Serial
  co2Serial.begin(9600);

  EEPROM.begin(512);
  //reads the correction curves from the webserver
  readCurves();

  //Getting the VOC Sensor Baseline
  CCS811Core::status returnCode = vocSensor.begin();
  if ((EEPROM.get(0, eeprom0) == 0xA5) && (EEPROM.get(1, eeprom1) == 0xB2)) {
    unsigned int baselineToApply = ((unsigned int)EEPROM.get(2, eeprom2) << 8 & 0xFFFF | EEPROM.get(3, eeprom3));
    Serial.println("Baseline available");
    Serial.println("Applied baseline: ");
    Serial.println(baselineToApply, HEX);
    baselineAvailable = true;
    errorStatus = vocSensor.setBaseline( baselineToApply );
    if (errorStatus == CCS811Core::SENSOR_SUCCESS) {
      baselineLoaded = true;
    }
  } else {
    Serial.println("Baseline not loaded");
  }
  //First time loaded, no baseline
  if ((EEPROM.get(4, eeprom4) == 0xFF) && (EEPROM.get(5, eeprom5) == 0xFF)) {
    Serial.println("First time plug in. Resetting EEPROM 0x000000100 and 0x000000101 to 0");
    EEPROM.put(4, 0x00);
    EEPROM.put(5, 0x00);
  }
  unsigned int memval4 = EEPROM.get(4, eeprom4);
  unsigned int memval5 = EEPROM.get(5, eeprom5);
  unsigned int currentTime = memval4 * 256 + memval5;
  Serial.print("Val at 4: ");
  Serial.println(memval4);
  Serial.print("Val at 5: ");
  Serial.println(memval5);
  Serial.print("Current burn in time: ");
  Serial.print(currentTime);
  Serial.println(" minutes");
  // Connect to WiFi network
  //WIFI_AP_STA is the combination of WIFI_STA and WIFI_AP. It allows you to create a local WiFi connection and connect to another WiFi router.
  //Potentially use later on to connect to to establish wifi (?)
  //WIFI_OFF changing WiFi mode to WIFI_OFF along with WiFi.forceSleepBegin() will put wifi into a low power state, provided wifi.nullmodesleep(false) has not been called.
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.persistent( false );
  WiFi.mode(WIFI_STA);
  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);

  int i = 0;
  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");
    i++;
    if (i > 60) {
      break;
    }
  }
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println();
    Serial.println("WiFi connected");

    Serial.println();
    Serial.print("MAC Address: ");
    Serial.println( WiFi.macAddress() );
    macAddr = WiFi.macAddress();
    Serial.println("WiFi connected");
    Serial.print("IP Address: ");
    IP = ipToString( WiFi.localIP() );
    Serial.println( IP );
    receiveData();
    if (location  != "999") {
      Serial.println("Data received.");
      display.clear();
      display.setTextAlignment(TEXT_ALIGN_CENTER);
      display.drawXbm(0, 0, concordia2_width, concordia2_height, concordia2_bits);
      display.drawString(64, 40, location);
      display.display();
    }
    else {
      Serial.println("Connection timed out after 30 seconds");
      IP = "NO CONNECTION";
    }
  }
  readCurves();
}



//Calculating PM sensor values
void calculatePM()
{
  unsigned short sum = 0;

  for (byte i = 0; i < 30; i++)
  {
    sum += ucRxBuf[i];
  }

  if (sum == ucRxBuf[30] * 256 + ucRxBuf[31])   //good checksum
  {
    Serial.print("*");
    pm10_raw = ((ucRxBuf[4] << 8) + ucRxBuf[5]); //PM1.0 value of the air detector module
    pm25_raw = ((ucRxBuf[6] << 8) + ucRxBuf[7]); //PM2.5 value of the air detector module
    pm100_raw = ((ucRxBuf[8] << 8) + ucRxBuf[9]); //PM10 value of the air detector module
    pm10 = ((ucRxBuf[10] << 8) + ucRxBuf[11]);  //PM1.0 value of the air detector module
    pm25 = ((ucRxBuf[12] << 8) + ucRxBuf[13]);  //PM2.5 value of the air detector module
    pm100 = ((ucRxBuf[14] << 8) + ucRxBuf[15]); //PM10 value of the air detector module
    part003 = ((ucRxBuf[16] << 8) + ucRxBuf[17]);
    part005 = ((ucRxBuf[18] << 8) + ucRxBuf[19]);
    part010 = ((ucRxBuf[20] << 8) + ucRxBuf[21]);
    part025 = ((ucRxBuf[22] << 8) + ucRxBuf[23]);
    part050 = ((ucRxBuf[24] << 8) + ucRxBuf[25]);
    part100 = ((ucRxBuf[26] << 8) + ucRxBuf[27]);
    hcho = ((ucRxBuf[28] << 8) + ucRxBuf[29]);

    pm25_corrected = a_pm25 * pm25 * pm25 + b_pm25 * pm25 + c_pm25;

    Serial.print("co2 sum: ");
    Serial.println(co2_avg);
    Serial.print("co2 value: ");
    Serial.println(co2);

    Serial.print("Loop count: ");
    Serial.println(loopCnt);

    if (loopCnt > 0) {
      Serial.print("co2 Delta: ");
      Serial.println(abs((co2_avg / loopCnt - co2)));
    }


    if (co2 < 8000 && co2 != 0) { //preventing insanely high values from being entered in

      loopCnt++;      //do averages
      pm25_avg += pm25_corrected;
      pm10_avg += pm10;
      pm100_avg += pm100;
      temp_avg += temp;
      rh_avg += rh;
      co2_avg += co2;
      lux_avg += int(lux + 0.5);
      Serial.println("Data is viable");
    } else {
      Serial.println("Data from CO2 was too large or zero");
    }

  }
  else
    Serial.print("#");


    
    
}

void readCO2(unsigned char ucData) {
  unsigned int old_co2 = co2;
  ucCO2RxBuf[ucCO2RxCnt++] = ucData;
  if (ucCO2RxBuf[0] != 0x42 && ucCO2RxBuf[1] != 0x4D) {
    ucCO2RxCnt = 0;
  }
  if (ucCO2RxCnt > 11) {
    co2 = ucCO2RxBuf[4] * 256 + ucCO2RxBuf[5];
    Serial.print("Value at index 4 of CO2: ");
    Serial.println(ucCO2RxBuf[4]);
    Serial.print("Value at index 5 of CO2: ");
    Serial.println(ucCO2RxBuf[5]);
    //Compute curve
    co2 = (a_co2 * co2 * co2) + (b_co2 * co2) + c_co2;
    ucCO2RxCnt = 0;
  }
  if ( co2 < 150 && loopCnt > 1 ) {
    //bad read
    Serial.print("bad CO2 read");
    co2 = old_co2;
  }
}

void displayInfo() {
//This function displays Information on the serial monitor as well as on the display


  Serial.println("----------------------------------");
  Serial.print("System clock: ");
  Serial.println(millis());
  Serial.print("PM1.0 (STD) : ");
  Serial.print(pm10);
  Serial.println(" ug/m3");

  Serial.print("PM2.5 (STD) : ");
  Serial.print(pm25_corrected);
  Serial.println(" ug/m3");

  Serial.print("PM10  (STD) : ");
  Serial.print(pm100);
  Serial.println(" ug/m3");
  Serial.println();

  Serial.print("Particles > 0.3 um: ");
  Serial.println(part003);
  Serial.print("Particles > 0.5 um: ");
  Serial.println(part005);
  Serial.print("Particles > 1.0 um: ");
  Serial.println(part010);
  Serial.print("Particles > 2.5 um: ");
  Serial.println(part025);
  Serial.print("Particles > 5.0 um: ");
  Serial.println(part050);
  Serial.print("Particles > 10. um: ");
  Serial.println(part100);
  Serial.println();
  Serial.print("CO2: ");
  Serial.print(co2);
  Serial.println(" ppm");
  Serial.print("AQI: ");
  aqi = convert2AQI( pm25_corrected );
  Serial.println( aqi );
  Serial.print("Temperature: ");
  Serial.print(temp);
  Serial.println(" *C");
  Serial.print("Humidity: ");
  Serial.print(rh);
  Serial.println(" %");
  Serial.print("BSSID: ");
  Serial.println(WiFi.BSSIDstr());
  Serial.println("----------------------------------");
  Serial.println();

  display.clear();
  display.setTextAlignment(TEXT_ALIGN_LEFT);
  String s = location;
  if (activeConnection) {
    s += " (";
    s += IP;
    s += ")";
  } else {
    s += "(";
    s += IP;
    s += ")";
  }
  display.setFont(ArialMT_Plain_10);
  display.drawString(0, 0, s);
  if (displayCount == 0 )
  {
    s = "PM2.5: ";
    if (pm25_corrected != 0) s += String( pm25_corrected  );
    else s += "OFFLINE";
    display.setFont(ArialMT_Plain_16);
    display.drawString(0, 11, s);
    s = "AQI  : ";
    if (aqi != 0) s += String( aqi );
    else s += "OFFLINE";
    display.drawString(0, 27, s);
    s = "CO2: ";
    if (co2 != 0 && co2 != 32767) {
      s += String( co2 );
      s += " ppm";
    }
    else s += "OFFLINE";

    display.drawString(0, 43, s);
  }
  else if (displayCount == 1)
  {
    s = "Temp: ";
    s += String( temp );
    s += "°C";
    display.setFont(ArialMT_Plain_16);
    display.drawString(0, 11, s);
    s = "Humid: ";
    s += String( rh );
    s += "%";
    display.drawString(0, 27, s);
    if (light_sensor_found)
    {
      s = "Lux: ";
      s += String( lux );
      display.drawString(0, 43, s);
    }
    if (!vocConnected) displayCount = -1;
  }
  else if (vocConnected && displayCount == 2) {
    s = "VOCs: ";
    s += VOClevels;
    display.setFont(ArialMT_Plain_16);
    display.drawString(0, 11, s);

    // For testing and debugging only, to be removed in deployment
    /*
      s = "VOC CO2: ";
      s += String (vocCO2);
      s += " ppm";
      display.drawString(0, 27, s);

        // For testing and debugging only, to be removed in deployment
      s = "BSSID: ";
      s += (WiFi.BSSIDstr());
      display.drawString(0, 43, s);
    */

    s = "**************";
    //s += delNum;
    //s += " ms";
    display.drawString(0, 27, s);

    // For testing and debugging only, to be removed in deployment
    s = "MemHeap: ";
    s += (ESP.getFreeHeap());
    display.drawString(0, 43, s);


    displayCount = -1;
  }
  displayCount += 1;
  display.display();
}

void uploadData() {
  if ( ( millis() - lastTime ) > uploadInterval ) {
    int pm10_avg2 = (int)( ((double)pm10_avg) / loopCnt + 0.5);
    double pm25_avg2 = pm25_avg / ((double)loopCnt);
    int pm100_avg2 = (int)( ((double)pm100_avg) / loopCnt + 0.5);
    double temp_avg2 = temp_avg / ((double)loopCnt);
    double rh_avg2 = rh_avg / ((double)loopCnt);
    int co2_avg2 = (int)( ((double)co2_avg) / loopCnt + 0.5);
    int lux_avg2 = (int)( ((double)lux_avg) / loopCnt + 0.5);
    if ( WiFi.status() == WL_CONNECTED ) {
      HTTPClient http;
      http.begin("http://sms.concordiashanghai.org/bdst/sensor_upload_data.php"); //HTTP
      //http://sms.concordiashanghai.org/bdst/sensor_upload_data.php
      //http.begin("iot.concordiashanghai.org", 80, "/data.php"); //HTTP
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");
      dtostrf(temp_avg2, 6, 2, temperature);
      dtostrf(rh_avg2, 6, 2, humidity);
      dtostrf(pm25_avg2, 6, 2, correctedPM25);
      //data format:  location=H529&temperature=24.2&humidity=23&....
      data = "location=";
      data += location;
      data += "&temperature=";
      data += temperature;
      data += "&humidity=";
      data += humidity;
      data += "&co2=";
      data += co2_avg2;
      data += "&esp_id=";
      data += espId;
      data += "&pm25=";
      data += correctedPM25;
      data += "&pm10=";
      data += pm10_avg2;
      data += "&pm100=";
      data += pm100_avg2;
      data += "&light=";
      data += lux_avg2;
      data += "&VOC=";
      data += vocTVOC;
      data += "&heap=";
      data += ESP.getFreeHeap();
      data += "&password=";
      data += server_password;

      Serial.println(data);
      wdt_reset();
      int httpCode = http.POST(data);
      http.writeToStream(&Serial);

      if (httpCode > 0 || PostError > 5) { // httpCode will be negative on error
        // HTTP header has been send and Server response header has been handled
        Serial.printf("[HTTP] POST... code: %d\n", httpCode);

        // file found at server
        if (httpCode == HTTP_CODE_OK || PostError > 5 ) {
          String payload = http.getString();
          Serial.println(payload);
        }
        loopCnt = 0;              //reset averages
        pm10_avg = 0;
        pm25_avg = 0;
        pm100_avg = 0;
        temp_avg = 0;
        rh_avg = 0;
        co2_avg = 0;
        lux_avg = 0;
        PostError = 0;
        lastTime = millis();

        //added by nick
        http.end();

      } else {
        Serial.printf("[HTTP] POST... failed, error: %s\n", http.errorToString(httpCode).c_str());
        PostError++;
      }
      //only checking to receive data after an hour;
      if ( ( millis() - lastTime_receive ) > receiveDataInterval ) {
        receiveData();
        lastTime_receive = millis();
      }

    }


  }

}
void reConnect() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  int i = 0;
  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");
    i++;
    if (i > 20) {
      break;
    }
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("Reconnected");
    Serial.println();
    Serial.print("MAC Address: ");
    Serial.println( WiFi.macAddress() );
    macAddr = WiFi.macAddress();
    Serial.println("WiFi connected");
    Serial.print("IP Address: ");
    IP = ipToString( WiFi.localIP() );
    Serial.println( IP );
    receiveData();
    readCurves();
  } else Serial.println("Reconnection failed");
  }

int convert2AQI(double pm)
{
  if (pm <= 12)        //AQI 0 - 50
    return int( (pm - 0.0) / (12.0 - 0.0) * (50.0 - 0.0) + 0.0 );
  else if (pm <= 35.4) //AQI 51 - 100
    return int( (pm - 12.1) / (35.4 - 12.1) * (100.0 - 51.0) + 51 );
  else if (pm <= 55.4) //AQI 101 - 150
    return int( (pm - 35.5) / (55.4 - 35.5) * (150.0 - 101.0) + 101 );
  else if (pm <= 150.4) //AQI 151 - 200
    return int( (pm - 55.5) / (150.4 - 55.5) * (200.0 - 151.0) + 151 );
  else if (pm <= 250.4) //AQI 201 - 300
    return int( (pm - 150.5) / (250.4 - 150.5) * (300.0 - 201.0) + 201 );
  else if (pm <= 350.4) //AQI 301 - 400
    return int( (pm - 250.5) / (350.4 - 250.5) * (400.0 - 301.0) + 301 );
  else if (pm <= 500.0) //AQI 401 - 500
    return int( (pm - 350.5) / (500.0 - 350.5) * (500.0 - 401.0) + 401 );
  else  //AQI > 500
    return pm;
}

String ipToString(IPAddress ip) {
  String s = "";
  for (int i = 0; i < 4; i++)
    s += i  ? "." + String(ip[i]) : String(ip[i]);
  return s;
}

void readLight()
{
  uint32_t lum = light_sensor.getFullLuminosity();
  ir = lum >> 16;
  full = lum & 0xFFFF;
  vis = full - ir;
  lux = light_sensor.calculateLux(full, ir);
}

void readVOC() {
  ESP.wdtFeed(); //Restarting Watchdog Timer
  unsigned int memval4 = EEPROM.get(4, eeprom4);
  unsigned int memval5 = EEPROM.get(5, eeprom5);
  unsigned int currentTime = memval4 * 256 + memval5;
  bool vocTime = millis() > vocWarmup;
  //readVOC change
  bool burnInTime = currentTime >= vocBurnin;
  if (!baselineAvailable && !burnInTime) {
    Serial.println("****No baseline found. No burn in time so far");
    ESP.wdtFeed(); //Restarting Watchdog Timer
    VOClevels =  (String)(currentTime * 100 / vocBurnin) + "% Burn";
    vocSensor.readAlgorithmResults();
    vocCO2 = vocSensor.getCO2();
    vocTVOC = -1;
  }
  else if (!baselineAvailable && burnInTime) {
    
    ESP.wdtFeed(); //Restarting Watchdog Timer
    Serial.println("baseline not availabible but burnintime is.");
    unsigned int baselineToApply = ((unsigned int)EEPROM.get(2, eeprom2) << 8 & 0xFFFF | EEPROM.get(3, eeprom3));
    result = vocSensor.getBaseline();
    EEPROM.put(0, 0xA5);
    EEPROM.put(1, 0xB2);
    EEPROM.put(2, (result >> 8) & 0x00FF);
    EEPROM.put(3, result & 0x00FF);
    EEPROM.commit();
    baselineAvailable = true;
    errorStatus = vocSensor.setBaseline(baselineToApply);
    if (errorStatus == CCS811Core::SENSOR_SUCCESS) {
      ESP.wdtFeed(); //Restarting Watchdog Timer
      baselineLoaded = true;
      Serial.println("Baseline loaded");
      VOClevels =  "BOOTING";
    }
    else {
      ESP.wdtFeed(); //Restarting Watchdog Timer
      Serial.println("No Baseline loaded");
      baselineLoaded = false;
      VOClevels = "ERROR";
    }
  }

  else if (baselineAvailable && !baselineLoaded) {
    Serial.println("****BaselineAvailable but no baselineLoaded");
    ESP.wdtFeed();//Restarting Watchdog Timer
    VOClevels =  "ERROR";
    baselineAvailable = false;
    vocSensor.readAlgorithmResults();
    vocCO2 = vocSensor.getCO2();
    vocTVOC = -1;
  }
  else if (baselineAvailable && baselineLoaded && !vocTime) {
    ESP.wdtFeed(); //Restarting Watchdog Timer
    Serial.println("****Baseline found and loaded but Warming Up");

    VOClevels =  (String)(millis() * 100 / vocWarmup) + "% WARM";
    vocSensor.readAlgorithmResults();
    vocCO2 = vocSensor.getCO2();
    vocTVOC = -1;
  }
  else if (baselineAvailable && baselineLoaded && vocTime) {
    ESP.wdtFeed(); //Restarting Watchdog Timer
        Serial.println("****WARM UP FINISHED");

    if (vocSensor.dataAvailable()) {
          Serial.println("****VOC Data Available");

      ESP.wdtFeed(); //Restarting Watchdog Timer
      vocSensor.readAlgorithmResults();
      vocCO2 = vocSensor.getCO2();
      vocTVOC = vocSensor.getTVOC();
      VOClevels =  (String)vocTVOC;


      //Added as of March 3 to make sure the VOC is warmed up properly
      if(vocTVOC < 10){
        ESP.restart();
      }
      
    } else {
      Serial.println("****NO AVAILABLE VOC DATA");
    }
  }
}

void addTime() {
  if (millis() % 60000 < 3700 ) {
    unsigned int memval4 = EEPROM.get(4, eeprom4);
    unsigned int memval5 = EEPROM.get(5, eeprom5);
    unsigned int currentTime = memval4 * 256 + memval5;
    Serial.print("Val at 4: ");
    Serial.println(memval4);
    Serial.print("Val at 5: ");
    Serial.println(memval5);
    Serial.print("Current burn in time: ");
    Serial.print(currentTime);
    Serial.println(" minutes");
    currentTime++;
    if (currentTime <= 2880) {
      Serial.println("48 hours not reached, updating EEPROM");
      EEPROM.put(4, (currentTime >> 8) & 0x00FF);
      EEPROM.put(5, currentTime & 0x00FF);
      EEPROM.commit();
    } else Serial.println("Burn in completed, no need to update");
  }
}

void receiveData() {
  for (int i = 0; i <= 6; i++) {
    HTTPClient getClient;
    String getURL = "http://" + dataUrl + "/" + phpPages[i] + ".php?espId=" + espId;
    Serial.print("Getting data from: ");
    Serial.println(getURL);
    getClient.begin(getURL);
    int httpCode = getClient.GET();
    if (httpCode >= 200 && httpCode < 300) {
      receivedData[i] = getClient.getString();
      Serial.print("Received Data: ");
      Serial.println(receivedData[i]);
    } else {
      Serial.print("Error code ");
      Serial.println(httpCode);
      receivedData[i] = 999;
    }
    getClient.end();
  }

  location = receivedData[0];
  location.trim();
  a_pm25_temp = receivedData[1].toFloat();
  b_pm25_temp = receivedData[2].toFloat();
  c_pm25_temp = receivedData[3].toFloat();
  a_co2_temp = receivedData[4].toFloat();
  b_co2_temp = receivedData[5].toFloat();
  c_co2_temp = receivedData[6].toFloat();
  //Ensures that invalid responses don't get written to memory
  if ((int)a_pm25_temp != 999 && (int)b_pm25_temp != 999 && (int)c_pm25_temp != 999) {
    if (a_pm25_temp != a_pm25 || b_pm25_temp != b_pm25 || c_pm25_temp != c_pm25) {
      Serial.println("Difference in server and current curves of PM25 detected, updating");
      a_pm25 = a_pm25_temp;
      b_pm25 = b_pm25_temp;
      c_pm25 = c_pm25_temp;
      commitPMCurves();
    }
    else Serial.println("No changes in curve values of PM25 detected, no action required");
    Serial.println("All PM2.5 values received, none invalid");
  } else {
    Serial.println("One or more PM2.5 values are invalid, nullifying");
    reConnect();
  }
  if ((int)a_co2_temp != 999 && (int)b_co2_temp != 999 && (int)c_co2_temp != 999) {
    if (a_co2_temp != a_co2 || b_co2_temp != b_co2 || c_co2_temp != c_co2) {
      Serial.println("Difference in server and current curves of CO2 detected, updating");
      a_co2 = a_co2_temp;
      b_co2 = b_co2_temp;
      c_co2 = c_co2_temp;
      commitCo2Curves();
    }
    else Serial.println("No changes in curve values of CO2 detected, no action required");
    Serial.println("All CO2 values received, none invalid");
  } else  {
    Serial.println("One or more CO2 values are invalid, nullifying");
    reConnect();
  }

}

void commitPMCurves() {
  int a_pm25_sig = 2, b_pm25_sig = 2, c_pm25_sig = 2;
  if (a_pm25 < 0) {
    a_pm25_sig = 1;
  }
  if (b_pm25 < 0) {
    b_pm25_sig = 1;
  }
  if (c_pm25 < 0) {
    c_pm25_sig = 1;
  }
  Serial.print("apmsig: ");
  Serial.println(a_pm25_sig);
  Serial.print("bpmsig: ");
  Serial.println(b_pm25_sig);
  Serial.print("cpmsig: ");
  Serial.println(b_pm25_sig);
  int a_pm25_int = ((int)abs(a_pm25)) % 256;
  int a_pm25_int2 = (abs(a_pm25) - a_pm25_int) / 256;
  int a_pm25_d1 = ((int)((a_pm25 * 100)) % 100);
  int a_pm25_d2 = ((int)(a_pm25 * 10000)) % 100;
  int b_pm25_int = ((int)abs(b_pm25)) % 256;
  int b_pm25_int2 = (abs(b_pm25) - b_pm25_int) / 256;
  int b_pm25_d1 = ((int)((b_pm25 * 100)) % 100);
  int b_pm25_d2 = ((int)(b_pm25 * 10000)) % 100;
  int c_pm25_int = ((int)abs(c_pm25)) % 256;
  int c_pm25_int2 = (abs(c_pm25) - c_pm25_int) / 256;
  int c_pm25_d1 = ((int)((c_pm25 * 100)) % 100);
  int c_pm25_d2 = ((int)(c_pm25 * 10000)) % 100;
  EEPROM.put(6, a_pm25_int);
  EEPROM.put(7, a_pm25_d1);
  EEPROM.put(8, a_pm25_d2);
  EEPROM.put(9, b_pm25_int);
  EEPROM.put(10, b_pm25_d1);
  EEPROM.put(11, b_pm25_d2);
  EEPROM.put(12, c_pm25_int);
  EEPROM.put(13, c_pm25_d1);
  EEPROM.put(14, c_pm25_d2);
  EEPROM.put(24, a_pm25_sig);
  EEPROM.put(25, b_pm25_sig);
  EEPROM.put(26, c_pm25_sig);
  EEPROM.put(106, a_pm25_int2);
  EEPROM.put(109, b_pm25_int2);
  EEPROM.put(112, c_pm25_int2);
  delay(2000);
  EEPROM.commit();
  Serial.println("PM curves commited");
}

void commitCo2Curves () {
  int a_co2_sig = 2, b_co2_sig = 2, c_co2_sig = 2;
  if (a_co2 < 0) {
    a_co2_sig = 1;
  }
  if (b_co2 < 0) {
    b_co2_sig = 1;
  }
  if (c_co2 < 0) {
    c_co2_sig = 1;
  }
  Serial.print("aco2sig: ");
  Serial.println(a_co2_sig);
  Serial.print("bco2sig: ");
  Serial.println(b_co2_sig);
  Serial.print("cco2sig: ");
  Serial.println(c_co2_sig);
  int a_co2_int = ((int)abs(a_co2)) % 256;
  int a_co2_int2 = (abs(a_co2) - a_co2_int) / 256;
  int a_co2_d1 = ((int)((abs(a_co2) * 100)) % 100);
  int a_co2_d2 = ((int)(abs(a_co2) * 10000)) % 100;
  int b_co2_int = ((int)abs(b_co2)) % 256;
  int b_co2_int2 = (abs(b_co2) - b_co2_int) / 256;
  int b_co2_d1 = ((int)((abs(b_co2) * 100)) % 100);
  int b_co2_d2 = ((int)(abs(b_co2) * 10000)) % 100;
  int c_co2_int = ((int)abs(c_co2)) % 256;
  int c_co2_int2 = (abs(c_co2) - c_co2_int) / 256;
  int c_co2_d1 = ((int)((abs(c_co2) * 100)) % 100);
  int c_co2_d2 = ((int)(abs(c_co2) * 10000)) % 100;
  EEPROM.put(15, a_co2_int);
  EEPROM.put(16, a_co2_d1);
  EEPROM.put(17, a_co2_d2);
  EEPROM.put(18, b_co2_int);
  EEPROM.put(19, b_co2_d1);
  EEPROM.put(20, b_co2_d2);
  EEPROM.put(21, c_co2_int);
  EEPROM.put(22, c_co2_d1);
  EEPROM.put(23, c_co2_d2);
  EEPROM.put(30, a_co2_sig);
  EEPROM.put(31, b_co2_sig);
  EEPROM.put(32, c_co2_sig);
  EEPROM.put(115, a_co2_int2);
  EEPROM.put(118, b_co2_int2);
  EEPROM.put(121, c_co2_int2);
  delay(2000);
  EEPROM.commit();
  Serial.println("CO2 curves committed");
}

//Reading the curves from EEPROM. The curves gained from online will be stored
//on the EEPROM and only updated if there is a difference found between the eeprom curves and server
void readCurves() {
  byte eepromRead;
  unsigned int a_pm25_int_read = EEPROM.get(6, eepromRead);
  unsigned int a_pm25_d1_read = EEPROM.get(7, eepromRead);
  unsigned int a_pm25_d2_read = EEPROM.get(8, eepromRead);
  unsigned int b_pm25_int_read = EEPROM.get(9, eepromRead);
  unsigned int b_pm25_d1_read = EEPROM.get(10, eepromRead);
  unsigned int b_pm25_d2_read = EEPROM.get(11, eepromRead);
  unsigned int c_pm25_int_read = EEPROM.get(12, eepromRead);
  unsigned int c_pm25_d1_read = EEPROM.get(13, eepromRead);
  unsigned int c_pm25_d2_read = EEPROM.get(14, eepromRead);
  unsigned int a_co2_int_read = EEPROM.get(15, eepromRead);
  unsigned int a_co2_d1_read = EEPROM.get(16, eepromRead);
  unsigned int a_co2_d2_read = EEPROM.get(17, eepromRead);
  unsigned int b_co2_int_read = EEPROM.get(18, eepromRead);
  unsigned int b_co2_d1_read = EEPROM.get(19, eepromRead);
  unsigned int b_co2_d2_read = EEPROM.get(20, eepromRead);
  unsigned int c_co2_int_read = EEPROM.get(21, eepromRead);
  unsigned int c_co2_d1_read = EEPROM.get(22, eepromRead);
  unsigned int c_co2_d2_read = EEPROM.get(23, eepromRead);
  unsigned int a_pm25_sig_read = EEPROM.get(24, eepromRead);
  unsigned int b_pm25_sig_read = EEPROM.get(25, eepromRead);
  unsigned int c_pm25_sig_read = EEPROM.get(26, eepromRead);
  unsigned int a_co2_sig_read = EEPROM.get(30, eepromRead);
  unsigned int b_co2_sig_read = EEPROM.get(31, eepromRead);
  unsigned int c_co2_sig_read = EEPROM.get(32, eepromRead);
  unsigned int a_pm25_int2_read = EEPROM.get(106, eepromRead);
  unsigned int b_pm25_int2_read = EEPROM.get(109, eepromRead);
  unsigned int c_pm25_int2_read = EEPROM.get(112, eepromRead);
  unsigned int a_co2_int2_read = EEPROM.get(115, eepromRead);
  unsigned int b_co2_int2_read = EEPROM.get(118, eepromRead);
  unsigned int c_co2_int2_read = EEPROM.get(121, eepromRead);
  
  if (a_pm25_d1_read == 255 || b_pm25_d1_read == 255 || c_co2_d1_read == 255) {
    Serial.println("Memory empty for PM2.5 calibration, using default curves");
    a_pm25 = 0.0061;
    b_pm25 = 0.0692;
    c_pm25 = 1.6286;
  } else {
    a_pm25 = ((a_pm25_int_read + a_pm25_int2_read * 256) + ((float)a_pm25_d1_read) / 100 + ((float)a_pm25_d2_read) / 10000) * pow(-1, a_pm25_sig_read);
    b_pm25 = ((b_pm25_int_read + b_pm25_int2_read * 256) + ((float)b_pm25_d1_read) / 100 + ((float)b_pm25_d2_read) / 10000) * pow(-1, b_pm25_sig_read);
    c_pm25 = ((c_pm25_int_read + c_pm25_int2_read * 256) + ((float)c_pm25_d1_read) / 100 + ((float)c_pm25_d2_read) / 10000) * pow(-1, c_pm25_sig_read);
    Serial.println("PM2.5 calibration read from eeprom");
    Serial.print("a_pm25: ");
    Serial.println(a_pm25, 4);
    Serial.print("b_pm25: ");
    Serial.println(b_pm25, 4);
    Serial.print("c_pm25: ");
    Serial.println(c_pm25, 4);
  }
  if (a_co2_d1_read == 255 || b_co2_d1_read == 255 || c_co2_d1_read == 255) {
    Serial.println("Memory empty for CO2 calibration, using default curves");
    a_co2 = 0;
    b_co2 = 1;
    c_co2 = 0;
  } else {
    a_co2 = ((a_co2_int_read + a_co2_int2_read * 256) + ((float)a_co2_d1_read) / 100 + ((float)a_co2_d2_read) / 10000) * pow(-1, a_co2_sig_read);
    b_co2 = ((b_co2_int_read + b_co2_int2_read * 256) + ((float)b_co2_d1_read) / 100 + ((float)b_co2_d2_read) / 10000) * pow(-1, b_co2_sig_read);
    c_co2 = ((c_co2_int_read + c_co2_int2_read * 256) + ((float)c_co2_d1_read) / 100 + ((float)c_co2_d2_read) / 10000) * pow(-1, c_co2_sig_read);
    Serial.println("CO2 calibration read from eeprom");
    Serial.print("a_co2: ");
    Serial.println(a_co2, 4);
    Serial.print("b_co2: ");
    Serial.println(b_co2, 4);
    Serial.print("c_co2: ");
    Serial.println(c_co2, 4);
  }
  if (a_co2_sig_read == 0 || b_co2_sig_read == 0 || c_co2_sig_read == 0) {
    Serial.println("CO2 sign byte invalid, rewriting.");
    updateCo2Signature();
  }
  if (a_pm25_sig_read == 0 || b_pm25_sig_read == 0 || c_pm25_sig_read == 0) {
    Serial.println("PM25 sign byte invalid, rewriting.");
    updatePMSignature();
  }
}

void readMemory() {
  for (int i = 1; i <= 512; i++ ) {
    byte eepromRead;
    Serial.print(i);
    Serial.print(": ");
    Serial.println(EEPROM.get(i, eepromRead));
  }
}

void updateCo2Signature() {
  //This is to record signs
  int a_co2_sig = 2, b_co2_sig = 2, c_co2_sig = 2;
  if (a_co2 < 0) {
    a_co2_sig = 1;
  }
  if (b_co2 < 0) {
    b_co2_sig = 1;
  }
  if (c_co2 < 0) {
    c_co2_sig = 1;
  }
  EEPROM.put(30, a_co2_sig);
  EEPROM.put(31, b_co2_sig);
  EEPROM.put(32, c_co2_sig);
  delay(1000);
  EEPROM.commit();
  Serial.println("Co2 Signatures Updated");
}

void updatePMSignature() {
  //This part is to store signs
  int a_pm25_sig = 2, b_pm25_sig = 2, c_pm25_sig = 2;
  if (a_pm25 < 0) {
    a_pm25_sig = 1;
  }
  if (b_pm25 < 0) {
    b_pm25_sig = 1;
  }
  if (c_pm25 < 0) {
    c_pm25_sig = 1;
  }
  EEPROM.put(24, a_pm25_sig);
  EEPROM.put(25, b_pm25_sig);
  EEPROM.put(26, c_pm25_sig);
  delay(1000);
  EEPROM.commit();
  Serial.println("PM25 Signatures Updated");
}



void loop() {


  
  delay(200);
  Serial.println("*******starting loop...");
  ESP.wdtFeed();
  pmSerial.enableRx(true);
  ESP.wdtFeed();
  co2Serial.enableRx(false);
  //Serial.println("**********finish enabling...");
  ESP.wdtFeed();
  pmSerial.flush();
  //Serial.println("**********finished flushing...");

  ESP.wdtFeed();
  //pmSerial.write(PMstartup, 7);
  delay(750);
 //Serial.println("**********ABOUT TO READ VOC...");
  readVOC();
 // Serial.println("**********VOC HAS BEEN READ...");
  ESP.wdtFeed();
  wdt_reset();
    
  
  if (pmSerial.find(0x42))
  {
     //Serial.println("**********pmSerial Find...");
     ESP.wdtFeed();
    ucRxBuf[0] = 0x42;
    int count = 1;
    while ( pmSerial.available() > 0 && count < 32 )
    {
      //Serial.println("**********reading pmSerial...");
      ESP.wdtFeed();
      ucRxBuf[count] = pmSerial.read();
      count++;
    }
     ESP.wdtFeed();
    if (count > 31 && ucRxBuf[1] == 0x4D)
    {
      //Serial.println("**********calculatingPM...");
      ESP.wdtFeed();
      calculatePM();
    }
    else
      return;  //bad data, start loop again
  }
  ESP.wdtFeed();
  //wdt_reset();
  //Serial.println("****Finished reading VOC and calculating");
  pmSerial.enableRx(false);
  ESP.wdtFeed();
  //Serial.println("****disabled pmSerial");
  co2Serial.enableRx(true);
  ESP.wdtFeed();
  //Serial.println("****enabled pmSerial");
  co2Serial.write(CO2startup, 7);
  ESP.wdtFeed();
 // Serial.println("****CO2startup co2Serial");
  delay(250);
  while (co2Serial.available() > 0)
  {
    ESP.wdtFeed();
    readCO2(co2Serial.read());
    //Serial.println("****Reading co2Serial");

  }
  //wdt_reset();
  ESP.wdtFeed();
  if (light_sensor_found)
  {
    ESP.wdtFeed();
    readLight();
      Serial.println("****Read Light Sensor");
  }
  //Serial.println("****about to upload data");
  if (activeConnection) uploadData();
  ESP.wdtFeed();
  //wdt_reset();
  delay(1000);
  ESP.wdtFeed();
  //wdt_reset();
  
  Serial.printf("loop heap size: %u\n", ESP.getFreeHeap());

  ESP.wdtFeed();
  //wdt_reset();


  delay(del);
  h = dht.readHumidity();
  t = dht.readTemperature();
  //originally !
  if (t == 0 || isnan(t) )   // or any kind of error
  {
    Serial.print( millis()); Serial.println(" **ERROR DHT ERROR**");

  }


  if (!isnan(h) && !isnan(t) && t != 0 && h != 0 ) //Good data
  {
    temp = t;
    rh = h;
    hIndex = dht.computeHeatIndex(t, h, false);  //calc heat index
  }

  
  wdt_reset();
  displayInfo();
  Serial.println("Info Displayed");
  addTime();
  Serial.println("Time added");

}
