// ── Gestion globale des confirmations ────────────────────────
document.addEventListener('submit', function(e) {
    const confirmMsg = e.target.getAttribute('data-confirm');
    if (confirmMsg && !confirm(confirmMsg)) {
        e.preventDefault();
    }
}, true);

document.addEventListener('click', function(e) {
    // Confirmation sur clic (boutons, liens)
    const btn = e.target.closest('[data-confirm]');
    if (btn && (e.target.tagName !== 'BUTTON' || (e.target.tagName === 'BUTTON' && e.target.type !== 'submit'))) {
        const confirmMsg = btn.getAttribute('data-confirm');
        if (!confirm(confirmMsg)) {
            e.preventDefault();
        }
    }
    
    // Redirection auto via data-href
    const redirectUrl = e.target.closest('[data-href]')?.getAttribute('data-href');
    if (redirectUrl) {
        window.location.href = redirectUrl;
    }
}, true);

const menuButton = document.getElementById('loginmenu');
const dropdownMenu = document.getElementById('dropdownMenu');

if (menuButton && dropdownMenu) {
    menuButton.addEventListener('click', () => {
        dropdownMenu.classList.toggle('show');
    });
}

const ResetPasswordLink = document.getElementById('resetPswd');
const zonePswd = document.getElementById('NewPswd');
const cacheLogin = document.getElementById('login');

// ── Création d'un compte ───────────────────────────────────

function checkFirstname() {
    const firstname = document.getElementById('firstName');
    const firstname_error = document.getElementById('firstName_error');
    if (!firstname) return 0;
    if (firstname.value == "") { firstname_error.classList.remove('titanic'); return 1; }
    else { firstname_error.classList.add('titanic'); return 0; }
}

function checkname() {
    const lastname = document.getElementById('lastName');
    const lastname_error = document.getElementById('lastName_error');
    if (!lastname) return 0;
    if (lastname.value == "") { lastname_error.classList.remove('titanic'); return 1; }
    else { lastname_error.classList.add('titanic'); return 0; }
}

function email_create() {
    const email = document.getElementById('email');
    const email_error = document.getElementById('email-creator_error');
    if (!email) return 0;
    if (email.value == "") { email_error.classList.remove('titanic'); return 1; }
    else { email_error.classList.add('titanic'); return 0; }
}

function password_create() {
    const passworD = document.getElementById('password');
    const passwor_error = document.getElementById('password_error');
    if (!passworD) return 0;
    if (passworD.value == "") { passwor_error.classList.remove('titanic'); return 1; }
    else { passwor_error.classList.add('titanic'); return 0; }
}

const formAccountCreate = document.getElementById('accountCreate');
if (formAccountCreate) {
    formAccountCreate.addEventListener('submit', function(event) {
        event.preventDefault();
        let nb_errors = checkFirstname() + checkname() + email_create() + password_create();
        if (nb_errors == 0) formAccountCreate.submit();
    });
}

// ── Formulaire de login ────────────────────────────────────

function checkLogin() {
    const login = document.getElementById('login-textzone');
    const login_error = document.getElementById('login_error');
    if (!login) return 0;
    if (login.value == "") { login_error.classList.remove('titanic'); return 1; }
    else { login_error.classList.add('titanic'); return 0; }
}

function checkPassword() {
    const password = document.getElementById('password');
    const password_error = document.getElementById('password_error');
    if (!password) return 0;
    if (password.value == "") { password_error.classList.remove('titanic'); return 1; }
    else { password_error.classList.add('titanic'); return 0; }
}

function checkNewPassword() {
    const newPassword = document.getElementById('nouveauPswd');
    const newPassword_error = document.getElementById('newPswd_error');
    if (!newPassword) return 0;
    if (newPassword.value == "") { newPassword_error.classList.remove('titanic'); return 1; }
    else { newPassword_error.classList.add('titanic'); return 0; }
}

function checkNewConfirmPassword() {
    const password = document.getElementById('confirmPswd');
    const password_error = document.getElementById('confirmPswd_error');
    if (!password) return 0;
    if (password.value == "") { password_error.classList.remove('titanic'); return 1; }
    else { password_error.classList.add('titanic'); return 0; }
}

