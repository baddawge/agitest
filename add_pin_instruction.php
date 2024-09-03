<?php
/**
 * Script: add_pin_instruction.php
 *
 * This script handles the addition of PIN instructions.
 *
 * @author Cameron Holt <cholt@agi.net>
 * @copyright Copyright 2024 AGI. All rights reserved.
 *            Released under the Closed Source License.
 * @license   Closed Source License
 * @version   Release: 1.1
 * @link      add_pin_instruction.php
 * @since     Script available since Release 1.0.0
 */
include 'db.inc.php';
session_start();
include 'functions.inc.php';
$resourceId = 2;
$userId = $_SESSION['user_id'];
$roleId = $_SESSION['role_id'];

$userHasReadAccess = userHasAccess($userId, $resourceId, 'read');
$userHasWriteAccess = userHasAccess($userId, $resourceId, 'write');
$userHasDeleteAccess = userHasAccess($userId, $resourceId, 'delete');

if (!isset($_SESSION['authenticated'])) {
    header("Location: index.php");
    exit;
}

if (!$userHasWriteAccess) {
	echo "Unauthorized";
	$user_id = $_SESSION['user_id'];
	$action = "Unauthorized User Attempt";
	$details = "User $_SESSION[user_email] tried to add a pin instruction but didn't have the proper permissions.";

	if (insertAuditLog($user_id, $action, $details)) {}
	exit;
}

// Retrieve the pin_id from the query parameter
$selectedPinId = $_GET['pin_id'];

// Fetch the pin name based on the pin_id
$pinName = getPinNameById($selectedPinId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$pinId = filter_input(INPUT_POST, 'pin_id', FILTER_VALIDATE_INT);
	$instructionText = filter_input(INPUT_POST, 'instruction_text', FILTER_SANITIZE_STRING);
	$requiresPhoto = filter_input(INPUT_POST, 'requires_photo', FILTER_VALIDATE_INT);
	$isExisting = filter_input(INPUT_POST, 'is_existing', FILTER_VALIDATE_INT);

	// Check if all required fields are provided
	if ($pinId !== false && $instructionText !== null && $requiresPhoto !== null) {
		// Insert the new pin instruction into the database
		$insertResult = insertPinInstruction($pinId, $instructionText, $isExisting, $requiresPhoto);

		if ($insertResult) {
			// Pin instruction successfully inserted, redirect or display a success message
			header("Location: pins.php"); // Redirect to the pins page
			exit;
		} else {
			// Error occurred during insertion
			$error = "Error occurred while adding the pin instruction.";
		}
	} else {
		// Invalid or missing input values
		$error = "Invalid input values.";
	}
}
?>
<!DOCTYPE html>
<html lang="en" class="dark-sidebar ColorLessIcons">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/favicon.png">
    <title>Admin: Add Pin Question</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&family=Roboto&display=swap" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/icons.css" />
    <link rel="stylesheet" href="assets/css/app.css" />
    <link rel="stylesheet" href="assets/css/dark-sidebar.css" />
    <link rel="stylesheet" href="assets/css/dark-theme.css" />
    <style>
        .action-icons {
            text-align: center;
        }

        .action-icons .edit-icon,
        .action-icons .delete-icon {
            margin: 0 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="sidebar-wrapper" data-simplebar="true">
            <?php
            include 'sidebarheader.inc.php';
            include 'sidenav.inc.php';
            ?>
        </div>
        <?php
        include 'header.inc.php';
        ?>
        <div class="page-wrapper">
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
                        <div class="breadcrumb-title pe-3">Admin</div>
                        <div class="ps-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 p-0">
                                    <li class="breadcrumb-item"><a href="javascript:;"><i class='bx bx-map'></i></a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Pin Instructions</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
						<h5>Add Pin Instruction for <?php echo $pinName; ?></h5>
						<hr/>
						<form method="POST">
							<input type="hidden" name="pin_id" value="<?php echo $selectedPinId; ?>">
							<div class="mb-3">
								<label for="instruction_text" class="form-label">Instruction Text</label>
								<textarea name="instruction_text" class="form-control" id="instruction_text" placeholder="Enter instruction text" required></textarea>
							</div>
							<div class="mb-3">
								<label for="requires_photo" class="form-label">Requires Photo</label>
								<select name="requires_photo" id="requires_photo" class="form-select" required>
									<option value="1">Yes</option>
									<option value="0" selected>No</option>
								</select>
							</div>
							<div class="mb-3">
								<label for="is_existing" class="form-label">Is this for an Existing or Proposed Question?</label>
								<select name="is_existing" id="is_existing" class="form-select" required>
									<option>Choose One</option>
									<option value="1">Existing</option>
									<option value="0" selected>Proposed</option>
								</select>
							</div>
							<button type="submit" class="btn btn-primary">Add Pin Instruction</button>
						</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    </div>
    <?php include 'footer.inc.php'; ?>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>