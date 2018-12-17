//******************************
//
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
//*Version：V0.8
//*Author：Joel Klammer
//*Date：May 11, 2018
//******************************
//*****  Revision History  *****
//******************************
// v0.9 Eliminated local wifi router - changed WIFI_AP_STA to WIFI_STA due to library updates
// v0.8 Added JSON page at port 8080
// v0.7 Added support for Light Sensor, switched SDA & SCL for Wire library
// v0.6 Added asynchronous webpage support, graphic welcome screen
// v0.5

#include <SoftwareSerial.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <FS.h>
#include <ESPAsyncTCP.h>
#include <ESPAsyncWebServer.h>
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
#define DHTPIN 2             // D4: digital pin sensor is connected to
DHT dht(DHTPIN, DHTTYPE);

Adafruit_TSL2591 light_sensor = Adafruit_TSL2591(2591); // pass in a number for the sensor identifier (for your use later)
CCS811Core::status errorStatus;

SoftwareSerial pmSerial(13, 15, false, 256);    // PM RX, TX
SoftwareSerial co2Serial(14, 12, false, 256);   // CO2 RX, TX

bool activeConnection = true;

bool wifiConnection = true;

bool vocConnected = true;
bool baselineAvailable = false;
bool baselineLoaded = false;
byte eeprom0, eeprom1, eeprom4, eeprom5;
unsigned int eeprom2, eeprom3;
unsigned int result;
String VOClevels;

int vocLevels = -1;
int vocCO2 = -1;
int vocTVOC = -1;
String macAddr;

const char* location = "esp_10";
const char* ssid = "CISS_Employees_Students";
const char* password = "";
const char* ssidAlt = "CISS_Visitors";
const char* passwordAlt = "";

static unsigned long uploadInterval = 1000 * 60 * 5;//ms between uploads
static unsigned long vocWarmup = 1000 * 60 * 20;
static unsigned long vocBurnin = 48 * 60; // Time for VOC burnin, 2880 minutes
const byte DNS_PORT = 53;
String webpage = "", JSON = "";

//Correction to the PM2.5 sensor of the form: Corrected = a*Raw^2 + b*Raw + c
const static double a_pm25 = 0.0061;
const static double b_pm25 = 0.0692;
const static double c_pm25 = 1.6286;

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
static byte LED = 16;                 //D0 (GPIO 16)


AsyncWebServer server(80);
AsyncWebServer JSONserver(8080);
CCS811 vocSensor(CCS811_ADDR);

void setup() {
  ESP.wdtDisable();
  ESP.wdtEnable(WDTO_8S);
  dht.begin();
  light_sensor_found = light_sensor.begin();
  if (light_sensor_found)
  {
    light_sensor.setGain(TSL2591_GAIN_MED);      // 25x gain
    light_sensor.setTiming(TSL2591_INTEGRATIONTIME_300MS);
  }
  display.init();
  display.flipScreenVertically();
  display.setTextAlignment(TEXT_ALIGN_CENTER);

  display.drawXbm(0, 0, concordia2_width, concordia2_height, concordia2_bits);
  display.setFont(ArialMT_Plain_16);
  display.drawString(64, 40, location);
  display.display();
  display.setTextAlignment(TEXT_ALIGN_LEFT);

  Serial.begin(115200);
  pmSerial.begin(9600);
  co2Serial.begin(9600);
  pinMode(LED, OUTPUT);

  EEPROM.begin(512);
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
  //WIFI_OFF changing WiFi mode to WIFI_OFF along with WiFi.forceSleepBegin() will put wifi into a low power state, provided wifi.nullmodesleep(false) has not been called.
  if (wifiConnection) {
    WiFi.mode(WIFI_OFF);
    delay(1000);
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
      if (i > 20) {
        activeConnection = false;
        break;
      }
    }
    if (activeConnection) {
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
    }
    else if (!activeConnection) {
      WiFi.mode(WIFI_OFF);
      delay(500);
      Serial.println();
      Serial.print("Connection to ");
      Serial.print(ssid);
      Serial.println(" failed after 10 seconds.");
      Serial.print("Attempting to connect to ");
      Serial.print(ssidAlt);
      WiFi.begin(ssidAlt, passwordAlt);
      activeConnection = true;
      i = 0;
      while (WiFi.status() != WL_CONNECTED)
      {
        delay(500);
        Serial.print(".");
        i++;
        if (i > 20) {
          activeConnection = false;
          break;
        }
      }
      if (activeConnection) {
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
      } else if (!activeConnection) {
        IP = "NO CONNECTION";
        Serial.println();
        Serial.println("Connection to both networks failed.");
      }
    }
  }
