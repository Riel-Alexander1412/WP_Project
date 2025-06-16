USE job_finder_system;

CREATE TABLE `admin` (
  Email varchar(200) NOT NULL PRIMARY KEY,
  Password varchar(20) NOT NULL,
  Name varchar(20) NOT NULL,
  LastActive timestamp NULL DEFAULT NULL
);

CREATE TABLE `employer` (
  Email varchar(20) NOT NULL PRIMARY KEY,
  Password varchar(200) NOT NULL,
  Name varchar(50) NOT NULL COMMENT 'Name of user or company',
  Contact varchar(50) NOT NULL,
  Address varchar(50) NOT NULL,
  Description varchar(3000) NOT NULL COMMENT 'Company Description',
  Image varchar(200) NOT NULL
);

CREATE TABLE `user` (
  Name varchar(100) NOT NULL COMMENT 'Full Legal names of users',
  Email varchar(20) NOT NULL COMMENT 'User email for login, verification and notice' PRIMARY KEY,
  Password varchar(200) NOT NULL COMMENT 'User password. Saved in HASH format',
  PhoneNum varchar(18) NOT NULL COMMENT 'User phone number',
  Address varchar(100) NOT NULL COMMENT 'User address',
  COO varchar(40) NOT NULL COMMENT 'User Country of Origin(COO)',
  DoB date NOT NULL COMMENT 'User Date of Birth(DoB)',
  Gender varchar(40) NOT NULL COMMENT 'User Gender(Apache Helicopter)',
  HiEdu varchar(50) NOT NULL COMMENT 'Highest Level of Education for user(SPM/GCSE, Diploma, Degree etc)',
  UniFeat text NOT NULL COMMENT 'Unique Features for each users to tell the employer why they should hire them over others.',
  Resume varchar(200) NOT NULL COMMENT 'Resume File',
  Image varchar(200) NOT NULL
);

CREATE TABLE job_listing (
    ListingID int(20) NOT NULL PRIMARY KEY,
    Position varchar(100) NOT NULL,
    EmployerID varchar(20) NOT NULL,
    JbLV varchar(20) NOT NULL COMMENT 'Level for Applied Position(Entry, Intermediate etc)',
    MinLV varchar(20) NOT NULL COMMENT 'Minimum level of education(Diploma/GCSE)',
    CourseType varchar(50) NOT NULL COMMENT 'Course/Major Type(Science Com, Adminstration, Accounting, Busniess etc)',
    CType varchar(20) NOT NULL COMMENT 'Contract Type(Part Time, Full Time, Permanent)',
    Salary varchar(20) NOT NULL COMMENT 'Salary Range(Exp: 1499 - 2000)',
    Tags varchar(100) NOT NULL COMMENT 'Tags for SEO(Search Engine Optimization)',
    PostDate date NOT NULL COMMENT 'Date of Job Posted on',
    Location varchar(100) NOT NULL COMMENT 'Location of Job Position',
    Status varchar(20) NOT NULL,
    FOREIGN KEY (EmployerID) REFERENCES `employer`(Email)
);

CREATE TABLE applied_jobs (
    AppID int(20) NOT NULL PRIMARY KEY,
    UserEmail varchar(20) NOT NULL,
    JobID int,
    Notes varchar(500) NOT NULL,
    Date date NOT NULL,
    FOREIGN KEY (UserEmail) REFERENCES `user`(Email),
    FOREIGN KEY (JobID) REFERENCES `job_listing`(ListingID)
);


ALTER TABLE applied_jobs
  MODIFY AppID int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE job_listing
  MODIFY ListingID int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

INSERT INTO admin (Email, Password, Name, LastActive) VALUES('test1@email.com', '12345', 'God Awa', '2025-06-14 20:38:06');
INSERT INTO admin (Email, Password, Name, LastActive) VALUES('test2@email.com', '12345', 'God Fuka', '2025-06-14 20:38:06');
INSERT INTO admin (Email, Password, Name, LastActive) VALUES('test3@email.com', '12345', 'God Awawawa', '2025-06-14 20:38:06');
INSERT INTO admin (Email, Password, Name, LastActive) VALUES('test4@email.com', '12345', 'God Mika', '2025-06-14 20:38:06');
INSERT INTO admin (Email, Password, Name, LastActive) VALUES('test5@email.com', '12345', 'God Grid', '2025-06-14 20:38:06');
INSERT INTO admin (Email, Password, Name, LastActive) VALUES('test@email.com', '12345', 'God damneee', '2025-06-14 20:38:06');

INSERT INTO employer (Email, Password, Name, Contact, Address, Description, Image) VALUES('test@test.com', 'L Corp', 'L Corp', 'ergerger', 'errgerge', 'ergerg', '');
INSERT INTO employer (Email, Password, Name, Contact, Address, Description, Image) VALUES('test2@test.com', 'K Corp', 'T Corp', 'ergerger', 'errgerge', 'ergerg', '');
INSERT INTO employer (Email, Password, Name, Contact, Address, Description, Image) VALUES('test42@test.com', 'Limbus Company', 'Limbus Company', 'ergerger', 'errgerge', 'ergerg', '');
INSERT INTO employer (Email, Password, Name, Contact, Address, Description, Image) VALUES('test999@test.com', 'SCP Foundation', 'SCPF', 'ergerger', 'errgerge', 'ergerg', '');


INSERT INTO job_listing (Position, EmployerID, JbLV, MinLV, CourseType, CType, Salary, Tags, PostDate, Location, Status) VALUES('Software Engineer', 'test42@test.com', 'Entry', 'Diploma', 'Computer Science', 'Full Time', '5400', 'Software', '2025-06-15', 'UMP', 'Active');
INSERT INTO job_listing (Position, EmployerID, JbLV, MinLV, CourseType, CType, Salary, Tags, PostDate, Location, Status) VALUES('Software Engineer', 'test42@test.com', 'Entry', 'Degree', 'Computer Science', 'Full Time', '5040', 'Software', '2025-06-15', 'UMP', 'Suspended');
INSERT INTO job_listing (Position, EmployerID, JbLV, MinLV, CourseType, CType, Salary, Tags, PostDate, Location, Status) VALUES('Software Engineer', 'test2@test.com', 'Entry', 'Master', 'Computer Science', 'Full Time', '2444', 'Software', '2025-06-15', 'UMP', 'Ended');
INSERT INTO job_listing (Position, EmployerID, JbLV, MinLV, CourseType, CType, Salary, Tags, PostDate, Location, Status) VALUES('Software Engineer', 'test999@test.com', 'Entry', 'Diploma', 'Computer Science', 'Full Time', '9099', 'Software', '2025-06-15', 'UMP', 'Active');


INSERT INTO `user`(`Name`, `Email`, `Password`, `PhoneNum`, `Address`, `COO`, `DoB`, `Gender`, `HiEdu`, `UniFeat`, `Resume`, `Image`) VALUES ('Teto','teto@teto.com','tet','12345','MikuStr','April','April','Pear','SynthV','Sang Mesmerizer with Migu','[value-11]','[value-12]')
