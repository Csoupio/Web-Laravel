@extends('layouts.app')

@section('title', $projet['Nom'])
@section('header-title', 'Détail du projet')

@section('content')
<div class="corp">

    {{-- Infos projet --}}
    <div class="left-panel">
        <div class="tuile">
            <p class="titre">{{ $projet['Nom'] }}</p>
            <p style="color:#666; font-size:13px;">
                Client : <strong>{{ $projet['clientNom'] }}</strong>
            </p>
            <p style="margin-top:12px;">{{ $projet['Description'] ?? 'Aucune description.' }}</p>
        </div>

        {{-- Liste des tickets du projet --}}
        <div class="tuile">
            <h3>Tickets du projet</h3>
            <div class="table-wrapper">
                <table class="Tickets-table" id="tickets-table">
                    <thead class="Tickets-head">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-tbody">
                        @forelse($tickets as $ticket)
                            <tr class="Tickets-ligne">
                                <td>{{ $ticket['ID'] }}</td>
                                <td>{{ $ticket['Nom'] }}</td>
                                <td>{{ $ticket['Status'] }}</td>
                                <td>{{ $ticket['Priorité'] ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket['ID']) }}">
                                        <button class="btn-add-comment" type="button">Voir</button>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="5" style="text-align:center; padding:20px;">
                                    Aucun ticket pour ce projet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Panneau droit --}}
    <div class="right-panel">
        <div class="tuile">
            <p class="titre">Informations</p>
            <p><strong>ID projet :</strong> {{ $projet['ID'] }}</p>
            <p><strong>Client :</strong> {{ $projet['clientNom'] }}</p>
            <p><strong>Tickets :</strong> <span id="ticket-count">{{ count($tickets) }}</span></p>
        </div>
        <div class="collaborateur tuile">
            <p class="titre">Collaborateurs :</p>
            <div class="avatar-stack">
                <div class="avatar" title="?">?</div>
            </div>
        </div>

        {{-- Bouton ouvre la modale --}}
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button type="button" class="btn-add-comment" id="open-modal-btn">
                + Nouveau ticket
            </button>
        </div>

        <a href="{{ route('projets.index') }}">
            <button type="button" class="btn-staff">← Retour aux projets</button>
        </a>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODALE — Créer un ticket via API (fetch)
════════════════════════════════════════════════ --}}
<div id="ticket-modal" style="display:none;">
    <div id="ticket-modal-overlay"></div>
    <div id="ticket-modal-box">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">Nouveau ticket</h3>
            <button id="close-modal-btn" style="background:none;border:none;font-size:22px;cursor:pointer;">✕</button>
        </div>

        <p style="color:#666; font-size:13px; margin-bottom:16px;">
            Projet : <strong>{{ $projet['Nom'] }}</strong>
        </p>

        {{-- Message d'erreur / succès --}}
        <div id="modal-alert" style="display:none; padding:10px; border-radius:8px; margin-bottom:12px; font-size:14px;"></div>

        <div class="form-row">
            <label for="m-title">Titre <span style="color:red">*</span></label>
            <input class="textzone" type="text" id="m-title" placeholder="Ex : Bug sur la page d'accueil">
            <div id="m-title-error" class="error-text titanic">Veuillez renseigner un titre.</div>
        </div>

        <div class="form-row">
            <label for="m-description">Description</label>
            <textarea class="textzone" id="m-description" rows="4"
                      placeholder="Décrivez le problème ou la fonctionnalité..."
                      style="height:auto; min-height:90px; resize:vertical;"></textarea>
        </div>

        <div class="form-row">
            <label for="m-priority">Priorité <span style="color:red">*</span></label>
            <select class="textzone" id="m-priority">
                <option value="">-- Choisir --</option>
                <option value="Haute">Haute</option>
                <option value="Moyenne">Moyenne</option>
                <option value="Basse">Basse</option>
            </select>
            <div id="m-priority-error" class="error-text titanic">Veuillez choisir une priorité.</div>
        </div>

        <div class="form-row">
            <label for="m-type">Type <span style="color:red">*</span></label>
            <select class="textzone" id="m-type">
                <option value="">-- Choisir --</option>
                <option value="Bug">Bug</option>
                <option value="Évolution">Évolution</option>
                <option value="Support">Support</option>
            </select>
            <div id="m-type-error" class="error-text titanic">Veuillez choisir un type.</div>
        </div>

        <div class="form-row">
            <label for="m-time">Temps estimé (heures)</label>
            <input class="textzone" type="number" id="m-time" placeholder="Ex : 3" min="0" step="0.5">
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:16px;">
            <button type="button" class="btn-c" id="cancel-modal-btn">Annuler</button>
            <button type="button" class="btn-add-comment" id="submit-ticket-btn">
                <span id="submit-label">Créer le ticket</span>
                <span id="submit-spinner" style="display:none;">⏳ Envoi…</span>
            </button>
        </div>
    </div>
</div>

<style>
/* ── Modale ── */
#ticket-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 900;
}
#ticket-modal-box {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    border-radius: 14px;
    padding: 28px;
    width: min(540px, 94vw);
    max-height: 90vh;
    overflow-y: auto;
    z-index: 901;
    box-shadow: 0 16px 48px rgba(0,0,0,.22);
}
#ticket-modal-box .form-row { margin-bottom: 14px; }
#ticket-modal-box label { font-weight: 700; margin-bottom: 6px; display:block; }
#ticket-modal-box .textzone { width: 100%; box-sizing: border-box; }

