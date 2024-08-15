<?php
$host = 'localhost';         
$DatabaseName = 'test';       
$HostUser = 'root';           
$Hostpass = '';         

$connect = mysqli_connect($host, $HostUser, $Hostpass, $DatabaseName);

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM account WHERE Id_account = $id";
    if (mysqli_query($connect, $query)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
