#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <DNSServer.h>

const char* ssidAP = "ESP";
const char* pwdAP = "longpassword";
const char* ssidSTA = "Jack's iPhone";
const char* pwdSTA = "strongpassword";

const byte DNS_PORT = 53;
IPAddress apIP(192, 168, 1, 1);
DNSServer dnsServer;
ESP8266WebServer webServer(80);

String webpage =
  "<!DOCTYPE html>"
  "<html>"
  "  <head>"
  "    <title>It Works?</title>"
  "    <input name = 'strinput' id='text1' value='stringinput'>"
  "    <script type='text/javascript'>"
  "    function request() {"
  "      var inputdata = document.getElementById('text1').value;"
  "      var req = new XMLHttpRequest();"
  "      req.open('POST', '?' + 'newstring=' + inputdata, true);"
  "      req.send();"
  "    };"
  "    </script>"
  "    <button type='submit' id='login-button' Onclick='request(); return true;' >Submit</button>"
  "  </head>"
  "  <script type = 'text/javascript'>"
  "  </script>"
  "</html>"
  ;

void setup() {
  Serial.begin(115200);
  WiFi.mode(WIFI_OFF);
  delay(1000);
  APMode();
  webServer.on("/", handleRoot);
  webServer.begin();


}

void APMode() {
  Serial.println("AP Mode");
  WiFi.mode(WIFI_AP);
  WiFi.softAPConfig(apIP, apIP, IPAddress(255, 255, 255, 0));
  WiFi.softAP(ssidAP, pwdAP, 8, false, 2);
  dnsServer.start(DNS_PORT, "rgb", apIP);
  Serial.print("Network created with SSID: ");
  Serial.println(ssidAP);
  Serial.print("Network password: ");
  Serial.println(pwdAP);
}

void STAMode() {
  Serial.println("STA Mode");
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssidSTA, pwdSTA);
  Serial.print("Connecting to ");
  Serial.print(ssidSTA);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("Connected");
  Serial.print("IP: ");
  Serial.println(ipToString(WiFi.localIP()));
}

void handleRoot() {
  String dataIn = webServer.arg(0);
  Serial.println(dataIn);
  webServer.send(200, "text/html", webpage);
}

String ipToString(IPAddress ip) {
  String s = "";
  for (int i = 0; i < 4; i++)
    s += i  ? "." + String(ip[i]) : String(ip[i]);
  return s;
}

void loop() {
  dnsServer.processNextRequest();
  webServer.handleClient();

}
