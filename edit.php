<?php

$host = 'localhost';         
$DatabaseName = 'test';       
$HostUser = 'root';           
$Hostpass = '';         

$connect = mysqli_connect($host, $HostUser, $Hostpass, $DatabaseName);

if (isset($_POST['id'])) {
    $id = $_POST['id'];
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
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);

        $update_query = "UPDATE account SET Prefix='$prefix', Name='$name', Lastname='$last_name', Dob='$birthdate', Age='$age', Img='$profile_picture', Number='$number' WHERE Id_account=$id";
    } else {

        $update_query = "UPDATE account SET Prefix='$prefix', Name='$name', Lastname='$last_name', Dob='$birthdate', Age='$age', Number='$number' WHERE Id_account=$id";
    }

    if (mysqli_query($connect, $update_query)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
