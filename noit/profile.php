<?php
    session_start();
    include("inc/connection.inc.php");

    if (isset($_SESSION['studentId'])) 
    {
        $studentId = $_SESSION['studentId'];
        $student = $con->prepare(
            "SELECT * FROM Student 
             JOIN User ON StudentId = User.Id 
             WHERE StudentId = ?"
        );
        $student->execute([$studentId]);
        $student = $student->fetch(PDO::FETCH_ASSOC);

        $comments = $con->prepare(
            "SELECT * FROM Comment 
             WHERE StudentId = ?"
        );
        $comments->execute([$studentId]);
        $commentCount = $comments->rowCount();
        
        $courses = $con->prepare(
            "SELECT * FROM StudentCourse 
             WHERE StudentId = ?"
        );
        $courses->execute([$studentId]);
        $courseCount = $courses->rowCount();
        
        $certificates = $con->prepare(
            "SELECT * FROM StudentCourse 
             JOIN Course ON StudentCourse.CourseId = Course.Id
             JOIN Comment ON StudentCourse.CourseId = Comment.CourseId
             WHERE StudentCourse.StudentId = ?
             AND NOW() > Course.EndTime
             AND StudentCourse.CourseId IN(
                SELECT CourseId 
                FROM Comment 
                WHERE Comment.StudentId = ?
            )"
        );
        $certificates->execute([$studentId, $studentId]);
        $certificateCount = $certificates->rowCount();
    } 

    else if (isset($_SESSION['providerId'])) 
    {
        $providerId = $_SESSION['providerId'];

        $provider = $con->prepare(
            "SELECT * FROM Provider 
             JOIN User ON ProviderId = User.Id 
             WHERE ProviderId = ?"
        );
        $provider->execute([$providerId]);
        $provider = $provider->fetch(PDO::FETCH_ASSOC);

        $courses = $con->prepare(
            "SELECT * FROM CourseProvider 
             WHERE ProviderId = ?"
        );
        $courses->execute([$providerId]);
        $courseCount = $courses->rowCount();

        $comments = $con->prepare(
            "SELECT * FROM Comment 
             JOIN Course ON Comment.CourseId = Course.Id
             JOIN CourseProvider ON CourseProvider.CourseId = Course.Id 
             WHERE CourseProvider.ProviderId = ?"
        );
        $comments->execute([$providerId]);
        $commentCount = $comments->rowCount();
    }

    else if (isset($_SESSION['instructorId'])) 
    {
        $instructorId = $_SESSION['instructorId'];
        $instructor = $con->prepare(
            "SELECT * FROM Instructor 
             JOIN User ON InstructorId = User.Id 
             WHERE InstructorId = ?"
        );
        $instructor->execute([$instructorId]);
        $instructor = $instructor->fetch(PDO::FETCH_ASSOC);

        $courses = $con->prepare(
            "SELECT * FROM CourseInstructor 
             WHERE InstructorId = ?"
        );
        $courses->execute([$instructorId]);
        $courseCount = $courses->rowCount();
    }
    
    else {
        header('Location: login.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - Profile</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <main>
        <?php
            if ($user['Role'] == 'Student')        
            {
        ?>
                <div class="card-main">
                    <h1 class="title">Profile</h1>
                    <div class="long no-gap">
                        <div class="card profile">
                            <img src="uploads/<?= $student['ProfilePicture']; ?>" alt="" class="profile-pic">
                            <h1><?= $student['Name']; ?></h1>
                            <p><?= $student['Role']; ?></p>
                        </div>
                    </div>
                    <div class="short">
                        <div class="card center three">
                            <a href="#">
                                <h1><?= $commentCount ?></h1> 
                            </a>
                            Comments
                        </div>
                        <div class="card center three">
                            <a href="courses.php#yourCourses">
                                <h1><?= $courseCount ?></h1> 
                            </a>
                            Courses
                        </div>
                        <div class="card center three">
                            <a href="#">
                                <h1><?= $certificateCount ?></h1> 
                            </a> 
                            Certificates
                        </div>
                    </div>
                </div>
        <?php
            }
            else if ($user['Role'] == 'Instructor')        
            {
        ?>
                <div class="card-main">
                    <h1 class="title">Profile</h1>
                    <div class="long no-gap">
                        <div class="card profile">
                            <img src="uploads/<?= $instructor['ProfilePicture']; ?>" alt="" class="profile-pic">
                            <h1><?= $instructor['Name']; ?></h1>
                            <p><?= $instructor['Profession']; ?></p>
                        </div>
                    </div>
                    <div class="short">
                        <div class="card center">
                            <a href="courses.php#yourCourses">
                                <h1><?= $courseCount ?></h1> 
                            </a>
                            Courses
                        </div>
                    </div>
                </div>
        <?php
            }
            else if ($user['Role'] == 'Provider')        
            {
        ?>
                <div class="card-main">
                    <h1 class="title">Profile</h1>
                    <div class="long no-gap">
                        <div class="card profile">
                            <img src="uploads/<?= $provider['ProfilePicture']; ?>" alt="" class="profile-pic">
                            <h1><?= $provider['Company']; ?></h1>
                            <p><?= $provider['Role']; ?></p>
                        </div>
                    </div>
                    <div class="short">
                        <div class="card center">
                            <a href="courses.php#yourCourses">
                                <h1><?= $courseCount ?></h1> 
                            </a>
                            Courses
                        </div>
                    </div>
                </div>
        <?php
            }
        ?>
    </main>

    <?php include("inc/footer.inc.php") ?>    
    <script src="js/script.js"></script>
</body>
</html>