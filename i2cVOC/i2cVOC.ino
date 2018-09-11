//Some notes on coding i2c taken from:
//https://howtomechatronics.com/tutorials/arduino/how-i2c-communication-works-and-how-to-use-it-with-arduino/


#include <Wire.h>


//Setting the address of the VOC Sensor
int voc_intcode[] = {0xB8, 0x00, 0xB8};
int voc_address = 0xB8;
int *a, *b, *c, *d, *e;
//I'm not really sure if that is the real correct address. 


void setup() {
  // put your setup code here, to run once:
  
  Serial.begin(9600);
  Wire.begin();

  

}

void i2cSend(int voc_address, int sentData) {
  int confirmByte;
  Wire.beginTransmission(voc_address);
  Wire.write(sentData);
  Wire.endTransmission();
}

void i2cReceive(int receiveAddr, int receiveBytes) {
  Wire.requestFrom(receiveAddr, receiveBytes);
 *a = Wire.read();
 *b = Wire.read();
 *c = Wire.read();
 *d = Wire.read();
 *e = Wire.read();
}

void printValues() {
  Serial.println("Incoming Data: ");
  Serial.println(*a);
  Serial.println(*b);
  Serial.println(*c);
  Serial.println(*d);
  Serial.println(*e);
}

void loop() {
  Serial.println("Begin Data Transfer");
  i2cSend(0xB8, 0);
  i2cReceive(0xB9, 5);
  printValues();
  delay(2000);
}
