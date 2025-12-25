// COMMENTAIRE : On attend que le HTML de la page soit entièrement chargé avant d'exécuter le script.
// C'est une bonne pratique pour éviter de chercher des éléments qui n'existent pas encore.
document.addEventListener('DOMContentLoaded', function() {
    
    // COMMENTAIRE : On essaie de récupérer le formulaire d'inscription par son ID
    const formInscription = document.getElementById('form-inscription');

    // COMMENTAIRE : Si le formulaire existe sur la page (pour ne pas faire d'erreur sur les autres pages)
    if (formInscription) {
        
        // COMMENTAIRE : On écoute l'événement "submit" (quand l'utilisateur clique sur "S'inscrire")
        formInscription.addEventListener('submit', function(e) {
            
            // COMMENTAIRE : Étape 1 : On nettoie. On supprime les anciens messages d'erreur affichés précédemment.
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            
            // On part du principe que le formulaire est valide (true). Si on trouve une erreur, on passera à false.
            let isValid = true;

            // COMMENTAIRE : Petite fonction utilitaire pour créer et afficher un message d'erreur rouge
            // Elle prend l'input concerné et le message à afficher.
            function displayError(inputElement, message) {
                const error = document.createElement('div');
                error.className = 'error-message alert alert-danger'; // Classes CSS pour le style rouge
                error.textContent = message;
                // Insère le message d'erreur juste APRÈS le champ input
                inputElement.parentNode.insertBefore(error, inputElement.nextSibling);
                isValid = false; // Il y a une erreur, donc le formulaire n'est plus valide
            }

            // ----------------------------------------------------
            // 1. Validation des champs obligatoires (Vides ou non ?)
            // ----------------------------------------------------
            const requiredFields = [
                { id: 'nom', name: 'Nom' },
                { id: 'prenom', name: 'Prénom' },
                { id: 'email', name: 'Email' },
                { id: 'formation', name: 'Choix de la formation' }
            ];

            // On boucle sur chaque champ obligatoire
            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
                // Si la valeur est vide (après avoir retiré les espaces inutiles avec trim())
                if (!input.value.trim()) {
                    displayError(input, `${field.name} est obligatoire.`);
                }
            });

            // ----------------------------------------------------
            // 2. Validation du format de l'email (Regex)
            // ----------------------------------------------------
            const emailInput = document.getElementById('email');
            // Regex : Expression régulière complexe pour vérifier qu'il y a bien "@" et "."
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            // Si l'email est rempli MAIS qu'il ne respecte pas le format, on affiche une erreur
            if (emailInput.value.trim() && !emailRegex.test(emailInput.value.trim())) {
                displayError(emailInput, 'Le format de l\'email est invalide.');
            }

            // ----------------------------------------------------
            // 3. Validation du format du téléphone (Optionnel)
            // ----------------------------------------------------
            const telInput = document.getElementById('tel');
            // Regex : Vérifie si c'est bien des chiffres, peut accepter +, -, espaces, parenthèses...
            const telRegex = /^\+?(\d{1,3})?[-.\s]?\(?\d{1,4}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/;
            
            // Si le téléphone est rempli (car il est optionnel) ET que le format est mauvais
            if (telInput.value.trim() && !telRegex.test(telInput.value.trim())) {
                displayError(telInput, 'Le format du numéro de téléphone est invalide.');
            }

            // ----------------------------------------------------
            // VERDICT FINAL
            // ----------------------------------------------------
            // Si isValid est devenu "false" à cause d'une erreur ci-dessus...
            if (!isValid) {
                e.preventDefault(); // ... ON BLOQUE l'envoi du formulaire (la page ne se recharge pas).
            }
        });
    }

    // ----------------------------------------------------
    // GESTION DES BOUTONS DE SUPPRESSION (Pour l'Admin)
    // ----------------------------------------------------
    // On sélectionne tous les boutons qui ont la classe "delete-confirm"
    document.querySelectorAll('.delete-confirm').forEach(button => {
        button.addEventListener('click', function(e) {
            // On ouvre une fenêtre de confirmation native du navigateur
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                // Si l'utilisateur clique sur "Annuler", on empêche le lien de se lancer
                e.preventDefault();
            }
            // Sinon (s'il clique sur OK), le lien s'exécute normalement et la suppression se fait.
        });
    });
});