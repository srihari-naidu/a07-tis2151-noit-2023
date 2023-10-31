-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2023 at 06:01 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `noit`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `Id` int(11) NOT NULL,
  `CourseId` int(11) NOT NULL,
  `StudentId` int(11) NOT NULL,
  `Comment` varchar(2055) NOT NULL,
  `CommentDate` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`Id`, `CourseId`, `StudentId`, `Comment`, `CommentDate`) VALUES
(0, 6, 1, 'I just finished the \"Excel Basics for Data Analysis\" course with Tessa Grey, and it was a great learning experience! The course content was well-structured, and Tessa\'s teaching style was excellent. However, I think the pace could be a bit slower for beginners like me. It would also be helpful to include more real-world examples. I encountered some technical issues, but overall, it was a valuable course. Looking forward to future improvements!', '2023-07-02');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `Id` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` varchar(2055) NOT NULL,
  `Thumbnail` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL,
  `Link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`Id`, `Title`, `Description`, `Thumbnail`, `Date`, `StartTime`, `EndTime`, `Link`) VALUES
(3, 'Foundations of Project Management', 'This course is the first in a series of six to equip you with the skills you need to apply to introductory-level roles in project management. Project managers play a key role in leading, planning and implementing critical projects to help their organizations succeed. In this course, you’ll discover foundational project management terminology and gain a deeper understanding of  the role and responsibilities of a project manager. We’ll also introduce you to the kinds of jobs you might pursue after completing this program. Throughout the program, you’ll learn from current Google project managers, who can provide you with a multi-dimensional educational experience that will help you build your skills  for on-the-job application. ', '64a053ebb3b84.png', '2023-07-01', '2023-07-01 16:00:00', '2023-07-01 18:00:00', 'https://meet.google.com/uuf-jdub-abu'),
(5, 'Introduction to Data Analytics', 'Ready to start a career in Data Analysis but don’t know where to begin? This course presents you with a gentle introduction to Data Analysis, the role of a Data Analyst, and the tools used in this job. You will learn about the skills and responsibilities of a data analyst and hear from several data experts sharing their tips & advice to start a career. This course will help you to differentiate between the roles of Data Analysts, Data Scientists, and Data Engineers. ', '64a055cb3a8a6.png', '2023-07-01', '2023-07-01 12:00:00', '2023-07-01 14:00:00', 'https://meet.google.com/jta-riat-asa'),
(6, 'Excel Basics for Data Analysis', 'Spreadsheet tools like Excel are an essential tool for working with data - whether for data analytics, business, marketing, or research. This course is designed to give you a basic working knowledge of Excel and how to use it for analyzing data. ', '64a0568891760.png', '2023-07-02', '2023-07-02 12:00:00', '2023-07-02 14:00:00', 'https://meet.google.com/jya-riht-czy'),
(7, 'Project Initiation: Starting a Successful Project', 'This is the second course in the Google Project Management Certificate program. This course will show you how to set a project up for success in the first phase of the project life cycle: the project initiation phase. In exploring the key components of this phase, you’ll learn how to define and manage project goals, deliverables, scope, and success criteria. You’ll discover how to use tools and templates like stakeholder analysis grids and project charters to help you set project expectations and communicate roles and responsibilities. Current Google project managers will continue to instruct and provide you with hands-on approaches for accomplishing these tasks while showing you the best project management tools and resources for the job at hand.', '64a0577e5f297.png', '2023-07-02', '2023-07-02 16:00:00', '2023-07-02 18:00:00', 'https://meet.google.com/rhc-kgvh-zov'),
(8, 'Project Planning: Putting It All Together', 'This is the third course in the Google Project Management Certificate program. This course will explore how to map out a project in the second phase of the project life cycle: the project planning phase. You will examine the key components of a project plan, how to make accurate time estimates, and how to set milestones. Next, you will learn how to build and manage a budget and how the procurement processes work. Then, you will discover tools that can help you identify and manage different types of risk and how to use a risk management plan to communicate and resolve risks. Finally, you will explore how to draft and manage a communication plan and how to organize project documentation. Current Google project managers will continue to instruct and provide you with hands-on approaches for accomplishing these tasks while showing you the best project management tools and resources for the job at hand.', '64a057ce7e85f.png', '2023-07-03', '2023-07-03 16:00:00', '2023-07-03 18:00:00', 'https://meet.google.com/rhc-kgvh-zov'),
(9, 'Data Visualization and Dashboards with Excel and Cognos', 'Learn how to create data visualizations and dashboards using spreadsheets and analytics tools. This course covers some of the first steps for telling a compelling story with your data using various types of charts and graphs. You\'ll learn the basics of visualizing data with Excel and IBM Cognos Analytics without having to write any code. ', '64a05993b59df.png', '2023-07-03', '2023-07-03 12:00:00', '2023-07-03 14:00:00', 'https://meet.google.com/ayy-finr-fco'),
(10, 'Project Execution: Running the Project', 'This is the fourth course in the Google Project Management Certificate program. This course will delve into the execution and closing phases of the project life cycle. You will learn what aspects of a project to track and how to track them. You will also learn how to effectively manage and communicate changes, dependencies, and risks. As you explore quality management, you will learn how to measure customer satisfaction and implement continuous improvement and process improvement techniques. ', '64a05b36cae1c.png', '2023-08-01', '2023-08-01 16:00:00', '2023-08-01 18:00:00', 'https://meet.google.com/smk-abkv-wpo'),
(11, 'Agile Project Management', 'This is the fifth course in the Google Project Management Certificate program. This course will explore the history, approach, and philosophy of Agile project management, including the Scrum framework. You will learn how to differentiate and blend Agile and other project management approaches. As you progress through the course, you will learn more about Scrum, exploring its pillars and values and comparing essential Scrum team roles. ', '64a05b5fe9638.png', '2023-08-02', '2023-08-02 16:00:00', '2023-08-02 18:00:00', 'https://meet.google.com/smk-abkv-wpo'),
(12, 'Capstone: Applying Project Management in the Real World', 'In this final, capstone course of the Google Project Management Certificate, you will practice applying the project management knowledge and skills you have learned so far. We encourage learners to complete Courses 1-5 before beginning the final course, as they provide the foundation necessary to complete the activities in this course. ', '64a05b83a82e6.png', '2023-08-03', '2023-08-03 16:00:00', '2023-08-03 18:00:00', 'https://meet.google.com/smk-abkv-wpo'),
(13, 'Python for Data Science, AI & Development', 'Kickstart your learning of Python with this beginner-friendly self-paced course taught by an expert. Python is one of the most popular languages in the programming and data science world and demand for individuals who have the ability to apply Python has never been higher.  ', '64a05d6e5e4fa.png', '2023-08-01', '2023-08-01 16:00:00', '2023-08-01 18:00:00', 'https://meet.google.com/vym-ifwp-ttx'),
(14, 'Python Project for Data Science', 'Python Project for Data Science', '64a05db1861eb.png', '2023-08-02', '2023-08-02 16:00:00', '2023-08-02 18:00:00', 'https://meet.google.com/saa-xzbv-xdg');

