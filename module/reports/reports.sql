CREATE TABLE reports
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    reportName VARCHAR(100) UNIQUE
);

CREATE TABLE reports_hosts
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    hostName VARCHAR(100),
    reportsID INT
);

CREATE TABLE reports_services
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    serviceName VARCHAR(100),
    reportsID INT
);