else if (!wifiConnection) IP = "TEST MODE";
}
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

    loopCnt++;      //do averages
    pm10_avg += pm10;
    pm25_avg += pm25_corrected;
    pm100_avg += pm100;
    temp_avg += temp;
    rh_avg += rh;
    co2_avg += co2;
    lux_avg += int(lux + 0.5);
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
    ucCO2RxCnt = 0;
  }
  if ( co2 < 350 ) {       //bad read
    co2 = old_co2;
  }
}

void displayInfo() {

  Serial.println("----------------------------------");

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
  Serial.println("----------------------------------");
  Serial.println();

  display.clear();
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
    s = "VOC CO2: ";
    s += String (vocCO2);
    s += " ppm";
    display.drawString(0, 27, s);



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
      http.begin("http://sms.concordiashanghai.org/bdst_insert.php"); //HTTP
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

      } else {
        Serial.printf("[HTTP] POST... failed, error: %s\n", http.errorToString(httpCode).c_str());
        PostError++;
      }
      http.end();
    } else {
      Serial.println("******************* WiFi DISCONNECTED!!******");
    }
  }
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
  unsigned int memval4 = EEPROM.get(4, eeprom4);
  unsigned int memval5 = EEPROM.get(5, eeprom5);
  unsigned int currentTime = memval4 * 256 + memval5;
  bool vocTime = millis() > vocWarmup;
  bool burnInTime = currentTime > vocBurnin;
  if (!baselineAvailable && !burnInTime) {
    VOClevels =  (String)(currentTime * 100 / vocBurnin) + "% Burn";
    vocSensor.readAlgorithmResults();
    vocCO2 = vocSensor.getCO2();
    vocTVOC = -1;
  }
  else if (!baselineAvailable && burnInTime) {
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
      baselineLoaded = true;
      Serial.println("Baseline loaded");
      VOClevels =  "BOOTING";
    }
    else {
     baselineLoaded = false;
     VOClevels = "ERROR";
    }
  }

  else if (baselineAvailable && !baselineLoaded) {
    VOClevels =  "ERROR";
    baselineAvailable = false;
    vocSensor.readAlgorithmResults();
    vocCO2 = vocSensor.getCO2();
    vocTVOC = -1;
  }
  else if (baselineAvailable && baselineLoaded && !vocTime) {
    VOClevels =  (String)(millis() * 100 / vocWarmup) + "% Warm";
    vocSensor.readAlgorithmResults();
    vocCO2 = vocSensor.getCO2();
    vocTVOC = -1;
  }
  else if (baselineAvailable && baselineLoaded && vocTime) {
    if (vocSensor.dataAvailable()) {
      vocSensor.readAlgorithmResults();
      vocCO2 = vocSensor.getCO2();
      vocTVOC = vocSensor.getTVOC();
      VOClevels =  (String)vocTVOC;
    }
  }
}

void addTime() {
  if (millis() % 60000 < 2000 ) {
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
    EEPROM.put(4, (currentTime >> 8) & 0x00FF);
    EEPROM.put(5, currentTime & 0x00FF);
    EEPROM.commit();
  }
}

void loop() {
  ESP.wdtFeed();
  pmSerial.enableRx(true);
  co2Serial.enableRx(false);
  digitalWrite(LED, LOW);
  pmSerial.flush();
  //pmSerial.write(PMstartup, 7);
  delay(750);
  readVOC();
  wdt_reset();
  if (pmSerial.find(0x42))
  {
    ucRxBuf[0] = 0x42;
    int count = 1;
    while ( pmSerial.available() > 0 && count < 32 )
    {
      ucRxBuf[count] = pmSerial.read();
      count++;
    }
    if (count > 31 && ucRxBuf[1] == 0x4D)
    {
      calculatePM();
    }
    else
      return;  //bad data, start loop again
  }
  wdt_reset();
  digitalWrite(LED, HIGH);

  pmSerial.enableRx(false);
  co2Serial.enableRx(true);
  co2Serial.write(CO2startup, 7);
  delay(250);
  while (co2Serial.available() > 0)
  {
    readCO2(co2Serial.read());
  }
  wdt_reset();
  if (light_sensor_found)
  {
    readLight();
  }
  if (activeConnection)uploadData();
  wdt_reset();
  delay(1000);
  wdt_reset();
  digitalWrite(LED, LOW);

  h = dht.readHumidity();
  t = dht.readTemperature();
  !isnan(h) && !isnan(t) && t != 0 && h != 0 ) //Good data
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
  digitalWrite(LED, HIGH);
}
