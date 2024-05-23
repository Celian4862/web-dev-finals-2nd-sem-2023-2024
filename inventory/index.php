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
	<title>Work In Progress</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css"/>
	<link rel="stylesheet" type="text/css" href="../inventory/style.css"/>
	<script src="https://kit.fontawesome.com/f3364d5594.js" crossorigin="anonymous"></script>
	<script src="script.js"></script>
</head>
<body class="container">
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
						location.href = "../userInput/userInput.php";
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
		<button class="return">
			<span class="fa-solid fa-arrow-right-to-bracket fa-2xl"></span>
		</button>
	</div>
	<div class="content-container">
			<div class="header"></div>
			<div class="content-header">
				<span>Inventory</span>
				<div>
					<button onclick="inventoryInput_link()">
						<script>
							function inventoryInput_link() {
								location.href = "../inventoryInput/inventoryInput.php";
							}
						</script>
						Add Item
					</button>
				</div>
				<a href=""><p>Back to list</p></a>
				<!-- TODO: ADD ARROWS FOR TABS (i'll do this tomorrow plz) -->
				<!-- <i class="fa-solid fa-arrow-left"></i> -->
				<!-- <i class="fa-solid fa-arrow-right"></i> -->
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
					<tr>
						<td><?= htmlspecialchars($product['id']) ?></td>
						<td><?= htmlspecialchars($product['label']) ?></td>
						<td><?= htmlspecialchars($product['product_description']) ?></td>
						<td><?= htmlspecialchars($product['warranty']) ?></td>
						<td class="edit-cell">
							<button onclick="openUpdateForm('<?= $product['id'] ?>')" class="edit">
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
		<div id="myModal" class="modal">
			<div class="modal-content">
				<span class="close" onclick="closeModal()">&times;</span>
				<form id="updateForm" action="includes/product_update.inc.php" method="post">
					<input type="hidden" name="id" id="id">
					<input type="text" name="label" id="label" placeholder="New label">
					<input type="text" name="product_description" placeholder="New Description">
					<input type="text" name="warranty" id="warranty" placeholder="New Warranty">
					<button type="submit">Update Product</button>
				</form>
			</div>	
		</div>
		</div>
</body>
</html>

