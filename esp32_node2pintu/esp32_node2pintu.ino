/*
 * ============================================================
 * ESP32 Node 2 - Pintu Masuk/Keluar Museum
 * ============================================================
 * Hardware:
 *   - 2x PN532 RFID reader via TCA9548A I2C multiplexer
 *       Channel 0 = PN532_MASUK  (reader di luar pintu)
 *       Channel 1 = PN532_KELUAR (reader di dalam pintu)
 *   - Relay  GPIO13 (HIGH = selenoid buka, LOW = selenoid tutup)
 *   - Buzzer GPIO33 (passive, via ledc)
 *
 * Alur:
 *   Tap kartu Masuk  -> POST /api/akses-masuk  ke Laravel
 *   Tap kartu Keluar -> POST /api/akses-keluar ke Laravel
 *
 * Laravel meng-orchestrate verifikasi AI (hanya untuk masuk).
 * Laravel mengembalikan {"status":"granted"} atau {"status":"denied","reason":"..."}
 *
 * ESP32 bertindak sebagai client:
 *   granted -> selenoid HIGH 5 detik, beep sukses 2x
 *   denied  -> buzzer 5000ms, selenoid tetap LOW
 *   error   -> buzzer 5000ms, selenoid tetap LOW
 * ============================================================
 */

#include <Wire.h>
#include <Adafruit_PN532.h>
#include <esp_arduino_version.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// ====================
// WIFI CONFIG
// ====================
const char* ssid       = "MUSEUM SENI";
const char* password   = "baksomalang";

IPAddress staticIP(10, 109, 1, 5);
IPAddress gateway(10, 109, 1, 1);
IPAddress subnet(255, 255, 255, 240);
IPAddress primaryDNS(8, 8, 8, 8);
IPAddress secondaryDNS(8, 8, 4, 4);

// ====================
// LARAVEL SERVER
// ====================
const char* aksesMasukURL  = "http://10.109.1.6:8000/api/akses-masuk";
const char* aksesKeluarURL = "http://10.109.1.6:8000/api/akses-keluar";

// ====================
// PIN CONFIG
// ====================
#define RELAY_PIN   13
#define BUZZER_PIN  33

// ====================
// TCA9548A CONFIG
// ====================
#define TCA_ADDR        0x70
#define PN532_MASUK     1
#define PN532_KELUAR    0

// ====================
// TIME CONFIG
// ====================
#define RFID_TIMEOUT_MS    50
#define SELENOID_OPEN_MS   5000
#define BUZZER_ERROR_MS    5000
#define READ_DELAY_MS      1000
#define HTTP_TIMEOUT_MS    12000
#define WIFI_RECONNECT_MS  10000

// ====================
// PN532 OBJECT
// ====================
Adafruit_PN532 nfc(-1, -1);

// ====================
// BUZZER CONFIG (ESP32 Core v3.x)
// ====================
#if ESP_ARDUINO_VERSION_MAJOR >= 3

void buzzerInit() {
  ledcAttach(BUZZER_PIN, 2500, 8);
  ledcWrite(BUZZER_PIN, 0);
}

void buzzerOn(uint16_t freq) {
  ledcWriteTone(BUZZER_PIN, freq);
  ledcWrite(BUZZER_PIN, 128);
}

void buzzerOff() {
  ledcWrite(BUZZER_PIN, 0);
  ledcWriteTone(BUZZER_PIN, 0);
}

#else

#define BUZZER_CHANNEL 0

void buzzerInit() {
  ledcSetup(BUZZER_CHANNEL, 2500, 8);
  ledcAttachPin(BUZZER_PIN, BUZZER_CHANNEL);
  ledcWrite(BUZZER_CHANNEL, 0);
}

void buzzerOn(uint16_t freq) {
  ledcWriteTone(BUZZER_CHANNEL, freq);
  ledcWrite(BUZZER_CHANNEL, 128);
}

void buzzerOff() {
  ledcWrite(BUZZER_CHANNEL, 0);
  ledcWriteTone(BUZZER_CHANNEL, 0);
}

#endif

// ====================
// TCA9548A SELECT
// ====================
void selectTCA(uint8_t channel) {
  if (channel > 7) return;
  Wire.beginTransmission(TCA_ADDR);
  Wire.write(1 << channel);
  Wire.endTransmission();
}

// ====================
// RELAY CONTROL
// ====================
// HIGH = selenoid BUKA (relay aktif-HIGH)
// LOW  = selenoid TUTUP
void relayClose() {
  digitalWrite(RELAY_PIN, LOW);
}

void relayOpen() {
  digitalWrite(RELAY_PIN, HIGH);
}

void triggerSelenoid() {
  Serial.println("Selenoid BUKA 5 detik");
  relayOpen();
  delay(SELENOID_OPEN_MS);
  relayClose();
  Serial.println("Selenoid TUTUP");
}

// ====================
// BUZZER SOUND
// ====================
void beep(uint16_t freq, uint16_t durationMs) {
  buzzerOn(freq);
  delay(durationMs);
  buzzerOff();
}

void beepReady() {
  beep(2000, 150);
  delay(100);
  beep(2500, 150);
}

void beepTap() {
  beep(2500, 200);
}

void beepSuccess() {
  beep(2500, 200);
  delay(100);
  beep(2500, 200);
}

void buzzerError() {
  Serial.println("BUZZER ERROR 5 detik");
  buzzerOn(1000);
  delay(BUZZER_ERROR_MS);
  buzzerOff();
}

