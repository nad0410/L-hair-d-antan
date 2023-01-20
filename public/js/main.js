document.querySelectorAll('#comf_delete').forEach(item => {
    item.addEventListener('click', event => {
        let text = "Voulez-vous vraiment supprimer ce produits ?";
        if (confirm(text) == true) {
        } else {
            event.preventDefault();
        }
    })
})

btn_ajout_prestation = document.querySelector("#ajout_prestation");
if (btn_ajout_prestation != null) {
    // Fait en sorte que quand tu appuies sur le + en dessous de prestation cela rajoute un input afin de pouvoir mettre plusieurs prestations dans le même RDV
    btn_ajout_prestation.addEventListener('click', button => {
        button.preventDefault();
        let presta = 1;

        if (presta == 1) {
            presta = presta + 1
            console.log(presta);
        }
        document.querySelector(".hidden").classList.remove("hidden")
        document.querySelector(".hidden_label").classList.remove("hidden_label")
        console.log("Salut");
    }); // CODE A REMETTRE EN BAS A LA FIN CAR NE MARCHE PLUS
}

select_category_produits = document.querySelector("#choice_category");
if (select_category_produits != null) {
    // Fait en sorte que quand tu changes d'option dans le sélecteur afin de choisir la catégorie que tu veux ça change de page en rajoutant /category/[id de la category]
    select_category_produits.addEventListener('change', event => {
        if (select_category_produits.options[select_category_produits.selectedIndex].value != 0) {

            window.location.href = "/produits/category/" + select_category_produits.options[select_category_produits.selectedIndex].value
        } else {
            window.location.href = "/produits/"
        }
        console.log("COUCOU");
    });
}

let slug = location.pathname.split('category/').slice(1);

if (slug.length > 0) {
    select_category_produits.value = slug;
};