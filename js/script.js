function openUpdateForm(id) {
    document.getElementById('id').value = id;
}

function toggle() {
	var popup = document.getElementById("myModal")
	var dark = document.getElementById("brightness")
	popup.classList.toggle("active")
	dark.classList.toggle("active")
}

//this is only for products