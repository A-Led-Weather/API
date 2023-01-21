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