function openUpdateForm(id, email) {
    document.getElementById('id').value = id;
    document.getElementById('email').value = email;
    document.getElementById('myModal').style.display = 'block';
}

function closeUpdateForm() {
    document.getElementById('myModal').style.display = 'none';
}

//this is only for users