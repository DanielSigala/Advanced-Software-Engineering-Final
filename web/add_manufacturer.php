<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Advanced Software Engineering</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <!-- MENU -->
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span the class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                </button>
                <!-- LOGO TEXT HERE -->
                <a href="#" class="navbar-brand">Add New Manufacturer</a>
            </div>
            <!-- MENU LINKS -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-nav-first">
                    <li><a href="index.php" class="smoothScroll">Home</a></li>
                    <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                    <li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
                </ul>
            </div>
        </div>
    </section>
    <!-- HOME -->
    <section id="home">
    </section>
    <!-- FEATURE -->
    <section id="feature">
        <div class="container">
            <h2>Add New Manufacturer</h2>
            <?php
			include("../db_connect.php");
			require_once("../log_function.php");

			if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['manufacturerName'])) {
				$dblink = db_connect();
				$manufacturerName = trim($_POST['manufacturerName']); // Trim to remove any extraneous whitespace

				// Data validation
				if (!is_string($manufacturerName) || strlen($manufacturerName) > 64) {
					echo "<div class='alert alert-danger' role='alert'>Error: Manufacturer name must be a string and not exceed 64 characters.</div>";
					log_action('Add Manufacturer Error', "Invalid manufacturer name provided: $manufacturerName");
				} else {
					$manufacturerName = $dblink->real_escape_string($manufacturerName);

					// Check if the manufacturer already exists
					$checkSql = "SELECT manufacturer_name FROM manufacturers WHERE manufacturer_name = '$manufacturerName'";
					$checkResult = $dblink->query($checkSql);

					if ($checkResult && $checkResult->num_rows > 0) {
						echo '<div class="alert alert-danger" role="alert">Manufacturer already exists in database!</div>';
						log_action('Add Manufacturer', "Attempted to add existing manufacturer: $manufacturerName");
					} else {
						$insertSql = "INSERT INTO manufacturers (manufacturer_name) VALUES ('$manufacturerName')";
						if ($dblink->query($insertSql)) {
							log_action('Add Manufacturer', "New manufacturer added: $manufacturerName");
							echo "<div class='alert alert-success' role='alert'>New manufacturer '$manufacturerName' added successfully.</div>";
							header("Location: index.php?msg=ManufacturerAdded");
							exit;
						} else {
							echo "<div class='alert alert-danger' role='alert'>Error adding new manufacturer: " . $dblink->error . "</div>";
							log_action('Add Manufacturer Failure', "Error adding new manufacturer: $manufacturerName - " . $dblink->error);
						}
					}
				}
			}
			?>
            <form action="add_manufacturer.php" method="post">
                <div class="form-group">
                    <label for="manufacturerName">Manufacturer Name:</label>
                    <input type="text" name="manufacturerName" id="manufacturerName" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Add Manufacturer</button>
            </form>
        </div>
    </section>
</body>
</html>
