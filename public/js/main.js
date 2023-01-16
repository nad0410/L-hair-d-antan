document.querySelectorAll('#comf_delete').forEach(item => {
    item.addEventListener('click', event => {
        let text = "Voulez-vous vraiment supprimer ce produits ?";
        if (confirm(text) == true) {
        } else {
            event.preventDefault();
        }
    })
})

select_category_produits = document.querySelector("#choice_category");

let slug = location.pathname.split('category/').slice(1)

if (slug.length > 0) {
    select_category_produits.value = slug;
}

select_category_produits.addEventListener('change', event => {
    if (select_category_produits.options[select_category_produits.selectedIndex].value != 0) {

        window.location.href = "/produits/category/" + select_category_produits.options[select_category_produits.selectedIndex].value;
    } else {
        window.location.href = "/produits/";
    }

})

document.querySelectorAll('.div-card-produits').forEach(item => {
    item.addEventListener('', event => {
        console.log("test");
    })
})