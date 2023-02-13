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
if (select_category_produits != null) { // Evite l'incompatibilité entre les pages
    // Fait en sorte que quand tu changes d'option dans le sélecteur afin de choisir la catégorie que tu veux ça change de page en rajoutant /category/[id de la category]
    select_category_produits.addEventListener('change', event => {
        if (select_category_produits.options[select_category_produits.selectedIndex].value != 0) {

            window.location.href = "/produits/category/" + select_category_produits.options[select_category_produits.selectedIndex].value
        } else {
            window.location.href = "/produits/"
        }
    });
}

let slug_produits = location.pathname.split('category/').slice(1);
if (slug_produits.length > 0) {
    select_category_produits.value = slug_produits;
};

select_category_prestation = document.querySelector("#choice_category_prestations");
if (select_category_prestation != null) { // Evite l'incompatibilité entre les pages
    // Fait en sorte que quand tu changes d'option dans le sélecteur afin de choisir la catégorie que tu veux ça change de page en rajoutant /category/[id de la category]
    select_category_prestation.addEventListener('change', event => {
        if (select_category_prestation.options[select_category_prestation.selectedIndex].value != 0) {

            window.location.href = "/prestations/" + select_category_prestation.options[select_category_prestation.selectedIndex].value
        } else {
            window.location.href = "/prestations/0"
        }
    });
}
if (select_category_prestation) {
    let slug_prestations = location.pathname.split('prestations/').slice(1);
    if (slug_prestations.length > 0) {
        select_category_prestation.value = slug_prestations;
    };

}

select_category_prestation_admin = document.querySelector("#choice_category_prestations-admin");
if (select_category_prestation_admin != null) { // Evite l'incompatibilité entre les pages
    // Fait en sorte que quand tu changes d'option dans le sélecteur afin de choisir la catégorie que tu veux ça change de page en rajoutant /category/[id de la category]
    select_category_prestation_admin.addEventListener('change', event => {
        if (select_category_prestation_admin.options[select_category_prestation_admin.selectedIndex].value != 0) {

            window.location.href = "" + select_category_prestation_admin.options[select_category_prestation_admin.selectedIndex].value
        } else {
            window.location.href = "admin/prestations/0"
        }
    });
}
let slug_prestations_admin = location.pathname.split('admin/prestations/').slice(1);
if (slug_prestations_admin.length > 0) {
    select_category_prestation_admin.value = slug_prestations_admin;
};

menu_burger = document.querySelector(".div-logo-burger")
if (menu_burger != null) { // Evite l'incompatibilité entre les pages
    menu_burger.addEventListener('click', event => {
        if (document.querySelector(".div-burger").classList.contains("hidden-burger")) {
            document.querySelector(".div-burger").classList.remove("hidden-burger")
        } else {
            document.querySelector(".div-burger").classList.add("hidden-burger")
        }

    });
}

btn_ajout_prestation = document.querySelector("#ajout_prestation");
if (btn_ajout_prestation != null) { // Evite l'incompatibilité entre les pages
    // Fait en sorte que quand tu appuies sur le + en dessous de prestation cela rajoute un input afin de pouvoir mettre une autre prestations dans le même RDV ( max 3)
    btn_ajout_prestation.addEventListener('click', button => {
        button.preventDefault();

        document.querySelector(".hidden").classList.remove("hidden")

        document.querySelector(".hidden-div").classList.add("reservation-div-input")
        document.querySelector(".hidden-div").classList.remove("hidden-div")

        document.querySelector(".hidden_label").classList.remove("hidden_label")
    });
}


if (window.screen.width < 768) {
    if (document.getElementById("footer-logo_fb-img")) {
        document.getElementById("footer-logo_fb-img").src = "https://img.icons8.com/fluency/96/000000/facebook-new.png"
    }
    if (document.getElementById("footer-logo_fb-img")) {
        document.getElementById("footer-logo_fb-img").src = "https://img.icons8.com/fluency/96/000000/facebook-new.png"
    }
    if (document.querySelector("#admin-prestation-logo-edit")) {
        document.querySelectorAll('#admin-prestation-logo-edit').forEach(item => {
            item.src = "https://img.icons8.com/ios-glyphs/90/null/edit-property.png"
            console.log("tedqz");
        })
    }
    if (document.querySelector("#admin-prestation-logo-suppr")) {
        document.querySelectorAll('#admin-prestation-logo-suppr').forEach(item => {
            item.src = "https://img.icons8.com/ios-glyphs/90/null/delete-property.png"
            console.log("tedqz");
        })
    }
}
document.querySelector("#bijoux_url_image").addEventListener('change',event => {
console.log("CHANGE !!!!");
});
