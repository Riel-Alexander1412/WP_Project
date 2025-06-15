-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 08:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_finder_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Email` varchar(20) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `Name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applied_jobs`
--

CREATE TABLE `applied_jobs` (
  `AppID` int(20) NOT NULL,
  `UserID` int(20) NOT NULL,
  `JobID` int(20) NOT NULL,
  `Notes` varchar(500) NOT NULL,
  `Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employer`
--

CREATE TABLE `employer` (
  `EmpID` int(20) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Password` varchar(30) NOT NULL,
  `Name` varchar(50) NOT NULL COMMENT 'Name of user or company',
  `Contact` varchar(50) NOT NULL,
  `Address` varchar(50) NOT NULL,
  `Description` varchar(3000) NOT NULL COMMENT 'Company Description',
  `Image` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employer`
--

INSERT INTO `employer` (`EmpID`, `Email`, `Password`, `Name`, `Contact`, `Address`, `Description`, `Image`) VALUES
(1, 'test@test.com', 'ergregerg', 'ergergreg', 'ergerger', 'errgerge', 'ergerg', '');

-- --------------------------------------------------------

--
-- Table structure for table `job_listing`
--

CREATE TABLE `job_listing` (
  `ListingID` int(20) NOT NULL,
  `Position` varchar(100) NOT NULL,
  `EmployerID` int(20) NOT NULL,
  `JbLV` varchar(20) NOT NULL COMMENT 'Level for Applied Position(Entry, Intermediate etc)',
  `MinLV` varchar(20) NOT NULL COMMENT 'Minimum level of education(Diploma/GCSE)',
  `CourseType` varchar(50) NOT NULL COMMENT 'Course/Major Type(Science Com, Adminstration, Accounting, Busniess etc)',
  `CType` varchar(20) NOT NULL COMMENT 'Contract Type(Part Time, Full Time, Permanent)',
  `Salary` varchar(20) NOT NULL COMMENT 'Salary Range(Exp: 1499 - 2000)',
  `Tags` varchar(100) NOT NULL COMMENT 'Tags for SEO(Search Engine Optimization)',
  `PostDate` date NOT NULL COMMENT 'Date of Job Posted on',
  `Location` varchar(100) NOT NULL COMMENT 'Location of Job Position',
  `Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_listing`
--

INSERT INTO `job_listing` (`ListingID`, `Position`, `EmployerID`, `JbLV`, `MinLV`, `CourseType`, `CType`, `Salary`, `Tags`, `PostDate`, `Location`, `Status`) VALUES
(3, 'Software Engineer', 1, 'Entry', 'Diploma', 'Computer Science', 'Full Time', '5000', 'Software', '2025-06-15', 'UMP', 'New');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(20) NOT NULL COMMENT 'Unique Identifier for each users',
  `Name` varchar(100) NOT NULL COMMENT 'Full Legal names of users',
  `Email` varchar(50) NOT NULL COMMENT 'User email for login, verification and notice',
  `Password` varchar(200) NOT NULL COMMENT 'User password. Saved in HASH format',
  `PhoneNum` varchar(18) NOT NULL COMMENT 'User phone number',
  `Address` varchar(100) NOT NULL COMMENT 'User address',
  `COO` varchar(40) NOT NULL COMMENT 'User Country of Origin(COO)',
  `DoB` date NOT NULL COMMENT 'User Date of Birth(DoB)',
  `Gender` varchar(40) NOT NULL COMMENT 'User Gender(Apache Helicopter)',
  `HiEdu` varchar(50) NOT NULL COMMENT 'Highest Level of Education for user(SPM/GCSE, Diploma, Degree etc)',
  `UniFeat` text NOT NULL COMMENT 'Unique Features for each users to tell the employer why they should hire them over others.',
  `Resume` varchar(200) NOT NULL COMMENT 'Resume File',
  `Image` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `Name`, `Email`, `Password`, `PhoneNum`, `Address`, `COO`, `DoB`, `Gender`, `HiEdu`, `UniFeat`, `Resume`, `Image`) VALUES
(1, 'test', 'test@test.com', 'test123', '0123', '123', 'Malaysia', '2025-06-12', 'Male', 'Diploma', 'qewr', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Email`);

--
-- Indexes for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  ADD PRIMARY KEY (`AppID`) USING BTREE,
  ADD UNIQUE KEY `UserID` (`UserID`),
  ADD UNIQUE KEY `JobID` (`JobID`);

--
-- Indexes for table `employer`
--
ALTER TABLE `employer`
  ADD PRIMARY KEY (`EmpID`);

--
-- Indexes for table `job_listing`
--
ALTER TABLE `job_listing`
  ADD PRIMARY KEY (`ListingID`),
  ADD UNIQUE KEY `EmployerID` (`EmployerID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  MODIFY `AppID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employer`
--
ALTER TABLE `employer`
  MODIFY `EmpID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `job_listing`
--
ALTER TABLE `job_listing`
  MODIFY `ListingID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT COMMENT 'Unique Identifier for each users', AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  ADD CONSTRAINT `applied_jobs_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applied_jobs_ibfk_2` FOREIGN KEY (`JobID`) REFERENCES `job_listing` (`ListingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_listing`
--
ALTER TABLE `job_listing`
  ADD CONSTRAINT `job_listing_ibfk_1` FOREIGN KEY (`EmployerID`) REFERENCES `employer` (`EmpID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
