// ============================================================
// assets/js/pse.js — Lógica del modal de pasarela PSE
// SprintTech LearningWithIA
// ============================================================

(function () {
  'use strict';

  /* ──────────────────────────────────────────
     Bancos disponibles
  ────────────────────────────────────────── */
  const BANKS = [
    {
      id: 'nequi',
      name: 'Nequi',
      type: 'Billetera digital',
      icon: 'N',
      cssClass: 'bank-nequi',
    },
    {
      id: 'bancolombia',
      name: 'Bancolombia',
      type: 'Banco · Ahorro / Corriente',
      icon: 'B',
      cssClass: 'bank-bancolombia',
    },
    {
      id: 'davivienda',
      name: 'Davivienda',
      type: 'Banco · Ahorro / Corriente',
      icon: 'D',
      cssClass: 'bank-davivienda',
    },
    {
      id: 'bogota',
      name: 'Banco de Bogotá',
      type: 'Banco · Ahorro / Corriente',
      icon: 'BB',
      cssClass: 'bank-bogota',
    },
    {
      id: 'bbva',
      name: 'BBVA Colombia',
      type: 'Banco · Ahorro / Corriente',
      icon: 'V',
      cssClass: 'bank-bbva',
    },
    {
      id: 'breb',
      name: 'Bre-B',
      type: 'Pago rápido interbancario',
      icon: '⚡',
      cssClass: 'bank-breb',
    },
  ];

  /* ──────────────────────────────────────────
     Estado del modal
  ────────────────────────────────────────── */
  let state = {
    step: 1,
    selectedBank: null,
    courseId: null,
    courseName: '',
    referencia: '',
    apiBase: '',
  };

  /* ──────────────────────────────────────────
     Inicialización
  ────────────────────────────────────────── */
  function init() {
    // Base URL inyectada por PHP vía window.PSE_BASE_URL
    state.apiBase = (window.PSE_BASE_URL || '').replace(/\/$/, '');

    injectModal();
    bindGlobalButtons();
  }

  /* ──────────────────────────────────────────
     Inyectar HTML del modal en el body
  ────────────────────────────────────────── */
  function injectModal() {
    if (document.getElementById('pse-overlay')) return;

    const html = `
    <div class="pse-overlay" id="pse-overlay" role="dialog" aria-modal="true" aria-labelledby="pse-dialog-title">

      <div class="pse-modal" id="pse-modal">

        <!-- HEADER -->
        <div class="pse-header">
          <div class="pse-header-left">
            <div class="pse-logo-wrap">
              <span>PSE</span>
            </div>
            <div class="pse-title-group">
              <h2 id="pse-dialog-title">Pasarela de Pago PSE</h2>
              <p>ACH Colombia · Pago seguro en línea</p>
            </div>
          </div>
          <button class="pse-close" id="pse-close-btn" aria-label="Cerrar">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- STEPS BAR -->
        <div class="pse-steps" id="pse-steps-bar">
          <div class="pse-step active" id="pse-step-ind-1">
            <div class="pse-step-num">1</div>
            <div class="pse-step-label">Seleccionar banco</div>
          </div>
          <div class="pse-step-line" id="pse-line-1"></div>
          <div class="pse-step" id="pse-step-ind-2">
            <div class="pse-step-num">2</div>
            <div class="pse-step-label">Tus datos</div>
          </div>
          <div class="pse-step-line" id="pse-line-2"></div>
          <div class="pse-step" id="pse-step-ind-3">
            <div class="pse-step-num">3</div>
            <div class="pse-step-label">Resultado</div>
          </div>
        </div>

        <!-- BODY -->
        <div class="pse-body">

          <!-- ══ PASO 1: Seleccionar banco ══ -->
          <div class="pse-step-content active" id="pse-content-1">

            <div class="pse-amount-box">
              <div>
                <div class="pse-amount-label">Total a pagar</div>
                <div class="pse-amount-value">$15.000 <span>COP</span></div>
              </div>
              <div class="pse-cert-name" id="pse-course-name-display">Certificado digital</div>
            </div>

            <div class="pse-section-title">Selecciona tu entidad bancaria</div>

            <div class="pse-banks-grid" id="pse-banks-grid">
              <!-- se genera por JS -->
            </div>

            <div class="pse-footer">
              <button class="pse-btn pse-btn-primary" id="pse-btn-step1" disabled>
                Continuar <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- ══ PASO 2: Datos del usuario ══ -->
          <div class="pse-step-content" id="pse-content-2">

            <div class="pse-banco-selected-badge" id="pse-selected-bank-badge">
              <!-- se llena dinámicamente -->
            </div>

            <form id="pse-data-form" novalidate>

              <div class="pse-form-group">
                <label class="pse-label" for="pse-tipo-persona">Tipo de persona</label>
                <select class="pse-select" id="pse-tipo-persona" name="tipo_persona">
                  <option value="natural">Persona Natural</option>
                  <option value="juridica">Persona Jurídica</option>
                </select>
              </div>

              <div class="pse-form-row">
                <div class="pse-form-group">
                  <label class="pse-label" for="pse-tipo-doc">Tipo de documento</label>
                  <select class="pse-select" id="pse-tipo-doc" name="tipo_doc">
                    <option value="">Seleccionar…</option>
                    <option value="CC">CC – Cédula de Ciudadanía</option>
                    <option value="CE">CE – Cédula de Extranjería</option>
                    <option value="NIT">NIT – Empresa</option>
                    <option value="PP">PP – Pasaporte</option>
                    <option value="TI">TI – Tarjeta de Identidad</option>
                  </select>
                </div>
                <div class="pse-form-group">
                  <label class="pse-label" for="pse-num-doc">Número de documento</label>
                  <input class="pse-input" type="text" id="pse-num-doc" name="numero_doc"
                         placeholder="Ej: 1012345678" maxlength="20" autocomplete="off">
                </div>
              </div>

              <div class="pse-form-group" style="margin-top:4px;">
                <label class="pse-label" for="pse-email-disp">Correo electrónico</label>
                <input class="pse-input" type="text" id="pse-email-disp"
                       placeholder="Se autocompletará con tu cuenta" disabled style="opacity:0.6;">
              </div>

            </form>

            <div class="pse-footer">
              <button class="pse-btn pse-btn-back" id="pse-btn-back-1">
                <i class="fa-solid fa-arrow-left"></i>
              </button>
              <button class="pse-btn pse-btn-primary" id="pse-btn-step2">
                Pagar $15.000 COP <i class="fa-solid fa-lock"></i>
              </button>
            </div>

          </div>

          <!-- ══ PASO 3: Procesando / Resultado ══ -->
          <div class="pse-step-content" id="pse-content-3">

            <!-- Estado: procesando -->
            <div class="pse-processing" id="pse-processing">
              <div class="pse-spinner-wrap">
                <div class="pse-spinner"></div>
                <div class="pse-spinner-inner"></div>
              </div>
              <div class="pse-processing-title">Procesando tu pago…</div>
              <div class="pse-processing-sub">Conectando con tu entidad bancaria.<br>No cierres esta ventana.</div>
              <div class="pse-processing-dots">
                <span></span><span></span><span></span>
              </div>
            </div>

            <!-- Estado: resultado -->
            <div class="pse-result" id="pse-result" style="display:none;">
              <div class="pse-result-icon" id="pse-result-icon"></div>
              <div class="pse-result-title" id="pse-result-title"></div>
              <div class="pse-result-sub" id="pse-result-sub"></div>
              <div class="pse-ref-box" id="pse-ref-box" style="display:none;">
                Referencia PSE: <strong id="pse-ref-num"></strong>
              </div>
              <div class="pse-footer" id="pse-result-actions"></div>
            </div>

          </div>

        </div><!-- /pse-body -->

        <!-- Seguridad -->
        <div class="pse-secure-strip">
          <div class="pse-secure-item"><i class="fa-solid fa-shield-halved"></i> Conexión SSL cifrada</div>
          <div class="pse-secure-item"><i class="fa-solid fa-check-circle"></i> Pago verificado por ACH Colombia</div>
          <div class="pse-secure-item"><i class="fa-solid fa-lock"></i> Datos protegidos</div>
        </div>

      </div><!-- /pse-modal -->
    </div><!-- /pse-overlay -->
    `;

    document.body.insertAdjacentHTML('beforeend', html);
    renderBanks();
    bindModalEvents();
  }

  /* ──────────────────────────────────────────
     Renderizar cards de bancos
  ────────────────────────────────────────── */
  function renderBanks() {
    const grid = document.getElementById('pse-banks-grid');
    if (!grid) return;
    grid.innerHTML = BANKS.map(bank => `
      <button class="pse-bank-card ${bank.cssClass}" data-bank="${bank.id}"
              type="button" id="bank-card-${bank.id}" aria-label="Seleccionar ${bank.name}">
        <div class="pse-bank-logo">${bank.icon}</div>
        <div class="pse-bank-info">
          <div class="pse-bank-name">${bank.name}</div>
          <div class="pse-bank-type">${bank.type}</div>
        </div>
        <div class="pse-bank-check"><i class="fa-solid fa-check"></i></div>
      </button>
    `).join('');
  }

  /* ──────────────────────────────────────────
     Bind eventos del modal
  ────────────────────────────────────────── */
  function bindModalEvents() {
    // Cerrar
    document.getElementById('pse-close-btn')
      .addEventListener('click', closeModal);

    // Click fuera del modal
    document.getElementById('pse-overlay').addEventListener('click', function (e) {
      if (e.target === this) closeModal();
    });

    // Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeModal();
    });

    // Selección de banco
    document.getElementById('pse-banks-grid').addEventListener('click', function (e) {
      const card = e.target.closest('.pse-bank-card');
      if (!card) return;
      // Deseleccionar todos
      document.querySelectorAll('.pse-bank-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      state.selectedBank = card.dataset.bank;
      document.getElementById('pse-btn-step1').disabled = false;
    });

    // Paso 1 → Paso 2
    document.getElementById('pse-btn-step1').addEventListener('click', function () {
      if (!state.selectedBank) return;
      fillStep2();
      goToStep(2);
    });

    // Paso 2: Volver
    document.getElementById('pse-btn-back-1').addEventListener('click', function () {
      goToStep(1);
    });

    // Paso 2: Pagar
    document.getElementById('pse-btn-step2').addEventListener('click', function () {
      if (!validateStep2()) return;
      goToStep(3);
      processPayment();
    });
  }

  /* ──────────────────────────────────────────
     Llenar badge del banco seleccionado en paso 2
  ────────────────────────────────────────── */
  function fillStep2() {
    const bank = BANKS.find(b => b.id === state.selectedBank);
    if (!bank) return;
    const badge = document.getElementById('pse-selected-bank-badge');
    badge.innerHTML = `
      <div class="mini-logo ${bank.cssClass}" style="background:var(--bank-accent, #00d4ff);">${bank.icon}</div>
      ${bank.name}
    `;
  }

  /* ──────────────────────────────────────────
     Validar formulario paso 2
  ────────────────────────────────────────── */
  function validateStep2() {
    const tipoDoc = document.getElementById('pse-tipo-doc').value;
    const numDoc  = document.getElementById('pse-num-doc').value.trim();
    const numField = document.getElementById('pse-num-doc');

    numField.style.borderColor = '';

    if (!tipoDoc) {
      showInlineError('Selecciona un tipo de documento.');
      return false;
    }
    if (numDoc.length < 5) {
      numField.style.borderColor = '#ef4444';
      numField.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
      showInlineError('Ingresa un número de documento válido.');
      return false;
    }
    return true;
  }

  /* ──────────────────────────────────────────
     Navegar al paso indicado
  ────────────────────────────────────────── */
  function goToStep(step) {
    // Ocultar todos los contenidos
    [1, 2, 3].forEach(n => {
      document.getElementById(`pse-content-${n}`).classList.toggle('active', n === step);
    });

    // Actualizar indicadores de pasos
    [1, 2, 3].forEach(n => {
      const ind = document.getElementById(`pse-step-ind-${n}`);
      ind.classList.remove('active', 'done');
      if (n < step)       ind.classList.add('done');
      else if (n === step) ind.classList.add('active');
    });

    // Líneas
    [1, 2].forEach(n => {
      const line = document.getElementById(`pse-line-${n}`);
      if (line) line.classList.toggle('done', n < step);
    });

    state.step = step;
  }

  /* ──────────────────────────────────────────
     Llamada AJAX para procesar el pago
  ────────────────────────────────────────── */
  function processPayment() {
    document.getElementById('pse-processing').style.display = 'block';
    document.getElementById('pse-result').style.display = 'none';

    const formData = new FormData();
    formData.append('course_id',    state.courseId);
    formData.append('banco',        state.selectedBank);
    formData.append('tipo_persona', document.getElementById('pse-tipo-persona').value);
    formData.append('tipo_doc',     document.getElementById('pse-tipo-doc').value);
    formData.append('numero_doc',   document.getElementById('pse-num-doc').value.trim());

    fetch(state.apiBase + '/payment/pse_process.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
    })
      .then(r => r.json())
      .then(data => showResult(data))
      .catch(() => showResult({ success: false, message: 'Error de conexión. Intenta de nuevo.' }));
  }

  /* ──────────────────────────────────────────
     Mostrar resultado del pago
  ────────────────────────────────────────── */
  function showResult(data) {
    document.getElementById('pse-processing').style.display = 'none';
    const resultDiv = document.getElementById('pse-result');
    resultDiv.style.display = 'block';

    const icon    = document.getElementById('pse-result-icon');
    const title   = document.getElementById('pse-result-title');
    const sub     = document.getElementById('pse-result-sub');
    const refBox  = document.getElementById('pse-ref-box');
    const actions = document.getElementById('pse-result-actions');

    if (data.success) {
      // ✅ Aprobado
      icon.className  = 'pse-result-icon success';
      icon.innerHTML  = '<i class="fa-solid fa-circle-check"></i>';
      title.textContent = '¡Pago aprobado!';
      sub.innerHTML   = `Tu pago de <strong>$15.000 COP</strong> fue procesado exitosamente.<br>
                         Ya puedes descargar tu certificado.`;

      refBox.style.display = 'block';
      document.getElementById('pse-ref-num').textContent = data.referencia || '';
      state.referencia = data.referencia || '';

      actions.innerHTML = `
        <button class="pse-btn pse-btn-success" id="pse-btn-download" style="width:100%;"
                onclick="window.open('${state.apiBase}/user/download_cert.php?course_id=${state.courseId}', '_blank')">
          <i class="fa-solid fa-download"></i> Descargar Certificado
        </button>
      `;

      // Marcar el botón de pago en la card como pagado
      markCourseAsPaid(state.courseId);

    } else {
      // ❌ Rechazado
      icon.className  = 'pse-result-icon error';
      icon.innerHTML  = '<i class="fa-solid fa-circle-xmark"></i>';
      title.textContent = 'Pago rechazado';
      sub.textContent   = data.message || data.error || 'La transacción no fue aprobada.';

      refBox.style.display = 'none';

      actions.innerHTML = `
        <button class="pse-btn pse-btn-back" onclick="pseFresh(${state.courseId}, '${escHtml(state.courseName)}')" style="flex:1;">
          <i class="fa-solid fa-rotate-left"></i> Reintentar
        </button>
        <button class="pse-btn pse-btn-primary" onclick="closePseModal()" style="flex:1;">
          Cerrar
        </button>
      `;
    }
  }

  /* ──────────────────────────────────────────
     Marcar curso como pagado en la UI
  ────────────────────────────────────────── */
  function markCourseAsPaid(courseId) {
    const btn = document.getElementById(`btn-pse-${courseId}`);
    if (btn) {
      btn.classList.add('paid');
      btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Certificado pagado';
      btn.disabled  = true;
      btn.onclick   = null;
    }
  }

  /* ──────────────────────────────────────────
     Abrir modal para un curso específico
  ────────────────────────────────────────── */
  function openPseModal(courseId, courseName) {
    state.courseId   = courseId;
    state.courseName = courseName;
    state.selectedBank = null;
    state.referencia   = '';
    state.step         = 1;

    // Reset UI
    document.querySelectorAll('.pse-bank-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('pse-btn-step1').disabled = true;
    document.getElementById('pse-tipo-doc').value  = '';
    document.getElementById('pse-num-doc').value   = '';
    document.getElementById('pse-num-doc').style.borderColor = '';
    document.getElementById('pse-num-doc').style.boxShadow   = '';
    document.getElementById('pse-processing').style.display = 'block';
    document.getElementById('pse-result').style.display     = 'none';
    document.getElementById('pse-result-actions').innerHTML = '';

    // Nombre del curso
    const nameDisp = document.getElementById('pse-course-name-display');
    if (nameDisp) nameDisp.textContent = 'Certificado: ' + (courseName || '');

    goToStep(1);

    const overlay = document.getElementById('pse-overlay');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  /* ──────────────────────────────────────────
     Cerrar modal
  ────────────────────────────────────────── */
  function closeModal() {
    const overlay = document.getElementById('pse-overlay');
    if (!overlay) return;
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  /* ──────────────────────────────────────────
     Reiniciar modal desde cero
  ────────────────────────────────────────── */
  function pseFresh(courseId, courseName) {
    openPseModal(courseId, courseName);
  }

  /* ──────────────────────────────────────────
     Bind botones "Realizar Pago" en la página
  ────────────────────────────────────────── */
  function bindGlobalButtons() {
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-pse-open]');
      if (!btn || btn.disabled) return;
      const courseId   = btn.dataset.courseId   || btn.dataset.pseOpen;
      const courseName = btn.dataset.courseName || '';
      openPseModal(courseId, courseName);
    });
  }

  /* ──────────────────────────────────────────
     Mostrar error inline en paso 2
  ────────────────────────────────────────── */
  function showInlineError(msg) {
    let err = document.getElementById('pse-inline-error');
    if (!err) {
      err = document.createElement('div');
      err.id = 'pse-inline-error';
      err.style.cssText = 'color:#ef4444;font-size:0.82rem;margin-top:-4px;margin-bottom:10px;';
      document.getElementById('pse-btn-step2').before(err);
    }
    err.textContent = msg;
    setTimeout(() => { if (err) err.textContent = ''; }, 3500);
  }

  /* ──────────────────────────────────────────
     Helper: escapar HTML
  ────────────────────────────────────────── */
  function escHtml(str) {
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
  }

  /* ──────────────────────────────────────────
     Exponer funciones globales necesarias
  ────────────────────────────────────────── */
  window.openPseModal  = openPseModal;
  window.closePseModal = closeModal;
  window.pseFresh      = pseFresh;

  /* ── Inicializar cuando el DOM esté listo ── */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
