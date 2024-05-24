<?php
require_once "includes/dbh.inc.php";

try {
    $query = "SELECT id, label, product_description, warranty FROM products";
    $stmt = $pdo->query($query);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Inventory</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
	<script src="https://kit.fontawesome.com/f3364d5594.js" crossorigin="anonymous"></script>
	<script src="../js/script.js" defer></script>
</head>
<body>
	<div class="container" id="brightness">
		<div class="navbar">
			<div class="center">
				<div class="logo-container">
					<img src="../images/n2n.png" alt="n2n logo" />
				</div>
			</div>
			<div class="tabs">
				<button onclick="dashboard_link()">
					<script>
						function dashboard_link() {
							location.href = "../dashboard/index.html";
						}
					</script>
					<i class="fa-solid fa-table-columns" style="color: #ffffff;"></i>
					<span>Dashboard</span>
				</button>
				<button onclick="user_link()">
					<script>
						function user_link() {
							location.href = "../users/index.php";
						}
					</script>
					<i class="fa-solid fa-user" style="color: #ffffff;"></i>
					<span>User</span>
				</button>
				<button class="active">
					<i class="fa-solid fa-dolly" style="color: #ffffff;"></i>
					<span>Inventory</span>
				</button>
				<button onclick="shipment_link()">
					<script>
						function shipment_link() {
							location.href = "../shipments/index.html";
						}
					</script>
					<i class="fa-solid fa-truck" style="color: #ffffff;"></i>
					<span>Shipments</span>
				</button>
				<button onclick="reception_link()">
					<script>
						function reception_link() {
							location.href = "../receptions/index.html";
						}
					</script>
					<i class="fa-solid fa-truck-ramp-box" style="color: #ffffff;"></i>
					<span>Reception</span>
				</button>
			</div>
			<button class="return" onclick="logout_link()">
				<script>
					function logout_link() {
						location.href = "../loginPage/index.php";
					}
				</script>
				<span class="fa-solid fa-arrow-right-to-bracket fa-2xl"></span>
			</button>
		</div>
		<div class="content-container">
				<div class="header"></div>
				<div class="content-header">
					<span>Inventory</span>
					<div class="button-container">
						<button onclick="inventoryInput_link()">
							<script>
								function inventoryInput_link() {
									location.href = "../inventoryInput/inventoryInput.php";
								}
							</script>
							Add Item
						</button>
					</div>
					<div class="arrows">
						<a href=""><i class="fa-solid fa-arrow-left fa-xl"></i></a>
						<a href=""><i class="fa-solid fa-arrow-right fa-xl"></i></a>
					</div>
				</div>
				<div class="box-container">
					<table>
						<tr class="table-header">
							<th>ID</th>
							<th>Label</th>
							<th>Description</th>
							<th>Warranty</th>
							<th>Edit</th>
						</tr>
						<?php foreach ($products as $product): ?>
						<tr class="table-content">
							<td><?= htmlspecialchars($product['id']) ?></td>
							<td><?= htmlspecialchars($product['label']) ?></td>
							<td><?= htmlspecialchars($product['product_description']) ?></td>
							<td><?= htmlspecialchars($product['warranty']) ?></td>
							<td class="edit-cell">
								<button onclick="openUpdateForm('<?= $product['id'] ?>'); toggle()" class="edit">
									<i class="fa-solid fa-gear" style="color: #ffffff;"></i>
								</button>
								<form action="includes/product_delete.inc.php" method="POST" >
									<input type="hidden" name="id" value="<?= $product['id'] ?>">
									<button type="submit" class="delete">
										<i class="fa-solid fa-trash" style="color: #ffffff;"></i>
									</button>
								</form>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-content" id="myModal">
		<div class="modal-header">
            <h1>Edit</h1>
            <span class="close" onclick="toggle()">&times;</span>
        </div>
		<form id="updateForm" action="includes/product_update.inc.php" method="post">
			<input type="hidden" name="id" id="id">
			<input type="text" name="label" id="label" placeholder="Label">
			<input type="text" name="product_description" placeholder="Description">
			<input type="text" name="warranty" id="warranty" placeholder="Warranty">
			<button type="submit" onclick="toggle()">Update</button>
		</form>
	</div>	
</body>
</html>

