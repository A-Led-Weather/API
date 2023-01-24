CREATE TABLE LOCATION (
                          locationId INT PRIMARY KEY AUTO_INCREMENT,
                          locationName VARCHAR(100) NOT NULL,
                          latitude FLOAT NOT NULL,
                          longitude FLOAT NOT NULL,
                          UNIQUE (locationName)

);

CREATE TABLE DEVICE (
                        deviceId INT PRIMARY KEY AUTO_INCREMENT,
                        deviceUuid VARCHAR(100) NOT NULL,
                        token VARCHAR(255) NOT NULL,
                        model VARCHAR(255) NOT NULL,
                        locationName VARCHAR(100) NOT NULL,
                        FOREIGN KEY (locationName) REFERENCES LOCATION(locationName),
                        UNIQUE (deviceUuid)
);

CREATE TABLE REPORT (
                        reportId INT PRIMARY KEY AUTO_INCREMENT,
                        temperature VARCHAR(255) NOT NULL,
                        humidity VARCHAR(255) NOT NULL,
                        dateTime DATETIME NOT NULL,
                        deviceUuid VARCHAR(100) NOT NULL,
                        locationName VARCHAR(100) NOT NULL,
                        FOREIGN KEY (deviceUuid) REFERENCES DEVICE(deviceUuid),
                        FOREIGN KEY (locationName) REFERENCES LOCATION(locationName)
);

CREATE TABLE USER (
                      userId INT PRIMARY KEY AUTO_INCREMENT,
                      userName VARCHAR(255) NOT NULL,
                      userEmail VARCHAR(100) NOT NULL,
                      userPassword VARCHAR(255) NOT NULL,
                      token VARCHAR(255),
                      UNIQUE (userEmail)
);

CREATE INDEX device_location_idx ON DEVICE(locationName);
CREATE INDEX report_deviceUuid_idx ON REPORT(deviceUuid);
CREATE INDEX report_location_idx ON REPORT(locationName);
CREATE UNIQUE INDEX device_uuid_idx ON DEVICE(deviceUuid);
CREATE UNIQUE INDEX user_userEmail_idx ON USER(userEmail);

INSERT INTO LOCATION (locationName, latitude, longitude) VALUES
('lyon', 45.7604, 4.84966);

INSERT INTO DEVICE (deviceUuid, token, model, locationName) VALUES
('78bccd19e274r', 'c56hs8uu675fr9gcxx6684332', 'ESP8266-O1S', 'lyon');

INSERT INTO REPORT (temperature, humidity, dateTime, deviceUuid, locationName) VALUES
(22.34, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(23.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(24.56, 22.10, NOW(), '78bccd19e274r', 'lyon'),
(25.78, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(26.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(22.34, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(23.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(24.56, 22.10, NOW(), '78bccd19e274r', 'lyon'),
(25.78, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(26.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(22.34, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(23.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(24.56, 22.10, NOW(), '78bccd19e274r', 'lyon'),
(25.78, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(26.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(22.34, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(23.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(24.56, 22.10, NOW(), '78bccd19e274r', 'lyon'),
(25.78, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(26.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(22.34, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(23.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(24.56, 22.10, NOW(), '78bccd19e274r', 'lyon'),
(25.78, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(26.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(22.34, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(23.12, 21.60, NOW(), '78bccd19e274r', 'lyon'),
(24.56, 22.10, NOW(), '78bccd19e274r', 'lyon'),
(25.78, 20.50, NOW(), '78bccd19e274r', 'lyon'),
(26.12, 21.60, NOW(), '78bccd19e274r', 'lyon');