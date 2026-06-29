<?php
/**
 * _footer.php
 * Rodapé do site
 */
?>
<footer class="footer">
  <div class="container footer__top">
    <div class="footer__brand">
      <a href="index.php" class="logo">Folha<span>Digital</span></a>
      <p>Jornalismo independente, rápido e confiável. Notícias de política, economia, esportes, cultura e tecnologia, todos os dias.</p>
      <div class="footer__social">
        <a href="#" aria-label="Facebook">
          <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
        </a>
        <a href="#" aria-label="Instagram">
          <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1"></circle></svg>
        </a>
        <a href="#" aria-label="Twitter/X">
          <svg viewBox="0 0 24 24"><path d="M22 4s-.7 2-2 3.5C21.5 16 13 21 4 17c2.5-.5 4-2 4-2-2.5-1-4-3.5-4-6.5 1 .5 2 .5 3 0C5 7 4.5 4 6 2c1.5 2.5 4 4 7 4-.2-3 3-5 6-3-1.5 1-2 2-2 2 1.5 0 3-.5 5-1z"></path></svg>
        </a>
      </div>
    </div>

    <div class="footer__col">
      <h4>Editorias</h4>
      <ul>
        <li><a href="noticias.php?categoria=politica">Política</a></li>
        <li><a href="noticias.php?categoria=economia">Economia</a></li>
        <li><a href="noticias.php?categoria=esportes">Esportes</a></li>
        <li><a href="noticias.php?categoria=cultura">Cultura</a></li>
        <li><a href="noticias.php?categoria=tecnologia">Tecnologia</a></li>
      </ul>
    </div>

    <div class="footer__col">
      <h4>Institucional</h4>
      <ul>
        <li><a href="#">Sobre nós</a></li>
        <li><a href="#">Anuncie</a></li>
        <li><a href="#">Termos de uso</a></li>
        <li><a href="#">Política de privacidade</a></li>
      </ul>
    </div>

    <div class="footer__col">
      <h4>Conta</h4>
      <ul>
        <li><a href="login.php">Entrar</a></li>
        <li><a href="cadastro.php">Cadastre-se</a></li>
      </ul>
    </div>
  </div>

  <div class="container footer__bottom">
    <span>&copy; <?= date('Y') ?> Folha Digital. Todos os direitos reservados.</span>
    <span>Feito com <i>&hearts;</i> no Brasil.</span>
  </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="Voltar ao topo">
  <svg viewBox="0 0 24 24"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
</button>

<script>
  // Menu mobile
  const menuToggle = document.getElementById('menuToggle');
  const navMobile = document.getElementById('navMobile');
  if (menuToggle && navMobile) {
    menuToggle.addEventListener('click', () => {
      const isOpen = navMobile.classList.toggle('is-open');
      menuToggle.classList.toggle('is-open', isOpen);
      menuToggle.setAttribute('aria-expanded', isOpen);
    });
  }

  // Botão voltar ao topo
  const backToTop = document.getElementById('backToTop');
  if (backToTop) {
    window.addEventListener('scroll', () => {
      backToTop.classList.toggle('is-visible', window.scrollY > 400);
    });
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
</script>