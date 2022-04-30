<?php
// Get id
$id = $_GET["id"];
if ($id == null)
{
    http_response_code(400);
    die("id GET field cannot be null");
}

// Parse id
$inted = intval($id);
if ($inted == 0)
{
    http_response_code(400);
    die("id GET field must be a valid id");
}

// Connect SQL
$host = "172.30.128.1";
$username = "ArbeidstimerTest";
$password = "4k@3mD4MNABsm!D!";
$query = "SELECT [ActivityDescription], [Hours], [Category], [Date], [dbo].[Users].[UnitMembership], [IsVerified], CONCAT([dbo].[Users].[FirstName], ' ', [dbo].[Users].[LastName]) AS [Name], CONCAT([sVisor].[FirstName], ' ', [sVisor].[LastName]) AS [Supervisor] FROM [dbo].[HourDefinitions] LEFT JOIN [dbo].[Users] ON [dbo].[Users].[Id] = [dbo].[HourDefinitions].[UserId] LEFT JOIN [dbo].[Users] as [sVisor] ON [sVisor].[Id] = [dbo].[HourDefinitions].[Supervisor] WHERE [dbo].[HourDefinitions].[Id] = " . $inted . ";";

$conn = sqlsrv_connect($host, array("UID" => $username, "PWD" => $password, "Database" => "Arbeidstimer", "TrustServerCertificate" => 1, "CharacterSet" => "UTF-8"));

// Do query
$result = sqlsrv_query($conn, $query);
$record = sqlsrv_fetch_array($result);

// Check that the ID has not been verified
if ($record["IsVerified"] == 1)
{
    http_response_code(400);
    die("id GET must not refer to an accepted record");
}

?>

<!doctype HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Revider arbeidstimer</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <!-- Bootstrap JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    </head>
    <body>
    <!-- Heading -->
    <h1 style="text-align: center;">Revider arbeidstimer</h1>
    <hr>

    <div class="container">
        <!-- Data table -->
        <table class="table">
            <thead>
            <tr>
                <th>Datapunkt</th>
                <th>Verdi</th>
            </tr>
            </thead>
            <tbody>
            <!-- Name -->
            <tr>
                <td>Navn</td>
                <td><?php echo $record["Name"]; ?></td>
            </tr>
            <!-- Unit -->
            <tr>
                <td>Enhet</td>
                <td><?php echo $record["UnitMembership"]; ?></td>
            </tr>
            <!-- Date -->
            <tr>
                <td>Dato</td>
                <td><?php echo date_format($record["Date"],"Y-m-d"); ?></td>
            </tr>
            <!-- Category -->
            <tr>
                <td>Kategori</td>
                <td><?php echo $record["Category"]; ?></td>
            </tr>
            </tbody>
        </table>

        <!-- Description -->
        <h5>Beskrivelse</h5>
        <p>
            <?php echo $record["ActivityDescription"]; ?>
        </p>

        <form action="verdict.php" method="post" id="revForm">
            <!-- Hours -->
            <div class="mb-3">
                <label for="hourInput" class="form-label"><h5>Antall timer</h5></label>
                <input type="number" class="form-control" id="hourInput" name="hours" min="0.5" max="24" step="0.5" value="<?php echo $record["Hours"]; ?>">
            </div>

            <!-- Hidden inputs -->
            <input type="hidden" name="accept" id="verdictInput" value="1">
            <input type="hidden" name="id" value="<?php echo $inted; ?>">
        </form>


        <!-- Actions -->
        <div class="justify-content-end d-flex w-100">
            <button class="btn btn-danger me-2" onclick="document.querySelector('#verdictInput').value = 0;document.querySelector('#revForm').submit()">Avvis</button>
            <button class="btn btn-primary" onclick="document.querySelector('#revForm').submit()">Godkjenn</button>
        </div>
    </div>


    </body>
</html>