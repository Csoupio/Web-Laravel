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

// ── Formulaire création de ticket ──────────────────────────

function checkTitle() {
    const title = document.getElementById('title');
    const errorTitle = document.getElementById('title_error');
    if (!title) return 0;
    if (title.value == "") { errorTitle.classList.remove('titanic'); return 1; }
    else { errorTitle.classList.add('titanic'); return 0; }
}

function checkDesc() {
    const description = document.getElementById('description');
    const errorDesc = document.getElementById('description_error');
    if (!description) return 0;
    if (description.value == "") { errorDesc.classList.remove('titanic'); return 1; }
    else { errorDesc.classList.add('titanic'); return 0; }
}

function checkProject() {
    const project = document.getElementById('project');
    const errorProject = document.getElementById('project_error');
    if (!project) return 0;
    if (project.value == "") { errorProject.classList.remove('titanic'); return 1; }
    else { errorProject.classList.add('titanic'); return 0; }
}

function checkPriority() {
    const priority = document.getElementById('priority');
    const errorPriority = document.getElementById('priority_error');
    if (!priority) return 0;
    if (priority.value == "") { errorPriority.classList.remove('titanic'); return 1; }
    else { errorPriority.classList.add('titanic'); return 0; }
}

function checkType() {
    const type = document.getElementById('type');
    const errorType = document.getElementById('type_error');
    if (!type) return 0;
    if (type.value == "") { errorType.classList.remove('titanic'); return 1; }
    else { errorType.classList.add('titanic'); return 0; }
}

function checkTime() {
    const time = document.getElementById('estimated_time');
    const errorTime = document.getElementById('estimated_time_error');
    if (!time) return 0;
    if (time.value === "" || parseFloat(time.value) <= 0) { errorTime.classList.remove('titanic'); return 1; }
    else { errorTime.classList.add('titanic'); return 0; }
}

const formTicket = document.getElementById('ticketForm');
if (formTicket) {
    formTicket.addEventListener('submit', function(event) {
        event.preventDefault();
        let nb_errors = checkTitle() + checkDesc() + checkProject() + checkPriority() + checkType() + checkTime();
        if (nb_errors == 0) formTicket.submit();
    });
}

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