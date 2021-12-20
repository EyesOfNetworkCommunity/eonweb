ALTER TABLE sessions modify session_id char(32);

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE reports
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    reportName VARCHAR(100) UNIQUE,
    period VARCHAR(20),
    cron VARCHAR(20)
);

--
-- Table structure for table `reports_hosts`
--

DROP TABLE IF EXISTS `reports_hosts`;
CREATE TABLE reports_hosts
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    hostName VARCHAR(100),
    reportsID INT
);

--
-- Table structure for table `reports_services`
--

DROP TABLE IF EXISTS `reports_services`;
CREATE TABLE reports_services
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    serviceName VARCHAR(100),
    reportsID INT
);

--
-- Table structure for table `reports_emails`
--

DROP TABLE IF EXISTS `reports_emails`;
CREATE TABLE reports_emails
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email VARCHAR(100),
    reportsID INT
);
