<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");
    
    if (isset($_SESSION['userId'])) 
    {
        $userId = $_SESSION['userId'];

        $user = $con->prepare(
            "SELECT * FROM User WHERE Id = ?"
        );
        $user->execute([$userId]);
        $user = $user->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $userId = '';
    }

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
            "SELECT * FROM Course 
             JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
             WHERE ProviderId = ?"
        );
        $courses->execute([$providerId]);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - Courses</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <?php 
        if (isset($_SESSION['warning_course_deleted']))
        {
            $message = $_SESSION['warning_course_deleted'];
            unset($_SESSION['warning_course_deleted']);
            echo "<div class=\"container\">";
                warning($message, "Ok", "courses.php");
            echo "</div>";
        }
        else
        {
    ?>
            <main>
                <div class="card-main">
                    <?php
                        if ($userId != '')
                        {
                            if (isset($_SESSION['studentId']))
                            {
                            ?>

                            <?php
                                $unreviewedCourses = $con->prepare(
                                    "SELECT * FROM Course 
                                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
                                     JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                                     WHERE StudentCourse.StudentId = ?
                                     AND NOW() > Course.EndTime
                                     AND StudentCourse.CourseId NOT IN (
                                         SELECT CourseId 
                                         FROM Comment 
                                         WHERE Comment.StudentId = ?
                                    )"
                                );
                                $unreviewedCourses->execute([$studentId, $studentId]);

                                if ($unreviewedCourses->rowCount() > 0)
                                {
                            ?>
                                    <h1 class="title" id="unreviewedCourses">Leave a Feedback</h1>
                                    <p><i>Leave a feedback on these courses and add a certificate to your collection!</i></p>
                                    <?php
                                        if ($unreviewedCourses->rowCount() > 0) 
                                        {
                                            echo "<div class=\"short no-gap\">";
                                            while ($course = $unreviewedCourses->fetch(PDO::FETCH_ASSOC)) 
                                            {
                                                $courseProvider = $con->prepare(
                                                    "SELECT * FROM Provider 
                                                    JOIN User ON ProviderId = User.Id 
                                                    WHERE ProviderId = ?"
                                                );
                                                $courseProvider->execute([$course['ProviderId']]);
                                                $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                                course($course, $provider);
                                            }
                                            echo "</div>";
                                        } 
                                        else {
                                            sorry("We couldn't find any of your unreviewed courses yet.", $gap=false);
                                        }
                                    ?>
                                <?php
                                    }
                                ?>
            

                                <h1 class="title" id="yourCourses">Courses</h1>
                                <p><i>Courses you've enrolled in.</i></p>
                                <?php
                                    $enrolledCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                                         JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                                         WHERE StudentCourse.StudentId = ?"
                                    );
                                    $enrolledCourses->execute([$studentId]);

                                    if ($enrolledCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $enrolledCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else {
                                        sorry("We couldn't find any of your enrolled courses yet. Start enrolling now!", $gap=true);
                                        button("Enroll Now!","courses.php#allCourses", "more");
                                    }
                                ?>

                                <h1 class="title" id="happeningCourses">Happening Courses</h1>
                                <p><i>Courses you've enrolled in that are happening now.</i></p>
                                <?php
                                    $happeningCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                                         JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                                         WHERE StudentCourse.StudentId = ?
                                         AND DATE(NOW()) = DATE(Course.Date)
                                         AND NOW() > Course.StartTime AND NOW() < Course.EndTime
                                         ORDER BY EndTime"
                                    );
                                    $happeningCourses->execute([$studentId]);

                                    if ($happeningCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $happeningCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else {
                                        sorry("We couldn't find of your happening courses yet.", $gap=false);
                                    }
                                ?>

                                <h1 class="title" id="upcomingCourses">Upcoming Courses</h1>
                                <p><i>Courses you've enrolled in that are upcoming.</i></p>
                                <?php
                                    $upcomingCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                                         JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                                         WHERE StudentCourse.StudentId = ?
                                         AND NOW() < Course.StartTime
                                         ORDER BY StartTime"
                                    );
                                    $upcomingCourses->execute([$studentId]);

                                    if ($upcomingCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $upcomingCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else {
                                        sorry("We couldn't find any of your upcoming courses yet.", $gap=false);
                                    }
                                ?>

                                <h1 class="title" id="pastCourses">Past Courses</h1>
                                <p><i>Courses you've enrolled in that have ended.</i></p>
                                <?php
                                    $pastCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
                                         JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                                         WHERE StudentCourse.StudentId = ?
                                         AND NOW() > Course.EndTime
                                         ORDER BY EndTime DESC"
                                    );
                                    $pastCourses->execute([$studentId]);
                                    if ($pastCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short no-gap\">";
                                        while ($course = $pastCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else {
                                        sorry("We couldn't find any of your past courses yet.", $gap=false);
                                    }
                                ?>
                            <?php
                            }

                            
                            else if (isset($_SESSION['providerId']))
                            {
                                ?>
                                <h1 class="title" id="yourCourses">Courses</h1>
                                <p><i>Courses you're providing.</i></p>
                                <?php
                                    $providedCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
                                         WHERE ProviderId = ?"
                                    );
                                    $providedCourses->execute([$providerId]);

                                    if ($providedCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $providedCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo " 
                                                <div class=\"card center three\">
                                                    <a href=\"course_add.php\"><button class=\"button button-add\">&#x2b;</button></a>
                                                </div>
                                        ";
                                        echo "</div>";
                                    } 
                                    else {
                                        sorry("We couldn't find any of your courses yet. Start adding now!", $gap=true);
                                        button("Add Now!", "course_add.php", "more");
                                    }
                                ?>

                                <h1 class="title" id="happeningCourses">Happening Courses</h1>
                                <p><i>Courses you're providing that are happening now.</i></p>
                                <?php
                                    $happeningCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
                                         WHERE CourseProvider.ProviderId = ?
                                         AND DATE(NOW()) = DATE(Course.Date)
                                         AND NOW() > Course.StartTime AND NOW() < Course.EndTime
                                         ORDER BY StartTime
                                         LIMIT 3"
                                    );
                                    $happeningCourses->execute([$providerId]);

                                    if ($happeningCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $happeningCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else {
                                        sorry("We couldn't find any of your happening courses yet.", $gap=false);
                                    }
                                ?>
                                <h1 class="title" id="upcomingCourses" >Upcoming Courses</h1>
                                <p><i>Courses you're providing that are upcoming.</i></p>
                                <?php
                                    $upcomingCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
                                         WHERE CourseProvider.ProviderId = ?
                                         AND NOW() < Course.StartTime
                                         ORDER BY StartTime"
                                    );
                                    $upcomingCourses->execute([$providerId]);

                                    if ($upcomingCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $upcomingCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else 
                                    {
                                        sorry("We couldn't find any upcoming courses yet.", $gap=false);
                                    }
                                ?>

                                <h1 class="title" id="pastCourses">Past Courses</h1>
                                <p><i>Courses you're providing that have ended.</i></p>
                                <?php
                                    $pastCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
                                         WHERE CourseProvider.ProviderId = ?
                                         AND NOW() > Course.EndTime
                                         ORDER BY EndTime DESC"
                                    );
                                    $pastCourses->execute([$providerId]);
                                    if ($pastCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $pastCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else 
                                    {
                                        sorry("We couldn't find any past courses yet.", $gap=false);
                                    }
                                ?>
                            <?php
                            }


                            else if (isset($_SESSION['instructorId']))
                            {
                                ?>
                                <h1 class="title" id="yourCourses">Courses</h1>
                                <p><i>Courses you're teaching.</i></p>
                                <?php
                                    $teachingCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                                         JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId
                                         WHERE CourseInstructor.InstructorId = ?
                                         AND Availability = ?"
                                    );
                                    $teachingCourses->execute([$instructorId, 'Available']);

                                    if ($teachingCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $teachingCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else {
                                        sorry("We couldn't find any courses yet.", $gap=false);;
                                    }
                                ?>


                                <h1 class="title" id="upcomingCourses" >Upcoming Courses</h1>
                                <p><i>Courses you're teaching soon.</i></p>
                                <?php
                                    $upcomingCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                                         JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId 
                                         WHERE CourseInstructor.InstructorId = ?
                                         AND Availability = ?
                                         AND NOW() < Course.StartTime
                                         ORDER BY StartTime"
                                    );
                                    $upcomingCourses->execute([$instructorId, 'Available']);

                                    if ($upcomingCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $upcomingCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else 
                                    {
                                        sorry("We couldn't find any upcoming courses yet.", $gap=false);
                                    }
                                ?>

                                <h1 class="title" id="pastCourses">Past Courses</h1>
                                <p><i>Courses you've thought before.</i></p>
                                <?php
                                    $pastCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                                         JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId 
                                         WHERE  CourseInstructor.InstructorId = ?
                                         AND Availability = ?
                                         AND NOW() > Course.EndTime
                                         ORDER BY EndTime DESC"
                                    );
                                    $pastCourses->execute([$instructorId, 'Available']);
                                    if ($pastCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $pastCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else 
                                    {
                                        sorry("We couldn't find any past courses yet.", $gap=false);
                                    }
                                ?>

                                
                                <h1 class="title" id="invitedCourses" >Invited Courses</h1>
                                <p><i>Courses you're invited to teach.</i></p>
                                <?php
                                    $invitedCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                                         JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId
                                         WHERE CourseInstructor.InstructorId = ?
                                         AND Status = ?
                                         AND NOW() < Course.StartTime
                                         ORDER BY StartTime"
                                    );
                                    $invitedCourses->execute([$instructorId, 'Invited']);

                                    if ($invitedCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $invitedCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else 
                                    {
                                        sorry("We couldn't find any invites yet.", $gap=false);;
                                    }
                                ?>


                                <h1 class="title" id="unavailableCourses" >Unavailable Courses</h1>
                                <p><i>Courses you've responded that you were unavailable to teach.</i></p>
                                <?php
                                    $unavailableCourses = $con->prepare(
                                        "SELECT * FROM Course 
                                         JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                                         JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId
                                         WHERE CourseInstructor.InstructorId = ?
                                         AND Availability = ?
                                         AND Status = ?
                                         AND NOW() < Course.StartTime
                                         ORDER BY StartTime"
                                    );
                                    $unavailableCourses->execute([$instructorId, 'Unavailable', 'Responded']);

                                    if ($unavailableCourses->rowCount() > 0) 
                                    {
                                        echo "<div class=\"short\">";
                                        while ($course = $unavailableCourses->fetch(PDO::FETCH_ASSOC)) 
                                        {
                                            $courseProvider = $con->prepare(
                                                "SELECT * FROM Provider 
                                                 JOIN User ON ProviderId = User.Id 
                                                 WHERE ProviderId = ?"
                                            );
                                            $courseProvider->execute([$course['ProviderId']]);
                                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                                            course($course, $provider);
                                        }
                                        echo "</div>";
                                    } 
                                    else 
                                    {
                                        sorry("We couldn't find any invites that you're unavailable for.", $gap=false);;
                                    }
                                ?>

                            <?php
                            }
                        }
                        if ($userId == '' || isset($_SESSION['studentId']))
                        {
                            echo "<h1 class=\"title\" id=\"allCourses\"> All Courses</h1>";

                            $allCourses = $con->prepare(
                                "SELECT * FROM Course 
                                 JOIN CourseProvider ON Course.Id = CourseProvider.CourseId"
                            );
                            $allCourses->execute();

                            if ($allCourses->rowCount() > 0) 
                            {
                                echo "<div class=\"short\">";
                                while ($course = $allCourses->fetch(PDO::FETCH_ASSOC)) 
                                {
                                    $courseProvider = $con->prepare(
                                        "SELECT * FROM Provider 
                                         JOIN User ON ProviderId = User.Id 
                                         WHERE ProviderId = ?"
                                    );
                                    $courseProvider->execute([$course['ProviderId']]);
                                    $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);
        
                                    course($course, $provider);
                                }
                            }
                            else 
                            {
                                sorry("We couldn't find any courses yet.", $gap=false);
                            }
                        }
                    ?>
                </div>
            </main>
    <?php
        }
    ?>

<?php include("inc/footer.inc.php"); ?>    
<script src="js/script.js"></script>
</body>
</html>