//Some notes on coding i2c taken from:
//https://howtomechatronics.com/tutorials/arduino/how-i2c-communication-works-and-how-to-use-it-with-arduino/


#include <Wire.h>
int voc_address = 0xB8;
int highByteVOC, lowByteVOC, rsvdHighByte, rsvdLowByte, checksumByte;

void setup() {
  Serial.begin(9600);
  Wire.begin();
}

void i2cSend(int voc_address, int sentData) {
  Wire.beginTransmission(voc_address);
  Wire.write(sentData);
  Wire.endTransmission();
}

void i2cReceive(int receiveAddr, int receiveBytes) {
  Wire.requestFrom(receiveAddr, receiveBytes);
   while(Wire.available() == 0) {
    Serial.println("Waiting for bytes");
    highByteVOC = Wire.read();
    lowByteVOC = Wire.read();
    rsvdHighByte = Wire.read();
    rsvdLowByte = Wire.read(); 
    checksumByte = Wire.read();
  }
 }

void printValues() {
  Serial.println("Incoming Data: ");
  Serial.print("High VOC Byte: ");
  Serial.println(highByteVOC);
  Serial.print("Low VOC Byte: ");
  Serial.println(lowByteVOC);
  Serial.print("High Reserved Byte: ");
  Serial.println(rsvdHighByte);
  Serial.print("Low Reserved Byte: ");
  Serial.println(rsvdLowByte);
  Serial.print("Checksum Byte: ");
  Serial.println(checksumByte);
  
}

void loop() {
  Serial.println("Begin Data Transfer");
  i2cSend(0x5C, 0);
  i2cReceive(0x5D, 5);
  printValues();
  delay(2000);
}