/* ── Ligne ajoutée dynamiquement ── */
.new-ticket-row {
    animation: slideIn .35s ease;
}
@keyframes slideIn {
    from { opacity:0; transform:translateY(-8px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
(function () {
    const PROJET_ID = {{ $projet['ID'] }};

    /* ── Éléments DOM ── */
    const modal         = document.getElementById('ticket-modal');
    const openBtn       = document.getElementById('open-modal-btn');
    const closeBtn      = document.getElementById('close-modal-btn');
    const cancelBtn     = document.getElementById('cancel-modal-btn');
    const submitBtn     = document.getElementById('submit-ticket-btn');
    const tbody         = document.getElementById('tickets-tbody');
    const countEl       = document.getElementById('ticket-count');
    const alertEl       = document.getElementById('modal-alert');

    /* ── Ouvrir / fermer la modale ── */
    function openModal()  {
        modal.style.display = 'block';
        document.getElementById('m-title').focus();
        clearForm();
    }
    function closeModal() {
        modal.style.display = 'none';
        clearForm();
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    document.getElementById('ticket-modal-overlay').addEventListener('click', closeModal);

    /* ── Reset formulaire ── */
    function clearForm() {
        ['m-title','m-description','m-time'].forEach(id => {
            document.getElementById(id).value = '';
        });
        ['m-priority','m-type'].forEach(id => {
            document.getElementById(id).value = '';
        });
        ['m-title-error','m-priority-error','m-type-error'].forEach(id => {
            document.getElementById(id).classList.add('titanic');
        });
        hideAlert();
    }

    /* ── Validation ── */
    function validate() {
        let ok = true;
        const title    = document.getElementById('m-title').value.trim();
        const priority = document.getElementById('m-priority').value;
        const type     = document.getElementById('m-type').value;

        const showErr = (id, show) =>
            document.getElementById(id).classList.toggle('titanic', !show);

        showErr('m-title-error',    title === '');
        showErr('m-priority-error', priority === '');
        showErr('m-type-error',     type === '');

        if (!title || !priority || !type) ok = false;
        return ok;
    }

    /* ── Alertes ── */
    function showAlert(msg, type) {
        alertEl.textContent = msg;
        alertEl.style.background = type === 'error' ? '#ffe7df' : '#d4edda';
        alertEl.style.color      = type === 'error' ? '#b23b2c' : '#155724';
        alertEl.style.border     = type === 'error' ? '1px solid #f2c2b8' : '1px solid #c3e6cb';
        alertEl.style.display    = 'block';
    }
    function hideAlert() { alertEl.style.display = 'none'; }

    /* ── Ajouter une ligne dans le tableau ── */
    function appendTicketRow(ticket) {
        /* Supprimer la ligne "Aucun ticket" si présente */
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.className = 'Tickets-ligne new-ticket-row';
        tr.innerHTML = `
            <td>${ticket.ID}</td>
            <td>${escHtml(ticket.Nom)}</td>
            <td>${escHtml(ticket.Status)}</td>
            <td>${escHtml(ticket['Priorité'] ?? '-')}</td>
            <td>
                <a href="/tickets/${ticket.ID}">
                    <button class="btn-add-comment" type="button">Voir</button>
                </a>
            </td>
        `;
        tbody.insertBefore(tr, tbody.firstChild);

        /* Incrémenter le compteur */
        if (countEl) countEl.textContent = parseInt(countEl.textContent || '0') + 1;
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;');
    }

    /* ── Soumission via fetch ── */
    submitBtn.addEventListener('click', async function () {
        if (!validate()) return;

        /* Spinner */
        document.getElementById('submit-label').style.display  = 'none';
        document.getElementById('submit-spinner').style.display = 'inline';
        submitBtn.disabled = true;
        hideAlert();

        const payload = {
            title:          document.getElementById('m-title').value.trim(),
            description:    document.getElementById('m-description').value.trim(),
            project:        PROJET_ID,
            priority:       document.getElementById('m-priority').value,
            type:           document.getElementById('m-type').value,
            estimated_time: document.getElementById('m-time').value || 0,
        };

        try {
            const res = await fetch('/api/v1/tickets', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body:    JSON.stringify(payload),
            });

            const json = await res.json();

            if (!res.ok) {
                /* Erreurs de validation Laravel */
                const firstError = json.errors
                    ? Object.values(json.errors)[0][0]
                    : (json.message ?? 'Une erreur est survenue.');
                showAlert(firstError, 'error');
                return;
            }

            /* ✅ Succès */
            showAlert('Ticket créé avec succès !', 'success');
            appendTicketRow(json.data);

            /* Fermer la modale après 1 s */
            setTimeout(closeModal, 1000);

        } catch (err) {
            showAlert('Impossible de contacter le serveur. Réessayez.', 'error');
        } finally {
            document.getElementById('submit-label').style.display  = 'inline';
            document.getElementById('submit-spinner').style.display = 'none';
            submitBtn.disabled = false;
        }
    });
})();
</script>
@endsection