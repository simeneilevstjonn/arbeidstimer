<?php
// Get the form data
// Id
$id = $_POST["id"];
if ($id == null)
{
    http_response_code(400);
    die("id POST field cannot be null");
}

// Parse id
$inted = intval($id);
if ($inted == 0)
{
    http_response_code(400);
    die("id POST field must be a valid id");
}

// Hours
$hours = $_POST["hours"];
if ($hours == null)
{
    http_response_code(400);
    die("hours POST field cannot be null");
}
$parsedhours = floatval($hours);
if ($parsedhours == null)
{
    http_response_code(400);
    die("hours POST field cannot be null");
}

// Accept
$accept = boolval($_POST["accept"]);
if ($accept === null)
{
    http_response_code(400);
    die("accept POST field cannot be null");
}


// Connect to SQL server
// Get sql login from file
$loginFile = fopen("../sqllogin.txt", "r") or die("Failed to get login file.");
$lines = explode("\n", fread($loginFile, filesize("../sqllogin.txt")));
$host = trim($lines[0]);
$username = trim($lines[1]);
$password = trim($lines[2]);

$conn = sqlsrv_connect($host, array("UID" => $username, "PWD" => $password, "Database" => "Arbeidstimer", "TrustServerCertificate" => 1, "CharacterSet" => "UTF-8"));

if( $conn === false ) {
    die( print_r( sqlsrv_errors(), true));
}

// Update if accept is true
if ($accept)
{
    $query = "UPDATE [dbo].[HourDefinitions] SET [IsVerified] = 1, [Hours] = " . $parsedhours ." WHERE [Id] = " . $inted . ";";
    // Do query
    $result = sqlsrv_query($conn, $query);
}
// Delete if false
else
{
    $query = "DELETE FROM [dbo].[HourDefinitions] WHERE [Id] = " . $inted . ";";
    // Do query
    $result = sqlsrv_query($conn, $query);
}

// Redirect to list
header("Location: list.php");
http_response_code(302);
die("Success");
?>