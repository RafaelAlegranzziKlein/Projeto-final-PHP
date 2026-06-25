<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Folha Digital — Portal de Notícias</title>
  <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

  <!-- Barra superior -->
  <div class="topbar">
    <div class="container">
      <span class="topbar__date">Quinta-feira, 25 de Junho de 2026</span>
      <div class="topbar__links">
        <a href="#">Entrar</a>
        <a href="#">Assine</a>
      </div>
    </div>
  </div>

  <!-- Cabeçalho -->
  <header class="header">
    <div class="container header__inner">
      <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>

      <a href="#" class="logo">Folha<span>Digital</span></a>

      <nav class="nav" aria-label="Navegação principal">
        <ul class="nav__list">
          <li><a href="#" class="nav__link is-active">Início</a></li>
          <li><a href="#" class="nav__link">Política</a></li>
          <li><a href="#" class="nav__link">Economia</a></li>
          <li><a href="#" class="nav__link">Esportes</a></li>
          <li><a href="#" class="nav__link">Cultura</a></li>
          <li><a href="#" class="nav__link">Tecnologia</a></li>
        </ul>
      </nav>

      <div class="header__actions">
        <button class="icon-btn" aria-label="Buscar">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
      </div>
    </div>
  </header>

  <!-- Navegação mobile (off-canvas) -->
  <nav class="nav--mobile" aria-label="Navegação mobile">
    <ul class="nav__list">
      <li><a href="#" class="nav__link is-active">Início</a></li>
      <li><a href="#" class="nav__link">Política</a></li>
      <li><a href="#" class="nav__link">Economia</a></li>
      <li><a href="#" class="nav__link">Esportes</a></li>
      <li><a href="#" class="nav__link">Cultura</a></li>
      <li><a href="#" class="nav__link">Tecnologia</a></li>
    </ul>
  </nav>

  <!-- Faixa de últimas notícias -->
  <div class="breaking">
    <div class="container">
      <span class="breaking__label">Urgente</span>
      <div class="breaking__ticker">
        <p>Congresso aprova nova lei de infraestrutura digital &nbsp;&nbsp;•&nbsp;&nbsp; Banco Central mantém taxa de juros &nbsp;&nbsp;•&nbsp;&nbsp; Seleção se prepara para amistoso internacional</p>
      </div>
    </div>
  </div>

  <!-- Hero -->
  <section class="hero container">
    <div class="hero__grid">
      <a href="#" class="hero__main">
        <img src="https://images.unsplash.com/photo-1495020689067-958852a7765e?w=900&h=560&fit=crop" alt="Notícia principal">
        <div class="hero__overlay">
          <span class="badge badge--politica">Política</span>
          <h1 class="hero__title">Governo anuncia novo pacote de investimentos em infraestrutura para 2027</h1>
          <p class="hero__excerpt">Medida prevê recursos para rodovias, portos e redes de energia em todo o país, com previsão de início das obras já no próximo semestre.</p>
          <div class="hero__meta">Por Ana Ribeiro · há 2 horas</div>
        </div>
      </a>

      <div class="hero__side">
        <a href="#" class="hero__side-item">
          <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=200&h=140&fit=crop" alt="">
          <div>
            <h3>Mercado financeiro reage a dados de inflação</h3>
            <span class="meta">Economia · há 1h</span>
          </div>
        </a>
        <a href="#" class="hero__side-item">
          <img src="https://images.unsplash.com/photo-1517649763962-0c623066013b?w=200&h=140&fit=crop" alt="">
          <div>
            <h3>Time nacional vence final do campeonato</h3>
            <span class="meta">Esportes · há 3h</span>
          </div>
        </a>
        <a href="#" class="hero__side-item">
          <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=200&h=140&fit=crop" alt="">
          <div>
            <h3>Festival de cinema anuncia line-up de 2026</h3>
            <span class="meta">Cultura · há 5h</span>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- Conteúdo principal -->
  <main class="main-content container">
    <div class="content-grid">

      <!-- Coluna de artigos -->
      <div>
        <section>
          <div class="section-title">
            <h2>Últimas notícias</h2>
            <a href="#">Ver todas →</a>
          </div>

          <div class="news-grid">
            <article class="card">
              <div class="card__image">
                <span class="badge badge--tecnologia">Tecnologia</span>
                <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=400&h=240&fit=crop" alt="">
              </div>
              <div class="card__body">
                <h3 class="card__title"><a href="#">Empresas nacionais investem em inteligência artificial</a></h3>
                <p class="card__excerpt">Setor de tecnologia projeta crescimento de dois dígitos para o próximo ano fiscal.</p>
                <div class="card__meta">há 4h · 5 min de leitura</div>
              </div>
            </article>

            <article class="card">
              <div class="card__image">
                <span class="badge badge--economia">Economia</span>
                <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=400&h=240&fit=crop" alt="">
              </div>
              <div class="card__body">
                <h3 class="card__title"><a href="#">Exportações batem recorde no primeiro semestre</a></h3>
                <p class="card__excerpt">Balança comercial registra superávit acima das expectativas de analistas.</p>
                <div class="card__meta">há 6h · 4 min de leitura</div>
              </div>
            </article>

            <article class="card">
              <div class="card__image">
                <span class="badge badge--esportes">Esportes</span>
                <img src="https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?w=400&h=240&fit=crop" alt="">
              </div>
              <div class="card__body">
                <h3 class="card__title"><a href="#">Atleta brasileira é destaque em competição internacional</a></h3>
                <p class="card__excerpt">Resultado garante vaga inédita para a próxima fase do torneio mundial.</p>
                <div class="card__meta">há 8h · 3 min de leitura</div>
              </div>
            </article>

            <article class="card">
              <div class="card__image">
                <span class="badge badge--cultura">Cultura</span>
                <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=400&h=240&fit=crop" alt="">
              </div>
              <div class="card__body">
                <h3 class="card__title"><a href="#">Exposição reúne obras inéditas de artistas locais</a></h3>
                <p class="card__excerpt">Mostra gratuita fica em cartaz até o final do mês na capital.</p>
                <div class="card__meta">há 10h · 3 min de leitura</div>
              </div>
            </article>

            <article class="card">
              <div class="card__image">
                <span class="badge badge--politica">Política</span>
                <img src="https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=400&h=240&fit=crop" alt="">
              </div>
              <div class="card__body">
                <h3 class="card__title"><a href="#">Câmara discute reforma administrativa nesta semana</a></h3>
                <p class="card__excerpt">Proposta deve ser votada em regime de urgência segundo líderes do governo.</p>
                <div class="card__meta">há 12h · 6 min de leitura</div>
              </div>
            </article>

            <article class="card">
              <div class="card__image">
                <span class="badge badge--tecnologia">Tecnologia</span>
                <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=400&h=240&fit=crop" alt="">
              </div>
              <div class="card__body">
                <h3 class="card__title"><a href="#">Startups brasileiras recebem aporte recorde</a></h3>
                <p class="card__excerpt">Rodada de investimentos coloca o país entre os destaques da região.</p>
                <div class="card__meta">há 14h · 4 min de leitura</div>
              </div>
            </article>
          </div>

          <div class="pagination">
            <button class="btn btn--primary">Carregar mais notícias</button>
          </div>
        </section>
      </div>

      <!-- Sidebar -->
      <aside class="sidebar">
        <div class="widget">
          <h3 class="widget__title">Mais lidas</h3>
          <ol class="ranked-list">
            <li>
              <span class="number">01</span>
              <a href="#">Governo anuncia novo pacote de investimentos em infraestrutura</a>
            </li>
            <li>
              <span class="number">02</span>
              <a href="#">Mercado financeiro reage a dados de inflação do mês</a>
            </li>
            <li>
              <span class="number">03</span>
              <a href="#">Seleção brasileira anuncia convocados para amistosos</a>
            </li>
            <li>
              <span class="number">04</span>
              <a href="#">Novo estudo aponta avanços em energias renováveis</a>
            </li>
          </ol>
        </div>

        <div class="widget newsletter">
          <h3 class="widget__title">Newsletter</h3>
          <p>Receba as principais notícias do dia direto no seu e-mail, todas as manhãs.</p>
          <form>
            <input type="email" placeholder="Seu e-mail" required>
            <button type="submit" class="btn btn--accent btn--block">Assinar</button>
          </form>
        </div>

        <div class="ad-slot">Espaço publicitário</div>
      </aside>

    </div>
  </main>

  <!-- Rodapé -->
  <footer class="footer">
    <div class="container footer__top">
      <div class="footer__brand">
        <a href="#" class="logo">Folha<span>Digital</span></a>
        <p>Jornalismo independente e de qualidade, todos os dias, para todo o Brasil.</p>
        <div class="footer__social">
          <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/></svg></a>
          <a href="#" aria-label="Twitter"><svg viewBox="0 0 24 24"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C4 15.4 2.1 11.6 2 4c2.2 2.6 5.3 4.1 8.5 4.5C10 5.8 12.5 3 16 3c1.5 0 2.6.7 3.4 1.7C20.5 4.5 22 4 22 4z"/></svg></a>
        </div>
      </div>

      <div class="footer__col">
        <h4>Seções</h4>
        <ul>
          <li><a href="#">Política</a></li>
          <li><a href="#">Economia</a></li>
          <li><a href="#">Esportes</a></li>
          <li><a href="#">Cultura</a></li>
        </ul>
      </div>

      <div class="footer__col">
        <h4>Institucional</h4>
        <ul>
          <li><a href="#">Sobre nós</a></li>
          <li><a href="#">Contato</a></li>
          <li><a href="#">Trabalhe conosco</a></li>
        </ul>
      </div>

      <div class="footer__col">
        <h4>Legal</h4>
        <ul>
          <li><a href="#">Termos de uso</a></li>
          <li><a href="#">Privacidade</a></li>
        </ul>
      </div>
    </div>

    <div class="container footer__bottom">
      <p>© 2026 Folha Digital. Todos os direitos reservados.</p>
      <p>Feito com CSS moderno e responsivo.</p>
    </div>
  </footer>

  <button class="back-to-top" aria-label="Voltar ao topo">
    <svg viewBox="0 0 24 24"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
  </button>

  <script>
    // Toggle do menu mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const navMobile = document.querySelector('.nav--mobile');
    menuToggle.addEventListener('click', () => {
      const isOpen = navMobile.classList.toggle('is-open');
      menuToggle.classList.toggle('is-open');
      menuToggle.setAttribute('aria-expanded', isOpen);
    });

    // Botão voltar ao topo
    const backToTop = document.querySelector('.back-to-top');
    window.addEventListener('scroll', () => {
      backToTop.classList.toggle('is-visible', window.scrollY > 400);
    });
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  </script>

</body>
</html>