if (ResetPasswordLink) {
    ResetPasswordLink.addEventListener('click', () => {
        if (zonePswd) zonePswd.classList.toggle('show');
        if (cacheLogin) cacheLogin.classList.add('titanic');
    });
}

const formLogin = document.getElementById('accountLogin');
if (formLogin) {
    formLogin.addEventListener('submit', function(event) {
        event.preventDefault();
        let nb_errors = 0;
        if (zonePswd && zonePswd.classList.contains('show')) {
            nb_errors = checkNewPassword() + checkNewConfirmPassword();
        } else {
            nb_errors = checkLogin() + checkPassword();
        }
        if (nb_errors == 0) formLogin.submit();
    });
}

// ── Espace administrateur ──────────────────────────────────

function checkUserName() {
    const UserName = document.getElementById('adminUserPrenom');
    const UserName_error = document.getElementById('adminUserPrenomError');
    if (!UserName) return 0;
    if (UserName.value == "") { UserName_error.classList.remove('titanic'); return 1; }
    else { UserName_error.classList.add('titanic'); return 0; }
}

function checkUserEmail() {
    const email = document.getElementById('adminUserEmail');
    const email_error = document.getElementById('adminUserEmailError');
    if (!email) return 0;
    if (email.value == "") { email_error.classList.remove('titanic'); return 1; }
    else { email_error.classList.add('titanic'); return 0; }
}

const formAdminUser = document.getElementById('formAdminUser');
if (formAdminUser) {
    formAdminUser.addEventListener('submit', function(event) {
        event.preventDefault();
        let nb_errors = checkUserName() + checkUserEmail();
        if (nb_errors == 0) formAdminUser.submit();
    });
}

function checkClientName() {
    const UserName = document.getElementById('adminClientName');
    const UserName_error = document.getElementById('adminClientNameError');
    if (!UserName) return 0;
    if (UserName.value == "") { UserName_error.classList.remove('titanic'); return 1; }
    else { UserName_error.classList.add('titanic'); return 0; }
}

function checkClientEmail() {
    const email = document.getElementById('adminClientEmail');
    const email_error = document.getElementById('adminClientEmailError');
    if (!email) return 0;
    if (email.value == "") { email_error.classList.remove('titanic'); return 1; }
    else { email_error.classList.add('titanic'); return 0; }
}

const formAdminClient = document.getElementById('formAdminClient');
if (formAdminClient) {
    formAdminClient.addEventListener('submit', function(event) {
        event.preventDefault();
        let nb_errors = checkClientName() + checkClientEmail();
        if (nb_errors == 0) formAdminClient.submit();
    });
}

function checkProjetName() {
    const UserName = document.getElementById('adminProjectName');
    const UserName_error = document.getElementById('adminProjectNameError');
    if (!UserName) return 0;
    if (UserName.value == "") { UserName_error.classList.remove('titanic'); return 1; }
    else { UserName_error.classList.add('titanic'); return 0; }
}

function checkProjetDescription() {
    const desc = document.getElementById('adminProjectDescription');
    const desc_error = document.getElementById('adminProjectDescriptionError');
    if (!desc) return 0;
    if (desc.value == "") { desc_error.classList.remove('titanic'); return 1; }
    else { desc_error.classList.add('titanic'); return 0; }
}

// ── Gestion des Modales ───────────────────────────────────

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Bloquer le défilement
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// ── API Ticket Creation ───────────────────────────────────

const btnSubmitTicket = document.getElementById('btnSubmitTicket');
const formApiCreateTicket = document.getElementById('formApiCreateTicket');

