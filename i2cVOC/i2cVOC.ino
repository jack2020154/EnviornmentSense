//Some notes on coding i2c taken from:
//https://howtomechatronics.com/tutorials/arduino/how-i2c-communication-works-and-how-to-use-it-with-arduino/


#include <Wire.h>


//Setting the address of the VOC Sensor
int voc_intcode[] = {0xB8, 0x00, 0xB8};
int voc_address = 0xB8;
//I'm not really sure if that is the real correct address. 


void setup() {
  // put your setup code here, to run once:
  
  Serial.begin(9600);
  Wire.begin();

  

}

void i2cSend(int sentData) {
  Wire.beginTransmission(voc_address);
  Wire.write(sentData);
  Wire.endTransmission();
  while(Wire.available() == 0) {
    int confirmByte = Wire.read();
  }
  if (confirmByte == 1) {
    Serial.println("Write " sentData " acknowledged");
  } else {
    Serial.println("Write " sentData " not acknowledged");
  }
}

void i2cReceive(int receiveAddr, int reveiceBytes) {
  int receivedArray[] = Wire.requestFrom(receiveAddr, receiveBytes);
}

void printValues(int arrayData) {
  for(int i, receivedArray[i] != 0, i++) {
    Serial.println(receivedArray[i]);
  }
}

void loop() {
  i2cSend(0xB8);
  i2cSend(0x00);
  i2cReceive(0xB9, 5);
  printValues(receivedArray);
}
