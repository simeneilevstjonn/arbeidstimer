<?php
    // Database things
    // Get sql login from file
    $loginFile = fopen("sqllogin.txt", "r") or die("Failed to get login file.");
    $lines = explode("\n", fread($loginFile, filesize("sqllogin.txt")));
    $host = trim($lines[0]);
    $username = trim($lines[1]);
    $password = trim($lines[2]);

    $query = "SELECT [Id], CONCAT([FirstName], ' ', [LastName]) AS [Name] FROM [dbo].[Users] WHERE [UnitMembership] = N'Vandrer' OR [UnitMembership] = N'Rover' ORDER BY [Name] ASC";

    $conn = sqlsrv_connect($host, array("UID" => $username, "PWD" => $password, "Database" => "Arbeidstimer", "TrustServerCertificate" => 1, "CharacterSet" => "UTF-8"));

    // Do query
    $result = sqlsrv_query($conn, $query);

    // Create array
    $users = array();

    while ($record = sqlsrv_fetch_array($result))
    {
        // Append to array
        $users[$record["Id"]] = $record["Name"];
    }

    // Get supervisors
    $query = "SELECT [Id], CONCAT([FirstName], ' ', [LastName]) AS [Name] FROM [dbo].[Users] WHERE [IsSupervisor] = 1 ORDER BY [Name] ASC";

    // Do query
    $result = sqlsrv_query($conn, $query);

    // Create array
    $supervisors = array();

    while ($record = sqlsrv_fetch_array($result))
    {
        // Append to array
        $supervisors[$record["Id"]] = $record["Name"];
    }

?>

<!doctype HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Registrer arbeidstimer</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <!-- Bootstrap 4 JS and jQuery -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>

        <!-- Select thing. Latest compiled and minified CSS and JS-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

        <!-- Special CSS for the dropdowns -->
        <style>
            .btn.dropdown-toggle.btn-light {
                background-color: #fff!important;
            }
            .dropdown.bootstrap-select.form-control {
                border: 1px solid #ced4da!important;
            }
            .bootstrap-select .dropdown-toggle:focus {
                outline: none!important;
            }
        </style>

        <script src="PeopleForm.js"></script>
        <script src="RegisterForm.js"></script>

        <script>
            const people = {
                <?php
                foreach ($users as $id => $name)
                    {
                        printf("\"%s\" : \"%s\",\n", $id, $name);
                    }
                ?>
            };

            let form;

            window.onload = () => {
                form = new PeopleForm(document.querySelector("#peopleTableBody"), people);
                form.appendRow();
            };
        </script>

    </head>
    <body>
    <!-- Heading -->
    <h1 style="text-align: center;">Registrer arbeidstimer</h1>
    <hr>

    <!-- Form -->
    <div class="container">
        <!-- Main data form -->
        <form action="submit.php" method="post" id="mainForm">
            <!-- Date -->
            <div class="mb-3">
                <label for="dateInput" class="form-label"><h5>Dato</h5></label>
                <input type="date" class="form-control" id="dateInput" name="date" value="<?php echo date("Y-m-d"); ?>">
            </div>

            <!-- Supervisor -->
            <div class="mb-3">
                <label for="supervisorInput" class="form-label"><h5>Arbeidsleder</h5></label>
                <select class="selectpicker form-control" data-live-search="true" name="supervisor" id="supervisorInput">
                    <!-- Default -->
                    <option value="null" disabled selected class="d-none">-</option>
                    <!-- Print users -->
                    <?php
                    foreach ($supervisors as $id => $name)
                    {
                        printf("<option value=\"%s\">%s</option>\n", $id, $name);
                    }
                    ?>
                </select>
            </div>

            <!-- Material -->
            <h5>Hva har det blitt jobbet med?</h5>

            <!-- Gruppearbeid -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="checkGruppearbeid" name="gruppearbeid">
                <label class="form-check-label" for="checkGruppearbeid">
                    Grupparbeid
                </label>
            </div>

            <!-- Nordsjø -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="checkNordsjo" name="nordsjo">
                <label class="form-check-label" for="checkNordsjo">
                    S/Y Nordsjø
                </label>
            </div>

            <!-- Gandsfjord -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="checkGandsfjord" name="gandsfjord">
                <label class="form-check-label" for="checkGandsfjord">
                    Gandsfjord
                </label>
            </div>

            <!-- Sisu -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="checkSisu" name="sisu">
                <label class="form-check-label" for="checkSisu">
                    Sisu
                </label>
            </div>

            <!-- Synergie -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="checkSynergie" name="synergie">
                <label class="form-check-label" for="checkSynergie">
                    Synergie
                </label>
            </div>

            <!-- Småbåter -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="checkSmabat" name="smabat">
                <label class="form-check-label" for="checkSmabat">
                    Småbåter
                </label>
            </div>

            <!-- Desciption -->
            <div class="mb-3 mt-3">
                <label for="descriptionInput" class="form-label"><h5>Hva har blitt gjort?</h5></label>
                <textarea class="form-control" id="descriptionInput" name="description"></textarea>
            </div>

            <!-- People -->
            <input type="hidden" name="people" id="peopleInput">
        </form>

        <!-- People -->
        <table class="table">
            <!-- Table header -->
            <thead>
            <tr>
                <th class="w-75">Navn</th>
                <th>Timer</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="peopleTableBody"></tbody>
        </table>

        <!-- Submission button -->
        <div class="justify-content-end d-flex w-100">
            <button class="btn btn-primary" onclick="submitForm()">Send</button>
        </div>
    </div>

    </body>
</html>