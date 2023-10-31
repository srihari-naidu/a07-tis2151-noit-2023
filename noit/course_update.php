<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");

    if (isset($_GET['id']))
    {
        $id = $_GET['id'];
        $courseId = $id;
    }
    else
    {
        header('Location: login.php');
    }

    if (isset($_SESSION['providerId'])) 
    {
        $providerId = $_SESSION['providerId'];

        $provider = $con->prepare(
            "SELECT * FROM Provider 
             JOIN User ON ProviderId = User.Id 
             WHERE ProviderId = ?"
        );
        $provider->execute([$providerId]);
        $provider = $provider->fetch(PDO::FETCH_ASSOC);
    }
    else 
    {
        header('Location: login.php');
    }

    if (isset($_POST['submit']))
    {
        $title = $_POST['title'];
        $description = $_POST['description'];

        $cDate = $_POST['date'];
        $date = date("Y-m-d", strtotime($cDate));

        $sTime = $_POST['start-time'];
        $startTime = $date.' '.date("H:i:s",strtotime($sTime));

        $eTime = $_POST['end-time'];
        $endTime = $date.' '.date("H:i:s",strtotime($eTime));

        $link = $_POST['link'];

        $old_image = $_POST['old_image'];
        $thumb = $_FILES['image']['name'];
        $ext = pathinfo($thumb, PATHINFO_EXTENSION);
        $thumbnail = uniqid().'.'.$ext;
        $thumb_size = $_FILES['image']['size'];
        $thumb_tmp_name = $_FILES['image']['tmp_name'];
        $thumb_folder = 'uploads/'.$thumbnail;

        $updCourse = $con->prepare(
            "UPDATE Course 
             SET Title = ?, 
                 Description = ?,
                 Date = ?,
                 StartTime = ?, 
                 EndTime = ?, 
                 Link = ?
             WHERE Course.Id = ?"
        );
        $updCourse->execute([$title, $description, $date, $startTime, $endTime, $link, $courseId]);
        move_uploaded_file($thumb_tmp_name, $thumb_folder);

        if (!empty($thumb))
        {
            $update_image = $con->prepare(
                "UPDATE Course 
                 SET Thumbnail = ? 
                 WHERE Course.Id = ?"
            );
            $update_image->execute([$thumbnail, $courseId]);
            move_uploaded_file($thumb_tmp_name, $thumb_folder);
            if ($old_image != '' AND $old_image != $thumbnail)
            {
                unlink('uploads/'.$old_image);
            }
         }

         if (!empty($_POST['instructor'])) 
         {
            $instructorIds = $_POST['instructor'];

            $courseInstructor = $con->prepare(
                "SELECT * FROM CourseInstructor
                 WHERE CourseId = ?"
            );
            $courseInstructor->execute([$courseId]);
            while($instructor = $courseInstructor->fetch(PDO::FETCH_ASSOC))
            {
                if (!in_array($instructor['InstructorId'], $instructorIds))
                {
                    $remCourseInstructor = $con->prepare(
                        "DELETE FROM CourseInstructor
                         WHERE InstructorId = ?"
                    );
                    $remCourseInstructor->execute([$instructor['InstructorId']]);
                }
            }

            foreach($instructorIds as $instructorId)
            {
                $courseInstructor = $con->prepare(
                    "SELECT * FROM CourseInstructor
                     WHERE CourseId = ?
                     AND InstructorId = ?"
                );
                $courseInstructor->execute([$courseId, $instructorId]);

                if ($courseInstructor->rowCount() <= 0)
                {
                    $insCourseInstructor = $con->prepare(
                        "INSERT INTO CourseInstructor (CourseId, InstructorId, Availability, Status)
                        VALUES (?, ?, ?, ?)"
                    );
                    $insCourseInstructor->execute([$courseId, $instructorId, '', 'Invited']);
                }
            }
        }
        else
        { 
            $courseInstructor = $con->prepare(
                "SELECT * FROM CourseInstructor
                 WHERE CourseId = ?"
            );
            $courseInstructor->execute([$courseId]);
            while($instructor = $courseInstructor->fetch(PDO::FETCH_ASSOC))
            {
                $remCourseInstructor = $con->prepare(
                    "DELETE FROM CourseInstructor
                        WHERE InstructorId = ?"
                );
                $remCourseInstructor->execute([$instructor['InstructorId']]);
            }

            $_SESSION['warning_no_instructors'] = "Course <b>{$title}</b> was added without instructors.";
        }

         $_SESSION['success_course_updated'] = "Course <b>{$title}</b> has been updated.";
         header('Location: course.php?id='.$courseId);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - Update Course</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <div class="container">
    <?php
        $course = $con->prepare(
            "SELECT *
             FROM Course
             JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
             WHERE Id = ?
             LIMIT 1"
        );
        $course->execute([$courseId]);
        $course = $course->fetch(PDO::FETCH_ASSOC);
    ?>
        <div class="card card-header">
            <h1>Update Course</h1>
        </div>
        <div class="card card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="field input">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" value="<?= $course['Title'] ?>" required>
                </div>
                <div class="field input">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" cols="30" rows="5" maxlength="2055" required><?= $course['Description'] ?></textarea>
                </div>
                <div class="field input file">
                    <label for="thumbnail">Thumbnail</label>
                    <img src="uploads/<?= $course['Thumbnail']; ?>" alt="" class="thumbnail">
                    <input type="hidden" name="old_image" value="<?= $course['Thumbnail']; ?>">
                    <input type="file" name="image" id="thumbnail" accept="image/*" class="file-input">
                </div>
                <div class="field input file">
                    <label for="link">Link</label>
                    <input type="text" name="link" id="link" value="<?= $course['Link'] ?>" required>
                </div>
                <div class="field input">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" class="date-input" value="<?= $course['Date'] ?>" required>
                </div>
                <div class="field input">
                    <label for="start-time">Start Time</label>
                    <input type="time" name="start-time" id="start-time" class="date-input" value="<?= date("H:i",strtotime($course['StartTime'])); ?>" required>
                </div>
                <div class="field input">
                    <label for="end-time">End Time</label>
                    <input type="time" name="end-time" id="end-time" class="date-input" value="<?= date("H:i",strtotime($course['EndTime'])); ?>" required>
                </div>
                <div class="field">
                    <label for="instructors">Instructors</label>
                    <?php
                        $companyInstructors = $con->prepare(
                            "SELECT * FROM Instructor
                             JOIN User ON Instructor.InstructorId = User.Id
                             WHERE Instructor.Company = ?"
                        );
                        $companyInstructors->execute([$provider['Company']]);

                        if ($companyInstructors->rowCount() > 0)
                        {
                            echo "<div class=\"checkbox\">";
                            while ($instructor = $companyInstructors->fetch(PDO::FETCH_ASSOC))
                            {
                                $courseInstructor = $con->prepare(
                                    "SELECT * FROM CourseInstructor
                                     WHERE CourseId = ?
                                     AND InstructorId = ?"
                                );
                                $courseInstructor->execute([$courseId, $instructor['Id']]);

                                if ($courseInstructor->rowCount() > 0)
                                {
                                    $cInstructor = $courseInstructor->fetch(PDO::FETCH_ASSOC);
                                    if ($cInstructor['Availability'] == 'Unavailable')
                                    {
                                        echo "
                                                <div class=\"option\">
                                                    <input disabled type=\"checkbox\" name=\"instructor[]\" value=\"{$instructor['Id']}\"/>
                                                    <p>{$instructor['Name']} — {$instructor['Company']} {$instructor['Profession']}</p>
                                                </div>
                                        ";
                                    }
                                    else
                                    {
                                        echo "
                                                <div class=\"option\">
                                                    <input checked type=\"checkbox\" name=\"instructor[]\" value=\"{$instructor['Id']}\"/>
                                                    <p>{$instructor['Name']} — {$instructor['Company']} {$instructor['Profession']}</p>
                                                </div>
                                        ";
                                    }
                                }
                                else
                                {
                                    echo "
                                            <div class=\"option\">
                                                <input type=\"checkbox\" name=\"instructor[]\" value=\"{$instructor['Id']}\"/>
                                                <p>{$instructor['Name']} — {$instructor['Company']} {$instructor['Profession']}</p>
                                            </div>
                                    ";
                                }
                            }
                            echo "</div>";
                        }
                        else
                        {
                            echo "<p>Sorry, we couldn't find any instructors from {$provider['Company']}.</p>";
                        }
                    ?>
                </div>
                <div class="links">
                    <div class="field">
                        <input class="button submit gap" type="submit" name="submit" value="Update">
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php include("inc/footer.inc.php") ?>    
<script src="js/script.js"></script>
</body>
</html>