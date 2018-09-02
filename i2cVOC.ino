//Some notes on coding i2c taken from:
//https://howtomechatronics.com/tutorials/arduino/how-i2c-communication-works-and-how-to-use-it-with-arduino/


#include <Wire.h>


//Setting the address of the VOC Sensor
int voc_address = 0xB8
//I'm not really sure if that is the real correct address. 


void setup() {
  // put your setup code here, to run once:
  
  Serial.begin(9600);
  Wire.begin;

  

}

void loop() {
  // put your main code here, to run repeatedly:


  //Probably put all of this into one function, but this should be the gist of i2c for a single sensor.
  //As long as you have the address of the sensor and use it then it should be fine. 


  //Start talking
  Wire.beginTransmission(voc_address);
  //Asking for register zero
  Wire.write(0);
  //Complete the Transmission
  Wire.endTransmission();
  //Request 1 byte
  Wire.requestFrom(voc_address, 1);
  //just waits for the response, might change this later
  while(Wire.available() == 0);
  //get the information and stores it into the variable
  int v = Wire.read(); 
  //print for the sake of printing
  Serial.print(v);
  
  

}
