CREATE TABLE reports
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    reportName VARCHAR(100) UNIQUE,
    period VARCHAR(20),
    cron VARCHAR(20)
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

CREATE TABLE reports_emails
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email VARCHAR(100),
    reportsID INT
);
