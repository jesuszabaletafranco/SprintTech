<?php
// ============================================================
// includes/footer.php
// ============================================================
require_once __DIR__ . '/../config/session.php';
$basePath = baseUrl();
?>
</main>

<!-- ═══════════════════ FOOTER ═══════════════════ -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-grid">

      <!-- Brand -->
      <div class="footer-brand">
        <a href="<?= $basePath ?>/" class="footer-logo">
          <i class="fa-solid fa-bolt-lightning"></i>
          Sprint<span>Tech</span>
        </a>
        <p class="footer-tagline">Aprende. Compite. Gana.</p>
        <p class="footer-desc">Plataforma de aprendizaje interactivo en Tecnologías 4.0. Capacítate con los skills más demandados del futuro.</p>
        <div class="footer-social">
          <a href="#" aria-label="Twitter" class="social-link"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="#" aria-label="LinkedIn" class="social-link"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="#" aria-label="YouTube" class="social-link"><i class="fa-brands fa-youtube"></i></a>
          <a href="#" aria-label="Instagram" class="social-link"><i class="fa-brands fa-instagram"></i></a>
        </div>
      </div>

      <!-- Cursos -->
      <div class="footer-col">
        <h4 class="footer-heading">Cursos</h4>
        <ul class="footer-links">
          <li><a href="<?= $basePath ?>/courses/detail.php?id=1">IA Generativa</a></li>
          <li><a href="<?= $basePath ?>/courses/detail.php?id=2">Agentes de IA</a></li>
          <li><a href="<?= $basePath ?>/courses/detail.php?id=3">Visión y NLP</a></li>
          <li><a href="<?= $basePath ?>/courses/detail.php?id=4">Flutter & React Native</a></li>
          <li><a href="<?= $basePath ?>/courses/detail.php?id=5">PWA y Edge AI</a></li>
          <li><a href="<?= $basePath ?>/courses/detail.php?id=6">IoT Industrial</a></li>
        </ul>
      </div>

      <!-- Plataforma -->
      <div class="footer-col">
        <h4 class="footer-heading">Plataforma</h4>
        <ul class="footer-links">
          <li><a href="<?= $basePath ?>/">Inicio</a></li>
          <li><a href="<?= $basePath ?>/courses/index.php">Todos los Cursos</a></li>
          <?php if (isLoggedIn()): ?>
            <li><a href="<?= $basePath ?>/user/my_courses.php">Mis Cursos</a></li>
            <li><a href="<?= $basePath ?>/user/certifications.php">Mis Certificaciones</a></li>
          <?php else: ?>
            <li><a href="<?= $basePath ?>/auth/login.php">Iniciar Sesión</a></li>
            <li><a href="<?= $basePath ?>/auth/register.php">Registrarse</a></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Metodología -->
      <div class="footer-col">
        <h4 class="footer-heading">Metodología ABR</h4>
        <div class="footer-abr">
          <div class="abr-step"><span class="abr-icon">🎯</span> Problema real</div>
          <div class="abr-step"><span class="abr-icon">⚙️</span> Uso de tecnología</div>
          <div class="abr-step"><span class="abr-icon">🔄</span> Prueba y error</div>
          <div class="abr-step"><span class="abr-icon">✅</span> Solución</div>
          <div class="abr-step"><span class="abr-icon">💡</span> Reflexión</div>
        </div>
      </div>

    </div>

    <!-- Bottom bar -->
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> SprintTech LearningWithIA. Todos los derechos reservados.</p>
      <div class="footer-bottom-links">
        <a href="#">Privacidad</a>
        <a href="#">Términos</a>
        <a href="#">Soporte</a>
      </div>
    </div>
  </div>
</footer>

<!-- JS Principal -->
<script src="<?= $basePath ?>/assets/js/main.js"></script>
</body>
</html>
