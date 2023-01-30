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
if (select_category_produits != null) { // Evite l'incompatibilité entre les page
    // Fait en sorte que quand tu changes d'option dans le sélecteur afin de choisir la catégorie que tu veux ça change de page en rajoutant /category/[id de la category]
    select_category_produits.addEventListener('change', event => {
        if (select_category_produits.options[select_category_produits.selectedIndex].value != 0) {

            window.location.href = "/produits/category/" + select_category_produits.options[select_category_produits.selectedIndex].value
        } else {
            window.location.href = "/produits/"
        }
    });
}

let slug = location.pathname.split('category/').slice(1);

if (slug.length > 0) {
    select_category_produits.value = slug;
};

menu_burger = document.querySelector(".div-logo-burger")
if (menu_burger != null) { // Evite l'incompatibilité entre les page
    menu_burger.addEventListener('click', event => {

        if (document.querySelector(".div-burger").classList.contains("hidden")) {
            document.querySelector(".div-burger").classList.remove("hidden")
        } else {
            document.querySelector(".div-burger").classList.add("hidden")
        }

    });
}

btn_ajout_prestation = document.querySelector("#ajout_prestation");
if (btn_ajout_prestation != null) { // Evite l'incompatibilité entre les page
    // Fait en sorte que quand tu appuies sur le + en dessous de prestation cela rajoute un input afin de pouvoir mettre une autre prestations dans le même RDV ( max 3)
    btn_ajout_prestation.addEventListener('click', button => {
        button.preventDefault();
        let presta = 1;
        console.log(document.querySelector(".hidden"));
        if (presta == 1) {
            presta = presta + 1
            console.log(presta);
        }
        document.querySelector(".hidden").classList.remove("hidden")

        document.querySelector(".hidden-div").classList.add("reservation-div-input")
        document.querySelector(".hidden-div").classList.remove("hidden-div")

        document.querySelector(".hidden_label").classList.remove("hidden_label")
    });
}
if (window.screen.width < 768) {
    document.getElementById("footer-logo_fb-img").src = "https://img.icons8.com/fluency/96/000000/facebook-new.png"

    console.log("logo");
}