// ====================
// PN532 INIT
// ====================
void initPN532(uint8_t channel, const char *name) {
  selectTCA(channel);
  nfc.begin();
  if (!nfc.getFirmwareVersion()) {
    Serial.print("PN532 ");
    Serial.print(name);
    Serial.println(" tidak ditemukan!");
    return;
  }
  nfc.SAMConfig();
  Serial.print("PN532 ");
  Serial.print(name);
  Serial.println(" siap.");
}

// ====================
// UID FORMAT
// ====================
// PN532 mengembalikan UID dalam urutan LSB-first (byte[0] = LSB).
// USB reader (PC/SC) menyimpan UID sebagai MSB-first integer.
// Untuk kompatibilitas dengan database Laravel (yang diisi via USB reader),
// kita balik urutan byte agar sama dengan format PC/SC.
String getUIDString(uint8_t *uid, uint8_t uidLength) {
  String result = "";
  for (int8_t i = uidLength - 1; i >= 0; i--) {
    if (uid[i] < 0x10) result += "0";
    result += String(uid[i], HEX);
  }
  result.toUpperCase();
  return result;
}

// ====================
// WIFI CONNECT
// ====================
void connectWiFi() {
  Serial.print("Menghubungkan WiFi: ");
  Serial.println(ssid);

  WiFi.setSleep(false);
  WiFi.mode(WIFI_STA);

  if (!WiFi.config(staticIP, gateway, subnet, primaryDNS, secondaryDNS)) {
    Serial.println("[ERROR] Gagal set Static IP!");
  }

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("[OK] WiFi Terhubung!");
  Serial.print("IP ESP32 (Static): ");
  Serial.println(WiFi.localIP());
}

void reconnectWiFi() {
  if (WiFi.status() == WL_CONNECTED) return;

  Serial.println("[WARN] WiFi terputus! Mencoba reconnect...");
  WiFi.disconnect();
  WiFi.begin(ssid, password);

  unsigned long startAttempt = millis();
  while (WiFi.status() != WL_CONNECTED &&
         millis() - startAttempt < WIFI_RECONNECT_MS) {
    delay(500);
    Serial.print(".");
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println();
    Serial.println("[OK] WiFi reconnected.");
  } else {
    Serial.println();
    Serial.println("[ERROR] WiFi reconnect gagal.");
  }
}

// ====================
// HANDLE RFID
// ====================
bool checkRFID(uint8_t channel, const char *name, const char *url) {
  uint8_t uid[7];
  uint8_t uidLength;

  selectTCA(channel);

  bool cardDetected = nfc.readPassiveTargetID(
    PN532_MIFARE_ISO14443A,
    uid,
    &uidLength,
    RFID_TIMEOUT_MS
  );

  if (!cardDetected) return false;

  String uidString = getUIDString(uid, uidLength);

  Serial.println();
  Serial.print("[");
  Serial.print(name);
  Serial.println("] Kartu terdeteksi");
  Serial.print("UID (HEX): ");
  Serial.println(uidString);

  beepTap();

  // Kirim ke Laravel
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[ERROR] WiFi tidak terhubung saat tap!");
    buzzerError();
    delay(READ_DELAY_MS);
    return true;
  }

  HTTPClient http;
  http.setTimeout(HTTP_TIMEOUT_MS);
  http.begin(url);
  http.addHeader("Content-Type", "application/json");

  // Build JSON body menggunakan ArduinoJson v7
  String payload;
  JsonDocument doc;
  doc["uid_kartu"] = uidString;
  serializeJson(doc, payload);

  Serial.print("POST -> ");
  Serial.println(url);
  Serial.print("Body: ");
  Serial.println(payload);

  int httpResponseCode = http.POST(payload);

  if (httpResponseCode > 0) {
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);

    String response = http.getString();
    Serial.print("Response: ");
    Serial.println(response);

    // Parse JSON response
    JsonDocument respDoc;
    DeserializationError error = deserializeJson(respDoc, response);

    if (error) {
      Serial.print("[ERROR] JSON parse failed: ");
      Serial.println(error.c_str());
      buzzerError();
    } else {
      const char* status = respDoc["status"] | "";

      if (strcmp(status, "granted") == 0) {
        Serial.println("[AKSES DIKENALI] Selenoid buka 5 detik.");
        beepSuccess();
        triggerSelenoid();
      } else {
        const char* reason = respDoc["reason"] | "unknown";
        Serial.print("[AKSES DITOLAK] Reason: ");
        Serial.println(reason);
        buzzerError();
      }
    }
  } else {
    Serial.print("[ERROR] HTTP POST gagal, code: ");
    Serial.println(httpResponseCode);
    buzzerError();
  }

  http.end();

  delay(READ_DELAY_MS);
  return true;
}

// ====================
// SETUP
// ====================
void setup() {
  Serial.begin(115200);

  Wire.begin();  // SDA = GPIO21, SCL = GPIO22

  pinMode(RELAY_PIN, OUTPUT);
  relayClose();

  buzzerInit();
  buzzerOff();

  Serial.println("=== Node 2 Pintu - Museum Seni ===");

  // Hubungkan WiFi (blocking sampai sukses)
  connectWiFi();

  // Inisialisasi PN532
  Serial.println("Memulai sistem RFID...");
  initPN532(PN532_MASUK, "Pintu Masuk");
  delay(500);
  initPN532(PN532_KELUAR, "Pintu Keluar");
  delay(500);

  Serial.println("Sistem siap. Tap kartu RFID.");
  beepReady();
}

// ====================
// LOOP
// ====================
void loop() {
  // Auto-reconnect WiFi kalau putus
  reconnectWiFi();

  // Baca RFID kedua reader bergantian
  checkRFID(PN532_MASUK, "MASUK", aksesMasukURL);
  checkRFID(PN532_KELUAR, "KELUAR", aksesKeluarURL);

  // Safety default state
  relayClose();
  buzzerOff();
}