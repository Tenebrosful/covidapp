// Importation Bootstrap
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

// Importation jQuery pour utilisation dans ce fichier
import $ from 'jquery';

/* Pour groupes.html */

// Rajout de comportement à la liste de choix pour rajout de groupes
$('.rajoutmodificationmodals').each(function() {
    $(this).find('.list-group-item-action').on('click', function() {
        if ($(this).find('#groupChecked').text() === '✔')
            $(this).find('#groupChecked').text('');
        else
            $(this).find('#groupChecked').text('✔');
    });
});
// Lit quelles groupes on été choisies au submit du formulaire
$('.rajoutmodificationmodals').each(function() {
    $(this).find('form').on('submit', function(e) {
        let checkedUsers = [];
        $(this).parent().parent().find('.list-group-item-action').each(function() {
            if ($(this).find('#groupChecked').text() === '✔')
                checkedUsers.push($(this).find('#groupId').text());
        });
        checkedUsers.push($(this).find("[name='users']").val());
        $(this).find("[name='users']").val(checkedUsers.join());
    });
});
// On prépare le modal pour modifier les groupes en fonction du groupe choisie
$('.buttonmodificationgroupes').each(function() {
    $(this).on('click', function(event) {
        // Définition de la fonction pour récupérer les données en JSON
        function requeteRecuperation(groupid) {
            return new Promise((resolve, reject) => {
                fetch("/group/"+groupid).then((response) => {
                    if (response.ok)
                        return response.json();
                    else
                        reject(response.statusText);
                }).then((responsejson) => {
                    resolve(responsejson);
                }).catch((error) => {
                    reject(error);
                });
            });
        }
        // Appel à cette fonction
        requeteRecuperation($(this).data('groupid')).then((resolve) => {
            // On met le titre
            $('#modificationGroupesModalLabel').html("Modification du groupe <i>" + resolve[0].nom + "</i>");
            // On décoche tout
            $('#modificationGroupesModal').find('#groupChecked').each(function() {
                $(this).text('');
            });
            // Puis on coche uniquement les utilisateurs qui appartiennent déjà au groupe
            resolve[1].forEach((utilisateur) => {
                // Recherche dans la liste et si trouvé on coche
                $('#modificationGroupesModal').find('.list-group-item-action').each(function() {
                    if ($(this).find('#groupId').text() == utilisateur.id)
                        $(this).find('#groupChecked').text('✔');
                });
            });
            // On remplit le l'id et le nom dans le form
            $('#modificationGroupesModal').find('#inputGroupId').val(resolve[0].id);
            $('#modificationGroupesModal').find('#inputGroupTitle').val(resolve[0].nom);
            $('#modificationGroupesModal').modal('show');
        }).catch((reject) => {
            console.error(reject);
        });
    });
});
