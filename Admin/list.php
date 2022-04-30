<?php
// Connect SQL
$host = "172.30.128.1";
$username = "ArbeidstimerTest";
$password = "4k@3mD4MNABsm!D!";
$query = "SELECT [dbo].[HourDefinitions].[Id], [Hours], [Date], CONCAT([dbo].[Users].[FirstName], ' ', [dbo].[Users].[LastName]) AS [Name], CONCAT([sVisor].[FirstName], ' ', [sVisor].[LastName]) AS [Supervisor] FROM [dbo].[HourDefinitions] LEFT JOIN [dbo].[Users] ON [dbo].[Users].[Id] = [dbo].[HourDefinitions].[UserId] LEFT JOIN [dbo].[Users] as [sVisor] ON [sVisor].[Id] = [dbo].[HourDefinitions].[Supervisor] WHERE [IsVerified] = 0 ORDER BY [Date] ASC;";

$conn = sqlsrv_connect($host, array("UID" => $username, "PWD" => $password, "Database" => "Arbeidstimer", "TrustServerCertificate" => 1, "CharacterSet" => "UTF-8"));

// Do query
$result = sqlsrv_query($conn, $query);
?>

<!doctype HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Ureviderte arbeidstimer</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <!-- Bootstrap JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    </head>
    <body>
        <!-- Heading -->
        <h1 style="text-align: center;">Ureviderte arbeidstimer</h1>
        <hr>

        <div class="container">
            <?php
            while ($record = sqlsrv_fetch_array($result))
            {
                echo "<a href=\"audit.php?id=" . $record["Id"] . "\" class=\"card rounded mb-2 container fs-6 text-decoration-none text-dark row\" style=\"display: flex\">";
                echo "<div class=\"col\">" . date_format($record["Date"],"Y-m-d") . "</div>";
                echo "<div class=\"col\">" . $record["Name"] . "</div>";
                echo "<div class=\"col\">" . $record["Hours"] . "</div>";
                echo "</a>";
            }
            ?>
        </div>
    </body>
</html>