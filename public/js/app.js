document.addEventListener('DOMContentLoaded', () => {
  iniciarAlternanciaTema();
  iniciarPreviewIA();
});

function iniciarAlternanciaTema() {
  const btn = document.getElementById('theme-toggle');
  if (!btn) return;

  function aplicarEstado() {
    const claro = document.documentElement.classList.contains('modo-claro');
    btn.classList.toggle('is-light', claro);
    btn.setAttribute('aria-pressed', claro ? 'true' : 'false');
    btn.setAttribute('aria-label', claro ? 'Alternar para modo escuro' : 'Alternar para modo claro');
    btn.setAttribute('title', claro ? 'Alternar para modo escuro' : 'Alternar para modo claro');
  }

  aplicarEstado();

  btn.addEventListener('click', () => {
    const claro = document.documentElement.classList.toggle('modo-claro');
    try {
      localStorage.setItem('supporteia-tema', claro ? 'claro' : 'escuro');
    } catch (e) {}
    aplicarEstado();
  });
}

function iniciarPreviewIA() {
  const titulo    = document.getElementById('titulo');
  const descricao = document.getElementById('descricao');
  const preview   = document.getElementById('ia-preview');
  if (!titulo || !descricao || !preview) return;

  let timer = null;
  let analisando = false;
  let ultimoConteudo = '';

  function analisar() {
    const t = titulo.value.trim();
    const d = descricao.value.trim();
    if (t.length < 10 || d.length < 20) return;

    const conteudo = `${t}\n${d}`;
    if (analisando || conteudo === ultimoConteudo) return;
    analisando = true;
    ultimoConteudo = conteudo;

    preview.className = 'ia-preview ativo';
    preview.innerHTML = `
      <div class="ia-preview-header">
        <div class="ia-dot"></div> IA analisando o problema...
      </div>
      <p style="font-size:.82rem;color:var(--text3);font-family:var(--font-mono)">Aguarde...</p>`;

    fetch(APP_URL + '/ia/analisar', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': CSRF_TOKEN
      },
      body: JSON.stringify({ titulo: t, descricao: d })
    })
    .then(r => {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    })
    .then(data => {
      if (!data.sucesso) {
        ultimoConteudo = '';
        preview.innerHTML = `<p style="color:var(--red);font-size:.85rem">⚠️ ${data.erro || 'Erro na análise da IA.'}</p>`;
        return;
      }
      const a = data.analise;
      preview.innerHTML = `
        <div class="ia-preview-header">
          <div class="ia-dot" style="animation:none;background:#2ecc71"></div>
          Análise da IA — Google Gemini
        </div>
        <div class="ia-grid">
          <div class="ia-item">
            <label>Categoria</label>
            <span class="cat-tag">${escHtml(a.categoria)}</span>
          </div>
          <div class="ia-item">
            <label>Prioridade sugerida</label>
            <span class="badge badge-${escHtml(a.prioridade)}">${escHtml(a.prioridade.toUpperCase())}</span>
          </div>
        </div>
        <div class="ia-analise-texto">
          <strong style="font-size:.72rem;color:var(--text3);font-family:var(--font-mono);text-transform:uppercase">Diagnóstico</strong><br>
          ${escHtml(a.analise)}<br><br>
          <strong style="font-size:.72rem;color:var(--text3);font-family:var(--font-mono);text-transform:uppercase">Sugestão</strong><br>
          ${escHtml(a.sugestao)}
        </div>`;
    })
    .catch(err => {
      ultimoConteudo = '';
      console.error('[IA Preview]', err);
      preview.innerHTML = `<p style="color:var(--red);font-size:.85rem">⚠️ Falha na conexão com a IA. Verifique a chave da API.</p>`;
    })
    .finally(() => {
      analisando = false;
    });
  }

  titulo.addEventListener('input',    () => { clearTimeout(timer); timer = setTimeout(analisar, 4000); });
  descricao.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(analisar, 4000); });
}

function reanalisarChamado(id) {
  const btn = document.getElementById('btn-reanalisar');
  if (btn) { btn.textContent = '⟳ Analisando...'; btn.disabled = true; }

  fetch(APP_URL + '/ia/reanalisar/' + id, {
    method: 'POST',
    headers: { 'X-CSRF-Token': CSRF_TOKEN }
  })
    .then(r => r.json())
    .then(data => {
      if (data.sucesso) {
        location.reload();
      } else {
        alert('Erro: ' + (data.erro || 'Falha desconhecida'));
        if (btn) { btn.textContent = '⟳ Reanalisar com IA'; btn.disabled = false; }
      }
    })
    .catch(() => {
      alert('Erro de conexão com a IA.');
      if (btn) { btn.textContent = '⟳ Reanalisar com IA'; btn.disabled = false; }
    });
}

function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str || ''));
  return d.innerHTML;
}