if (btnSubmitTicket && formApiCreateTicket) {
    btnSubmitTicket.addEventListener('click', async function() {
        const formData = new FormData(formApiCreateTicket);
        const data = Object.fromEntries(formData.entries());

        // Reset errors logic (if any)
        btnSubmitTicket.disabled = true;
        btnSubmitTicket.innerText = 'Création...';

        try {
            const response = await fetch('/api/v1/tickets', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // 1. Fermer la modale
                closeModal('modalCreateTicket');
                formApiCreateTicket.reset();

                // 2. Mise à jour dynamique du tableau
                updateTicketTable(result.ticket);

                // 3. Notification (facultatif si toast existant)
                alert('Ticket créé avec succès !');
            } else {
                alert('Erreur : ' + (result.message || 'Impossible de créer le ticket'));
            }
        } catch (error) {
            console.error('API Error:', error);
            alert('Une erreur réseau est survenue.');
        } finally {
            btnSubmitTicket.disabled = false;
            btnSubmitTicket.innerText = 'Créer le ticket';
        }
    });
}

function updateTicketTable(ticket) {
    const tableBody = document.querySelector('#tableTickets tbody');
    const noTicketsRow = document.getElementById('noTicketsRow');
    const ticketCount = document.getElementById('ticketCount');

    if (noTicketsRow) noTicketsRow.remove();

    const newRow = document.createElement('tr');
    newRow.className = 'Tickets-ligne';
    newRow.innerHTML = `
        <td>#${ticket.id}</td>
        <td class="bold">${ticket.nom}</td>
        <td><span class="fact-badge fact-badge--inclus">${ticket.status}</span></td>
        <td>${ticket.priorite || '-'}</td>
        <td>
            <a href="${ticket.url}">
                <button class="btn-add-comment small" type="button">Voir</button>
            </a>
        </td>
    `;

    // Animation d'entrée
    newRow.style.opacity = '0';
    newRow.style.transform = 'translateY(10px)';
    newRow.style.transition = 'all 0.4s ease';

    tableBody.prepend(newRow);

    // Déclencher l'animation
    setTimeout(() => {
        newRow.style.opacity = '1';
        newRow.style.transform = 'translateY(0)';
    }, 50);

    // Mettre à jour le compteur
    if (ticketCount) {
        ticketCount.innerText = parseInt(ticketCount.innerText) + 1;
    }
}

const formAdminProjet = document.getElementById('formAdminProject');
if (formAdminProjet) {
    formAdminProjet.addEventListener('submit', function(event) {
        event.preventDefault();
        let nb_errors = checkProjetName() + checkProjetDescription();
        if (nb_errors == 0) formAdminProjet.submit();
    });
}

// ── Filtres tableau tickets ────────────────────────────────
// ✅ CORRIGÉ : filtre par statut (colonne data-status)
const filtres = document.querySelectorAll(".filter-btn");
for (let i = 0; i < filtres.length; i++) {
    filtres[i].addEventListener("click", function(event) {
        event.preventDefault();
        const valeur = filtres[i].getAttribute('value') || '';
        const trs = document.querySelectorAll('.Tickets-table tbody tr');

        for (let j = 0; j < trs.length; j++) {
            const statusCell = trs[j].querySelector(".status-cell");
            if (!statusCell) continue;

            if (valeur === '') {
                trs[j].classList.remove('titanic');
            } else if (statusCell.innerText.toLowerCase().replace('-', ' ') !== valeur.toLowerCase().replace('-', ' ')) {
                trs[j].classList.add('titanic');
            } else {
                trs[j].classList.remove('titanic');
            }
        }
    });
}

// ✅ CORRIGÉ : filtre par priorité (colonne data-priority)
const filtresStatus = document.querySelectorAll(".filter-btn-Statut");
for (let i = 0; i < filtresStatus.length; i++) {
    filtresStatus[i].addEventListener("click", function(event) {
        event.preventDefault();
        const valeur = filtresStatus[i].innerText.toLowerCase();
        const trs = document.querySelectorAll('.Tickets-table tbody tr');

        for (let j = 0; j < trs.length; j++) {
            const priorityCell = trs[j].querySelector(".priority-cell");
            if (!priorityCell) continue;

            if (valeur === 'tous') {
                trs[j].classList.remove('titanic');
            } else if (priorityCell.innerText.toLowerCase() !== valeur) {
                trs[j].classList.add('titanic');
            } else {
                trs[j].classList.remove('titanic');
            }
        }
    });
}