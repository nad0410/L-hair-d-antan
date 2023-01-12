document.querySelectorAll('#comf_delete').forEach(item => {
    item.addEventListener('click', event => {
        let text = "Voulez-vous vraiment supprimer ce produits ?";
        if (confirm(text) == true) {
        } else {
            event.preventDefault();
        }
    })
})