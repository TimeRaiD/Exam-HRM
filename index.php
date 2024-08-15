<?php

$host = 'localhost';         
$DatabaseName = 'test';       
$HostUser = 'root';           
$Hostpass = '';         

$connect = mysqli_connect($host, $HostUser, $Hostpass, $DatabaseName);

$last_updated = date('Y-m-d H:i:s');

if(isset($_POST['submitdata'])){
    $prefix = $_POST['prefix'];
    $name = $_POST['firstname'];
    $last_name = $_POST['lastname'];
    $birthdate = $_POST['birthdate'];
    $number = $_POST['number'];

    $birthdateDate = new DateTime($birthdate);
    $today = new DateTime('today');
    $age = $today->diff($birthdateDate)->y;

    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $profile_picture = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $profile_picture;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    
        if($check !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars($profile_picture). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    } else {
        echo "Error: " . $_FILES['profile_picture']['error'];
    }
    

    if($name != ""){
        $insert = "INSERT INTO account (Prefix, Name, Lastname, Dob, Age, Img, Number) 
                   VALUES ('$prefix', '$name', '$last_name', '$birthdate', '$age', '$profile_picture','$number')";
        mysqli_query($connect,$insert)or die("Could not insert");
        
        print "<script type='text/javescript'>alert('บันทึกสำเร็จ')</script>";
        print "<meta HTTP-EQUIV='Refresh' CONTENT='0'; URL=index.php>";
    }
}


$query = "SELECT * FROM account";
$result = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <title>MHR_Test</title>
    </head>
    <body>
        <header>
            <h1 class="tc">แบบทดสอบ</h1>
        </header>
        <main>
            <form action="index.php" method="POST" enctype="multipart/form-data">
                <label for="prefix">คำนำหน้าชื่อ:</label>
                <input list="prefixes" id="prefix" name="prefix">
                <datalist id="prefixes">
                    <option value="นาย">
                    <option value="นาง">
                    <option value="นางสาว">
                </datalist>
                <br><br>

                <label for="firstname">ชื่อ:</label>
                <input type="text" id="firstname" name="firstname" required>
                <br><br>

                <label for="lastname">นามสกุล:</label>
                <input type="text" id="lastname" name="lastname" required>
                <br><br>

                <label for="birthdate">วันเดือนปีเกิด:</label>
                <input type="date" id="birthdate" name="birthdate" required>
                <br><br>

                <label for="profile_picture">รูปภาพโปรไฟล์:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                <br><br>

                <label for="number">รายรับ/จ่าย</label>
                <input type="text" id="number" name="number" required>
                <br><br>

                <input type="submit" name="submitdata" value="บันทึกข้อมูล">
                <br><br>
            </form>

            <h2>แสดงกราฟจำนวนสมาชิกตามอายุ</h2>
            <canvas id="myChart" width="400px" height="100px"></canvas>

            <form action="index.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="search" placeholder="ค้นหาชื่อ-นามสกุล">
                

                <select id="age_role" name="age_role">
                <option value="DESC">มากไปน้อย</option>
                <option value="ASC">น้อยไปมาก</option>
                </select>
                <input type="submit" name="submit_search" value="ค้นหา">

            </form>


            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Prefix</th>
                        <th>Name</th>
                        <th>Lastname</th>
                        <th>Date of Birth</th>
                        <th><button id="sort-age" class="btn btn-link">Age</button></th>
                        <th>Profile Picture</th>
                        <th>Income/Expenses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search = '';
                    if (isset($_POST['submit_search'])) {
                        $search = $_POST['search'];
                    }

                    $order = 'ASC';
                    if (isset($_POST['age_role']) && $_POST['age_role'] == 'DESC') {
                        $order = 'DESC';
                    }
                    
                    $query = "SELECT * FROM account";
                    
                    if ($search != '') {
                        $query .= " WHERE Name LIKE '%$search%' OR Lastname LIKE '%$search%'";
                    }
                    
                    $query .= " ORDER BY Age $order";

                    $result = mysqli_query($connect, $query);

                    $count = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $count++;
                    ?>
                    <tr>
                        <td><?php echo $count; ?></td>
                        <td><?php echo $row['Prefix']; ?></td>
                        <td><?php echo $row['Name']; ?></td>
                        <td><?php echo $row['Lastname']; ?></td>
                        <td><?php echo $row['Dob']; ?></td>
                        <td><?php echo $row['Age']; ?></td>
                        <td>
                            <?php if($row['Img'] != '') { ?>
                                <img src="uploads/<?php echo $row['Img']; ?>" width="100" height="100">
                            <?php } else { ?>
                                No Image
                            <?php } ?>
                        </td>
                        <td><?php echo $row['Number']; ?></td>
                        <td>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal<?php echo $row['Id_account']; ?>">
                                แก้ไข
                            </button>
                            <a href="#" class="btn btn-danger delete-btn" data-id="<?php echo $row['Id_account']; ?>">ลบ</a>
                        </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="editModal<?php echo $row['Id_account']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">แก้ไขข้อมูล</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form class="edit-form" action="edit.php?id=<?php echo $row['Id_account']; ?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $row['Id_account']; ?>">
                                        <div class="form-group">
                                            <label for="prefix">Prefix</label>
                                            <input type="text" class="form-control" name="prefix" value="<?php echo $row['Prefix']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="firstname">First Name</label>
                                            <input type="text" class="form-control" name="firstname" value="<?php echo $row['Name']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="lastname">Last Name</label>
                                            <input type="text" class="form-control" name="lastname" value="<?php echo $row['Lastname']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" class="form-control" name="birthdate" value="<?php echo $row['Dob']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="profile_picture">Profile Picture</label>
                                            <input type="file" class="form-control" name="profile_picture">
                                            <img src="uploads/<?php echo $row['Img']; ?>" width="100" height="100" style="margin-top: 10px;">
                                        </div>
                                        <div class="form-group">
                                            <label for="number">Income/Expenses</label>
                                            <input type="text" class="form-control" name="number" value="<?php echo $row['Number']; ?>">
                                        </div>
                                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </tbody>
            </table>

        </main>
        <?php 
        $query = "SELECT Age, COUNT(*) AS count FROM `account` GROUP BY Age";
        $result = mysqli_query($connect, $query);
        
        $age_g = [];
        $num_g = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $age_g[] = $row['Age'];  
                $num_g[] = $row['count']; 
            }
        }
        
        $age_g = implode(",", $age_g);
        $num_g = implode(",", $num_g);
        ?>
        <script>
            const ctx = document.getElementById('myChart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                labels: [<?php echo $age_g;?>],
                datasets: [{
                    label: 'อายุ',
                    data: [<?php echo $num_g;?>],
                    borderWidth: 1
                }]
                },
                options: {
                scales: {
                    y: {
                    beginAtZero: true
                    }
                }
                }
            });
            </script>

    </body>
</html>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<script>
$(document).on('submit', '.edit-form', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: 'edit.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response == 'success') {
                alert('Updated successfully!');
                location.reload();
            } else {
                alert('Error occurred while updating!');
            }
        }
    });
});
</script>


<script>
$(document).on('click', '.delete-btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    if (confirm('Are you sure you want to delete this record?')) {
        $.ajax({
            type: 'POST',
            url: 'delete.php',
            data: { id: id },
            success: function(response) {
                if (response == 'success') {
                    alert('Deleted successfully!');
                    location.reload();
                } else {
                    alert('Error occurred while deleting!');
                }
            }
        });
    }
});
</script>
