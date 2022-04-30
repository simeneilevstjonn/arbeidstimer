<?php
// Get the form data
// Date
$date = $_POST["date"];
if ($date == null)
{
    http_response_code(400);
    die("date POST field cannot be null");
}

// Attempt to parse date
$parseddate = date_parse($date);
if ($parseddate["error_count"] + $parseddate["warning_count"] != 0)
{
    http_response_code(400);
    die("date POST field must contain a valid date");
}


// Supervisor
$supervisor = htmlspecialchars($_POST["supervisor"], ENT_QUOTES);
if ($supervisor == null)
{
    http_response_code(400);
    die("supervisor POST field cannot be null");
}

// Description
$description = htmlspecialchars($_POST["description"], ENT_QUOTES);
if ($description == null)
{
    http_response_code(400);
    die("description POST field cannot be null");
}

// Checkboxes
$gruppearbeid = boolval($_POST["gruppearbeid"]) ?? false;
$nordsjo = boolval($_POST["nordsjo"]) ?? false;
$gandsfjord = boolval($_POST["gandsfjord"]) ?? false;
$sisu = boolval($_POST["sisu"]) ?? false;
$synergie = boolval($_POST["synergie"]) ?? false;
$smabat = boolval($_POST["smabat"]) ?? false;

// Serialised people data
$peopledata = $_POST["people"];
if ($peopledata == null)
{
    http_response_code(400);
    die("people POST field cannot be null");
}

// Parse JSON
$json = Array(json_decode($peopledata));

// Check that the JSON has data
if (sizeof($json) == 0)
{
    http_response_code(400);
    die("people POST field must contain data");
}

// Format the description
$desc = "";
if ($gruppearbeid) $desc .= "[Gruppearbeid] ";
if ($nordsjo) $desc .= "[Nordsjø] ";
if ($gandsfjord) $desc .= "[Gandsfjord] ";
if ($sisu) $desc .= "[Sisu] ";
if ($synergie) $desc .= "[Synergie] ";
if ($smabat) $desc .= "[Småbåter] ";
$desc .= $description;


// Connect to SQL server
// Get sql login from file
$loginFile = fopen("sqllogin.txt", "r") or die("Failed to get login file.");
$lines = explode("\n", fread($loginFile, filesize("sqllogin.txt")));
$host = trim($lines[0]);
$username = trim($lines[1]);
$password = trim($lines[2]);

$conn = sqlsrv_connect($host, array("UID" => $username, "PWD" => $password, "Database" => "Arbeidstimer", "TrustServerCertificate" => 1, "CharacterSet" => "UTF-8"));

if( $conn === false ) {
    die( print_r( sqlsrv_errors(), true));
}

// Validate that the supervisor exists
$query = "SELECT COUNT(*) AS [C] FROM [dbo].[Users] WHERE [IsSupervisor] = 1 AND [Id] = N'" . $supervisor . "'";
// Do query
$result = sqlsrv_query($conn, $query);

while ($record = sqlsrv_fetch_array($result))
{
    if ($record["C"] != "1")
    {
        http_response_code(400);
        die("supervisor POST field must contain a valid supervisor ID.");
    }
}

// Add all records
// Create tran
$result = sqlsrv_query($conn, "BEGIN TRAN T1;");
$error = false;

foreach ($json[0] as $id => $hours)
{
    // Construct query
    $query = sprintf("INSERT INTO [dbo].[HourDefinitions] ([Date], [UserId], [Hours], [Category], [ActivityDescription], [Supervisor]) VALUES ('%s-%s-%s',N'%s',%E, N'Gruppe',N'%s',N'%s');",
        $parseddate["year"],
        $parseddate["month"],
        $parseddate["day"],
        $id,
        $hours,
        $desc,
        $supervisor
    );

    // Do query
    $result = sqlsrv_query($conn, $query);

    // Check for error
    if ($result === false)
    {
        $error = true;
        break;
    }
}

// Rollback or commit tran
if ($error)
{
    $result = sqlsrv_query($conn, "ROLLBACK TRAN T1;");
    http_response_code(400);
    die("One or more supplied person IDs were invalid.");
}
else
{
    $result = sqlsrv_query($conn, "COMMIT TRAN T1;");
    echo "success";
}
?>