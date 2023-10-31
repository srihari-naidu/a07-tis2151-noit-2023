<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");
    
    if (isset($_GET['id']))
    {
        $getId = $_GET['id'];
        $courseId = $getId;

        $course = $con->prepare(
            "SELECT * FROM Course
             JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
             WHERE Id = ?
             LIMIT 1"
        );
        $course->execute([$courseId]);
    }
    else
    {
        header('Location: login.php');
    }

    if (isset($_SESSION['studentId'])) 
    {
        $studentId = $_SESSION['studentId'];
        $student = $con->prepare(
            "SELECT * 
             FROM Student 
             JOIN User ON StudentId = User.Id 
             WHERE StudentId = ?"
        );
        $student->execute([$studentId]);
        $student = $student->fetch(PDO::FETCH_ASSOC);

        $studentCourse = $con->prepare(
            "SELECT * FROM StudentCourse
             JOIN Course ON StudentCourse.CourseId = Course.Id
             WHERE StudentId = ?
             AND CourseId = ?"
        );
        $studentCourse->execute([$studentId, $courseId]);

        $courseEnded = $con->prepare(
            "SELECT * FROM Course
             WHERE Id = ?
             AND NOW() > Course.EndTime"
        );
        $courseEnded->execute([$courseId]);

        $courseHappening = $con->prepare(
            "SELECT * FROM Course
             WHERE Id = ?
             AND NOW() > Course.StartTime
             AND NOW() < Course.EndTime"
        );
        $courseHappening->execute([$courseId]);

        if (isset($_POST['enroll-course'])) 
        {
            $courseId = $_POST['courseId'];
            $course = $con->prepare(
                "SELECT * FROM Course
                 WHERE Course.Id = ?"
            );
            $course->execute([$courseId]);
            $course = $course->fetch(PDO::FETCH_ASSOC);
    
            $studentCourse = $con->prepare(
                "SELECT * FROM StudentCourse
                 JOIN Course ON StudentCourse.CourseId = Course.Id
                 WHERE StudentId = ?
                 AND CourseId = ?"
            );
            $studentCourse->execute([$studentId, $courseId]);
    
            if ($studentCourse->rowCount() > 0) 
            {
                $remStudentCourse = $con->prepare(
                    "DELETE FROM StudentCourse
                     WHERE StudentId = ?
                     AND CourseId = ?"
                );
                $remStudentCourse->execute([$studentId, $courseId]);
                $_SESSION['warning_course_unenrolled'] = "You have unenrolled from <b>{$course['Title']}</b>.";
            }
            else {
                $insStudentCourse = $con->prepare(
                    "INSERT INTO StudentCourse(StudentId, CourseId)
                     VALUES (?, ?)"
                );
                $insStudentCourse->execute([$studentId, $courseId]);
                $_SESSION['success_course_enrolled'] = "You have enrolled to <b>{$course['Title']}</b>.";
            }
        }
        if (isset($_POST['post-feedback']))
        {
            $courseId = $_POST['courseId'];
            $feedback = $_POST['feedback'];

            $insFeedback = $con->prepare(
                "INSERT INTO Comment (CourseId, StudentId, Comment)
                 VALUES (?, ?, ?)"
            );
            $insFeedback->execute([$courseId, $studentId, $feedback]);

            $commentId = $con->lastInsertId();     
            $verComment = $con->prepare(
                "SELECT * FROM Comment
                 WHERE Id = ?"
            );
            $verComment->execute([$commentId]);
            if ($verComment->rowCount() > 0) 
            {
                $_SESSION['success_feedback_added'] = "Thank you! Your feedback has been added.";
            }
        } 

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

        if (isset($_POST['delete-course']))
        {
            $courseId = $_POST['courseId'];
            $verCourse = $con->prepare(
                "SELECT * FROM Course
                 JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                 WHERE Course.Id = ?
                 AND CourseProvider.ProviderId = ?
                 LIMIT 1"
            );
            $verCourse->execute([$courseId, $providerId]);
         
            if ($verCourse->rowCount() > 0)
            {
                $course = $verCourse->fetch(PDO::FETCH_ASSOC);
                unlink('uploads/'.$course['Thumbnail']);

                $delCourse = $con->prepare(
                    "DELETE FROM Course 
                     WHERE Id = ?"
                );
                $delCourse->execute([$courseId]);
                $_SESSION['warning_course_deleted'] = "Course <b>{$course['Title']}</b> has been deleted.";
                header('Location: courses.php');
            }
        }
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

        $instructorCourse = $con->prepare(
            "SELECT * FROM CourseInstructor
             JOIN Course ON CourseInstructor.CourseId = Course.Id
             WHERE InstructorId = ?
             AND CourseId = ?
             AND NOW() < Course.StartTime"
        );
        $instructorCourse->execute([$instructorId, $courseId]);
     
        if (isset($_POST['accept-invite'])) 
        {
            $courseId = $_POST['courseId'];
            $course = $con->prepare(
                "SELECT * FROM Course
                 WHERE Course.Id = ?"
            );
            $course->execute([$courseId]);
            $course = $course->fetch(PDO::FETCH_ASSOC);
    
            $acceptInstructorCourse = $con->prepare(
                "UPDATE CourseInstructor
                 SET Availability = ?, 
                     Status = ?
                 WHERE InstructorId = ?
                 AND CourseId = ?"
            );
            $acceptInstructorCourse->execute(['Available', 'Responded', $instructorId, $courseId]);
            $_SESSION['success_invite_accepted'] = "You have accepted the invite to teach <b>{$course['Title']}</b>.";
        }
     
        if (isset($_POST['decline-invite'])) 
        {
            $courseId = $_POST['courseId'];
            $course = $con->prepare(
                "SELECT * FROM Course
                 WHERE Course.Id = ?"
            );
            $course->execute([$courseId]);
            $course = $course->fetch(PDO::FETCH_ASSOC);
    
            $declineInstructorCourse = $con->prepare(
                "UPDATE CourseInstructor
                 SET Availability = ?,
                     Status = ?
                 WHERE InstructorId = ?
                 AND CourseId = ?"
            );
            $declineInstructorCourse->execute(['Unavailable', 'Responded', $instructorId , $courseId]);
            $_SESSION['warning_invite_declined'] = "You withdrew from teaching <b>{$course['Title']}</b>.";
        }

        if (isset($_POST['respond-invite'])) 
        {
            $courseId = $_POST['courseId'];
            $course = $con->prepare(
                "SELECT * FROM Course
                 WHERE Course.Id = ?"
            );
            $course->execute([$courseId]);
            $course = $course->fetch(PDO::FETCH_ASSOC);
    
            $instructorCourse = $con->prepare(
                "SELECT * FROM CourseInstructor
                 JOIN Course ON CourseInstructor.CourseId = Course.Id
                 WHERE InstructorId = ?
                 AND CourseId = ?
                 AND Availability = ?"
            );
            $instructorCourse->execute([$instructorId, $courseId, 'Available']);
    
            if ($instructorCourse->rowCount() > 0) 
            {
                $declineInstructorCourse = $con->prepare(
                    "UPDATE CourseInstructor
                     SET Availability = ?,
                         Status = ?
                     WHERE InstructorId = ?
                     AND CourseId = ?"
                );
                $declineInstructorCourse->execute(['Unavailable', 'Responded', $instructorId , $courseId]);
                $_SESSION['warning_invite_declined'] = "You withdrew from teaching <b>{$course['Title']}</b>.";
            }
            else {
                $acceptInstructorCourse = $con->prepare(
                    "UPDATE CourseInstructor
                     SET Availability = ?, 
                         Status = ?
                     WHERE InstructorId = ?
                     AND CourseId = ?"
                );
                $acceptInstructorCourse->execute(['Available', 'Responded', $instructorId, $courseId]);
                $_SESSION['success_invite_accepted'] = "You have accepted the invite to teach <b>{$course['Title']}</b>.";
            }
        }
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
    <title>NOIT - Course</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <?php 
        if (isset($_SESSION['success_course_updated']))
        {
            $message = $_SESSION['success_course_updated'];
            unset($_SESSION['success_course_updated']);
            echo "<div class=\"container\">";
                success($message, "View", "");
            echo "</div>";
        }
        else if (isset($_SESSION['warning_no_instructors']))
        {
            $message = $_SESSION['warning_no_instructors'];
            unset($_SESSION['warning_no_instructors']);
            echo "<div class=\"container\">";
                warning($message, "Ok", "");
            echo "</div>";
        }
        else if (isset($_SESSION['success_course_enrolled']))
        {
            $message = $_SESSION['success_course_enrolled'];
            unset($_SESSION['success_course_enrolled']);
            echo "<div class=\"container\">";
                success($message, "Ok", "");
            echo "</div>";
        }
        else if (isset($_SESSION['warning_course_unenrolled']))
        {
            $message = $_SESSION['warning_course_unenrolled'];
            unset($_SESSION['warning_course_unenrolled']);
            echo "<div class=\"container\">";
                warning($message, "Ok", "");
            echo "</div>";
        }
        else if (isset($_SESSION['success_feedback_added']))
        {
            $message = $_SESSION['success_feedback_added'];
            unset($_SESSION['success_feedback_added']);
            echo "<div class=\"container\">";
                success($message, "Ok", "");
            echo "</div>";
        }
        else if (isset($_SESSION['success_invite_accepted']))
        {
            $message = $_SESSION['success_invite_accepted'];
            unset($_SESSION['success_invite_accepted']);
            echo "<div class=\"container\">";
                success($message, "Ok", "");
            echo "</div>";
        }
        else if (isset($_SESSION['warning_invite_declined']))
        {
            $message = $_SESSION['warning_invite_declined'];
            unset($_SESSION['warning_invite_declined']);
            echo "<div class=\"container\">";
                warning($message, "Ok", "");
            echo "</div>";
        }
        else
        {
    ?>
            <main>
                <div class="card-main">
                    <h1 class="title">Course</h1>
                    <?php
                        if ($course->rowCount() > 0) 
                        {
                            $course = $course->fetch(PDO::FETCH_ASSOC);

                            $courseProvider = $con->prepare(
                                "SELECT * FROM Provider 
                                 JOIN User ON ProviderId = User.Id 
                                 WHERE ProviderId = ?"
                            );
                            $courseProvider->execute([$course['ProviderId']]);
                            $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);
                    ?>
                            <div class="short">
                                <div class="card course-main">
                                    <img src="uploads/<?= $course['Thumbnail']; ?>" alt="" class="thumbnail">
                                    <div>
                                        <h1 class="title"><?= $course['Title']; ?></h1>
                                        <p><?= $course['Description'] ?></p>
                                    </div>
                                    <div class="details">
                                        <h3>Date</h3>
                                        <p><b><?= date("d-m-y", strtotime($course['Date'])); ?></b></p>

                                        <br>
                                        <h3>Time</h3>
                                        <p><b><?= date("H:i", strtotime($course['StartTime'])); ?></b> <i> - </i> <b><?= date("H:i", strtotime($course['EndTime'])); ?></b></p>
                                        <?php
                                            if (isset($_SESSION['studentId']) && $studentCourse->rowCount() > 0 || isset($_SESSION['providerId']) || isset($_SESSION['instructorId'])) 
                                            {
                                                echo "
                                                        <br>
                                                        <h3>Livestream Link</h3>
                                                        <a href=\"{$course['Link']}\">{$course['Link']}</a>
                                                ";
                                            }
                                        ?>
                                    </div>
                                    <div class="provider">
                                        <img src="uploads/<?= $provider['ProfilePicture']; ?>" alt="" class="profile-pic">
                                        <h2><?= $provider['Company']?></h2>
                                    </div>
                                </div>
                        <?php
                            if (isset($_SESSION['studentId']))
                            {
                                if ($courseEnded->rowCount() <= 0 && $courseHappening->rowCount() <= 0)
                                {
                        ?>
                                    <div class="card center actions">
                                        <form action="" method="POST">
                                            <input type="hidden" name="courseId" value="<?= $courseId; ?>">
                                            <?php
                                                if ($studentCourse->rowCount() <= 0)
                                                {
                                                    echo "<input class=\"button action\" type=\"submit\" name=\"enroll-course\" value=\"Enroll\">";
                                                }
                                                else
                                                {
                                                    echo "<input class=\"button action\" type=\"submit\" name=\"enroll-course\" value=\"Unenroll\">";
                                                }
                                            ?>
                                        </form>
                                    </div>
                        <?php
                                }
                                else if ($courseEnded->rowCount() > 0)
                                {
                                    echo "
                                            <div class=\"card center actions\">
                                                <p>This course has ended.</p>
                                            </div>
                                    ";
                                }
                                else if ($courseHappening->rowCount() > 0)
                                {
                                    echo "
                                            <div class=\"card center actions\">
                                                <p>This course is ongoing.</p>
                                            </div>
                                    ";
                                }
                            }
                            else if (isset($_SESSION['providerId']))
                            {
                        ?>
                                <div class="card center actions">
                                    <div class="link">
                                        <a href="course_update.php?id=<?= $courseId; ?>"><button class="button action">Edit</button></a>
                                    </div>
                                    <div class="link">
                                        <form action="" method="POST">
                                            <input type="hidden" name="courseId" value="<?= $courseId; ?>">
                                            <input type="submit" value="Delete" class="button action" onclick="return confirm('Delete <?= $course['Title']; ?>?');" name="delete-course">
                                        </form>
                                    </div>
                                </div>
                        <?php
                            }
                            if (isset($_SESSION['instructorId']))
                            {
                                if ($instructorCourse->rowCount() > 0)
                                {
                        ?>
                                 <div class="card center actions">
                                    <form action="" method="POST">
                                        <input type="hidden" name="courseId" value="<?= $courseId; ?>">
                                        <?php
                                            $courseInstructor = $instructorCourse->fetch(PDO::FETCH_ASSOC);
                                            if ($courseInstructor['Availability'] == 'Unavailable')
                                            {
                                                echo "<input class=\"button action\" type=\"submit\" name=\"respond-invite\" value=\"Available\">";
                                            }
                                            else if ($courseInstructor['Availability'] == 'Available')
                                            {
                                                echo "<input class=\"button action\" type=\"submit\" name=\"respond-invite\" value=\"Unavailable\">";
                                            }
                                            else if ($courseInstructor['Availability'] == '')
                                            {
                                                echo "<input class=\"button action\" type=\"submit\" name=\"accept-invite\" value=\"Accept\">";
                                                echo " <input class=\"button action\" type=\"submit\" name=\"decline-invite\" value=\"Decline\">";
                                            }
                                        ?>
                                    </form>
                                </div>
                        <?php
                                }
                            }
                        ?>
                        </div>


                        <h1 class="title">Instructors</h1>
                        <?php 
                            $courseInstructors = $con->prepare(
                                "SELECT * FROM CourseInstructor
                                 JOIN Instructor ON CourseInstructor.InstructorId = Instructor.InstructorId
                                 JOIN User ON Instructor.InstructorId = User.Id
                                 WHERE CourseId = ? AND Availability = ?"
                            );
                            $courseInstructors->execute([$courseId, 'Available']);

                            if ($courseInstructors->rowCount() > 0) 
                            {
                                echo "<div class=\"short\">";
                                while ($courseInstructor = $courseInstructors->fetch(PDO::FETCH_ASSOC)) 
                                {
                                    instructor($courseInstructor);
                                }
                                echo "</div>";
                            }
                            else 
                            {
                                sorry("We couldn't find any instructors for this course.", $gap=false);
                            }
                        ?>
                    <?php 
                        }
                        else 
                        {
                            sorry("We couldn't find for the course you're looking for.", $gap=false);
                        }
                    ?>

                    <h1 class="title">Feedback</h1>
                    <?php
                        $pastCourse = $con->prepare(
                            "SELECT * FROM Course 
                             WHERE Course.Id = ?
                             AND NOW() > Course.EndTime"
                        );
                        $pastCourse->execute([$courseId]);
                        
                        if (isset($_SESSION['studentId']))
                        {
                            if ($studentCourse->rowCount() > 0 && $pastCourse->rowCount() > 0)
                            {
                    ?>
                                <form action="" method="POST">
                                    <input type="hidden" name="courseId" value="<?= $courseId; ?>">
                                    <div class="short no-gap">
                                        <div class="card">
                                            <div class="field input">
                                                <textarea name="feedback" id="feedback" cols="30" rows="5" maxlength="2055" required></textarea>
                                            </div>
                                            <input class="button submit no-gap" type="submit" name="post-feedback" value="Post">
                                        </div>
                                    </div>
                                </form>
                    <?php
                            }
                        }
                    ?>

                    <?php
                        $comments = $con->prepare(
                            "SELECT * FROM Comment 
                             JOIN Course ON Comment.CourseId = Course.Id
                             WHERE Comment.CourseId = ? 
                             ORDER BY CommentDate DESC"
                        );
                        $comments->execute([$courseId]);
                        if ($comments->rowCount() > 0) 
                        {
                            echo "<div class=\"short\">";
                            while ($comment = $comments->fetch(PDO::FETCH_ASSOC)) 
                            {
                                $commentingStudent = $con->prepare("SELECT * FROM Student JOIN User ON StudentId = User.Id WHERE StudentId = ?");
                                $commentingStudent->execute([$comment['StudentId']]);
                                $student = $commentingStudent->fetch(PDO::FETCH_ASSOC);

                                feedback($comment, $student);
                            }
                        }
                        else 
                        {
                            sorry("We couldn't find any feedback yet.", $gap=false);
                        }
                    ?>
                </div>
            </main>
    <?php
        }
    ?>
<?php include("inc/footer.inc.php") ?>    
<script src="js/script.js"></script>
</body>
</html>