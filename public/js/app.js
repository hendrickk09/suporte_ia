document.addEventListener('DOMContentLoaded', () => {
  iniciarPreviewIA();
});

function iniciarPreviewIA() {
  const titulo    = document.getElementById('titulo');
  const descricao = document.getElementById('descricao');
  const preview   = document.getElementById('ia-preview');
  if (!titulo || !descricao || !preview) return;

  let timer = null;

  function analisar() {
    const t = titulo.value.trim();
    const d = descricao.value.trim();
    if (t.length < 10 || d.length < 20) return;

    preview.classList.add('ativo');
    preview.innerHTML = `<div class="ia-preview-header"><div class="ia-dot"></div>IA analisando...</div><p class="ia-loading">Classificando com Gemini...</p>`;

    fetch(APP_URL + '/ia/analisar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ titulo: t, descricao: d })
    })
    .then(r => r.json())
    .then(data => {
      if (!data.sucesso) return;
      const a = data.analise;
      preview.innerHTML = `
        <div class="ia-preview-header"><div class="ia-dot" style="animation:none"></div>Análise da IA — Google Gemini</div>
        <div class="ia-grid">
          <div class="ia-item"><label>Categoria</label><span class="badge badge-andamento">${a.categoria}</span></div>
          <div class="ia-item"><label>Prioridade sugerida</label><span class="badge badge-${a.prioridade}">${a.prioridade.toUpperCase()}</span></div>
        </div>
        <div class="ia-analise-texto">
          <strong style="font-size:.78rem;color:var(--text3);font-family:var(--font-mono);text-transform:uppercase">Diagnóstico</strong><br>${a.analise}<br><br>
          <strong style="font-size:.78rem;color:var(--text3);font-family:var(--font-mono);text-transform:uppercase">Sugestão</strong><br>${a.sugestao}
        </div>`;
    })
    .catch(() => {
      preview.innerHTML = `<p class="ia-loading" style="color:var(--red)">Falha na análise. Verifique a chave da API.</p>`;
    });
  }

  titulo.addEventListener('input',    () => { clearTimeout(timer); timer = setTimeout(analisar, 1800); });
  descricao.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(analisar, 1800); });
}

function reanalisarChamado(id) {
  const btn = document.getElementById('btn-reanalisar');
  if (btn) { btn.textContent = 'Analisando...'; btn.disabled = true; }

  fetch(APP_URL + '/ia/reanalisar/' + id, { method: 'POST' })
    .then(r => r.json())
    .then(data => {
      if (data.sucesso) location.reload();
      else {
        alert('Erro: ' + (data.erro || 'Falha desconhecida'));
        if (btn) { btn.textContent = '⟳ Reanalisar com IA'; btn.disabled = false; }
      }
    })
    .catch(() => {
      alert('Erro de conexão.');
      if (btn) { btn.textContent = '⟳ Reanalisar com IA'; btn.disabled = false; }
    });
}
