<?php
session_start();

$mysqli = require __DIR__ . "/database.php";

$is_updated = false;
$user = [];

if (isset($_SESSION["user_id"])) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $dob = $_POST["dob"];
        $address = $_POST["address"];

        $update_query = $mysqli->prepare("UPDATE user SET name=?, email=?, phone=?, dob=?, address=? WHERE id=?");

        $update_query->bind_param("ssissi", $name, $email, $phone, $dob, $address, $_SESSION["user_id"]);
        if ($update_query->execute()) {
            $is_updated = true;
        } else {
            echo "Error updating record: " . $mysqli->error;
        }

        $update_query->close();
    }

    $sql = "SELECT * FROM user WHERE id = {$_SESSION["user_id"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();
    
    echo json_encode(["user" => $user, "is_updated" => $is_updated]);
    
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $age = $_POST["phone"];
    $dob = $_POST["dateofbirth"];
    $add= $_POST["address"];
    $email=$_POST["email"];


    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $mongoClient->sandeep->auth;

    $result = $collection->updateOne(
        ['email' => $email],
        ['$set' => [
            'name' => $name,
            'phone' => $age,
            'dob' => $dob,
            'add' => $add,
        ]]
    );

    if ($result->getModifiedCount() > 0) {
        echo "User updated successfully!";
    } else {
        echo "Error updating user: " . $result->getWriteErrors()[0]->getMessage();
    }
}

?>