-- --------------------------------------------------------

--
-- Table structure for table `courseinstructor`
--

CREATE TABLE `courseinstructor` (
  `CourseId` int(11) NOT NULL,
  `InstructorId` int(11) NOT NULL,
  `Availability` enum('Available','Unavailable','') NOT NULL,
  `Status` enum('Invited','Responded') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courseinstructor`
--

INSERT INTO `courseinstructor` (`CourseId`, `InstructorId`, `Availability`, `Status`) VALUES
(3, 5, 'Available', 'Responded'),
(7, 2, 'Available', 'Responded'),
(7, 5, 'Available', 'Responded'),
(8, 2, 'Available', 'Responded'),
(8, 5, 'Unavailable', 'Responded'),
(6, 7, 'Available', 'Responded'),
(9, 6, 'Available', 'Responded'),
(9, 7, 'Unavailable', 'Responded'),
(10, 2, 'Available', 'Responded'),
(10, 5, 'Available', 'Responded'),
(11, 2, 'Available', 'Responded'),
(11, 5, 'Available', 'Responded'),
(12, 2, 'Available', 'Responded'),
(12, 5, 'Available', 'Responded'),
(5, 6, 'Available', 'Responded'),
(13, 6, 'Available', 'Responded'),
(14, 6, 'Available', 'Responded');

-- --------------------------------------------------------

--
-- Table structure for table `courseprovider`
--

CREATE TABLE `courseprovider` (
  `CourseId` int(11) NOT NULL,
  `ProviderId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courseprovider`
--

INSERT INTO `courseprovider` (`CourseId`, `ProviderId`) VALUES
(3, 3),
(5, 8),
(6, 8),
(7, 3),
(8, 3),
(9, 8),
(10, 3),
(11, 3),
(12, 3),
(13, 8),
(14, 8);

-- --------------------------------------------------------

--
-- Table structure for table `instructor`
--

CREATE TABLE `instructor` (
  `InstructorId` int(11) NOT NULL,
  `Company` varchar(64) NOT NULL,
  `Profession` varchar(64) NOT NULL,
  `DateHired` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor`
--

INSERT INTO `instructor` (`InstructorId`, `Company`, `Profession`, `DateHired`) VALUES
(2, 'Google', 'Developer', '2023-06-30'),
(5, 'Google', 'Project Manager', '2023-06-30'),
(6, 'IBM', 'Data Scientist', '2023-06-30'),
(7, 'IBM', 'Business Analyst', '2023-06-30');

-- --------------------------------------------------------

--
-- Table structure for table `provider`
--

CREATE TABLE `provider` (
  `ProviderId` int(11) NOT NULL,
  `Company` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provider`
--

INSERT INTO `provider` (`ProviderId`, `Company`) VALUES
(3, 'Google'),
(8, 'IBM');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `StudentId` int(11) NOT NULL,
  `DateEnrolled` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentId`, `DateEnrolled`) VALUES
(1, '2023-06-30'),
(4, '2023-06-30'),
(9, '2023-06-30');

-- --------------------------------------------------------

--
-- Table structure for table `studentcourse`
--

CREATE TABLE `studentcourse` (
  `StudentId` int(11) NOT NULL,
  `CourseId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentcourse`
--

INSERT INTO `studentcourse` (`StudentId`, `CourseId`) VALUES
(1, 6),
(1, 9),
(1, 13),
(1, 11),
(4, 6),
(4, 9),
(4, 13),
(9, 9),
(9, 8);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `Id` int(11) NOT NULL,
  `Role` enum('Provider','Instructor','Student') NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `ProfilePicture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Id`, `Role`, `Name`, `Email`, `Password`, `ProfilePicture`) VALUES
(1, 'Student', 'Tyler Reed', 'tyler@snoit.com', 'tyler', '649ee22b5b7fc.jpg'),
(2, 'Instructor', 'George Williams', 'george@inoit.com', 'george', '649ee411d43d0.jpg'),
(3, 'Provider', 'Sundar Pichai', 'google@pnoit.com', 'google', '649ee49c6d7fa.png'),
(4, 'Student', 'Rachel Naidu', 'rachel@snoit.com', 'rachel', '649ee7e5b2b3f.jpg'),
(5, 'Instructor', 'Anne Williams', 'anne@inoit.com', 'anne', '649eed35011d9.jpg'),
(6, 'Instructor', 'William Herondale', 'william@inoit.com', 'william', '649eedcf16ef3.jpg'),
(7, 'Instructor', 'Tessa Grey', 'tessa@inoit.com', 'tessa', '649eee2b206f7.jpg'),
(8, 'Provider', 'Arvind Krishna', 'ibm@pnoit.com', 'ibm', '649eee55dfb8b.jpg'),
(9, 'Student', 'Reine Young', 'reine@snoit.com', 'reine', '649eeead441ad.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD KEY `comment_ibfk_1` (`CourseId`),
  ADD KEY `comment_ibfk_2` (`StudentId`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `courseinstructor`
--
ALTER TABLE `courseinstructor`
  ADD KEY `courseinstructor_ibfk_1` (`CourseId`),
  ADD KEY `InstructorId` (`InstructorId`);

--
-- Indexes for table `courseprovider`
--
ALTER TABLE `courseprovider`
  ADD KEY `courseprovider_ibfk_1` (`CourseId`),
  ADD KEY `courseprovider_ibfk_2` (`ProviderId`);

--
-- Indexes for table `instructor`
--
ALTER TABLE `instructor`
  ADD KEY `instructor_ibfk_1` (`InstructorId`);

--
-- Indexes for table `provider`
--
ALTER TABLE `provider`
  ADD KEY `provider_ibfk_1` (`ProviderId`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD KEY `student_ibfk_1` (`StudentId`);

--
-- Indexes for table `studentcourse`
--
ALTER TABLE `studentcourse`
  ADD KEY `studentcourse_ibfk_1` (`CourseId`),
  ADD KEY `studentcourse_ibfk_2` (`StudentId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`CourseId`) REFERENCES `course` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`StudentId`) REFERENCES `student` (`StudentId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courseinstructor`
--
ALTER TABLE `courseinstructor`
  ADD CONSTRAINT `courseinstructor_ibfk_1` FOREIGN KEY (`CourseId`) REFERENCES `course` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `courseinstructor_ibfk_2` FOREIGN KEY (`InstructorId`) REFERENCES `instructor` (`InstructorId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courseprovider`
--
ALTER TABLE `courseprovider`
  ADD CONSTRAINT `courseprovider_ibfk_1` FOREIGN KEY (`CourseId`) REFERENCES `course` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `courseprovider_ibfk_2` FOREIGN KEY (`ProviderId`) REFERENCES `provider` (`ProviderId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `instructor`
--
ALTER TABLE `instructor`
  ADD CONSTRAINT `instructor_ibfk_1` FOREIGN KEY (`InstructorId`) REFERENCES `user` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `provider`
--
ALTER TABLE `provider`
  ADD CONSTRAINT `provider_ibfk_1` FOREIGN KEY (`ProviderId`) REFERENCES `user` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`StudentId`) REFERENCES `user` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studentcourse`
--
ALTER TABLE `studentcourse`
  ADD CONSTRAINT `studentcourse_ibfk_1` FOREIGN KEY (`CourseId`) REFERENCES `course` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentcourse_ibfk_2` FOREIGN KEY (`StudentId`) REFERENCES `student` (`StudentId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
