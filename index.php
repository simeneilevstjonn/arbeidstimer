<!doctype HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Arbeidstimer</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <!-- Bootstrap JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    </head>
    <body>
    <!-- Heading -->
    <h1 style="text-align: center;">Arbeidstimer</h1>
    <hr>

    <!-- Scoreboard container -->
    <div class="container">
        <table class="table">
            <thead>
            <tr>
                <th>Rangering</th>
                <th>Person</th>
                <th>Enhet</th>
                <th>Antall timer</th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Database things
            $host = "172.30.128.1";
            $username = "ArbeidstimerTest";
            $password = "4k@3mD4MNABsm!D!";
            $query = "SELECT CONCAT([FirstName], ' ', [LastName]) AS [Name], [UnitMembership], [Hours] FROM ( SELECT [UserId], SUM([Hours]) AS [Hours] FROM ( SELECT [UserId], FLOOR(CAST([Hours] AS DECIMAL(16, 4)) / CAST(POWER(2, FLOOR(CAST(DATEDIFF(DAY, [Date], CAST(GETDATE() AS DATE)) AS DECIMAL(16, 4)) / 365.0)) AS DECIMAL(16, 4))) AS [Hours] FROM [dbo].[HourDefinitions] WHERE [IsVerified] = 1 ) AS [Data] GROUP BY [UserId] )  AS Definitions JOIN [dbo].[Users] ON [dbo].[Users].[Id] = Definitions.[UserId] WHERE [Hours] > 0 AND [UnitMembership] IS NOT NULL ORDER BY [Hours] DESC;";

            $conn = sqlsrv_connect($host, array("UID" => $username, "PWD" => $password, "Database" => "Arbeidstimer", "TrustServerCertificate" => 1, "CharacterSet" => "UTF-8"));

            if( $conn === false ) {
                die( print_r( sqlsrv_errors(), true));
            }


            // Do query
            $result = sqlsrv_query($conn, $query);

            // Print lines
            $i = 1;
            $lastScore = 2147483647;
            $lastPlace = 1;
            while ($record = sqlsrv_fetch_array($result))
            {
                $place = $i++;

                // Find this persons place.
                if ($lastScore == $record["Hours"]) $place = $lastPlace;
                else $lastPlace = $place;

                echo "<tr><td>" . $place . ".</td><td>" . $record["Name"] . "</td><td>" . $record["UnitMembership"] . "</td><td>" . $record["Hours"] . "</td></th>\n";

                // Assign to lastscore helper
                $lastScore = $record["Hours"];
            }
            ?>
            </tbody>
        </table>
    </div>
    </body>
</html>