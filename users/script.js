function openUpdateForm(id, email) {
    document.getElementById('id').value = id
    document.getElementById('email').value = email
    // document.getElementById('myModal').style.display = 'block'
}

console.log('test')

function toggle() {
    var popup = document.getElementById("myModal")
    var dark = document.getElementById("brightness")
    popup.classList.toggle("active")
    dark.classList.toggle("active")
}