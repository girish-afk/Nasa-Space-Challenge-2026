document.addEventListener('DOMContentLoaded', () => {
    const page = location.pathname.split('/').pop() || 'module';
    const key = `projectAstro.notes.${page}.v1`;
    const form = document.getElementById('moduleNoteForm');
    const input = document.getElementById('moduleNote');
    const list = document.getElementById('moduleNotes');
    if (!form || !input || !list) return;
    let notes = [];
    try { notes = JSON.parse(localStorage.getItem(key) || '[]'); } catch (_) { notes = []; }
    if (!Array.isArray(notes)) notes = [];
    const render = () => {
        list.replaceChildren();
        if (!notes.length) {
            const empty = document.createElement('li'); empty.className = 'module-note-empty';
            empty.textContent = 'No notes yet. Add your first mission note above.'; list.append(empty); return;
        }
        notes.forEach((note, index) => {
            const item = document.createElement('li'); item.className = 'module-note-item';
            const text = document.createElement('span'); text.textContent = note;
            const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'module-note-remove';
            remove.setAttribute('aria-label', 'Delete note'); remove.innerHTML = '<i class="bi bi-trash3"></i>';
            remove.addEventListener('click', () => { notes.splice(index, 1); save(); });
            item.append(text, remove); list.append(item);
        });
    };
    const save = () => { try { localStorage.setItem(key, JSON.stringify(notes)); } catch (_) { /* Storage may be disabled. */ } render(); };
    form.addEventListener('submit', event => {
        event.preventDefault(); const value = input.value.trim(); if (!value) return;
        notes.unshift(value); notes = notes.slice(0, 100); input.value = ''; save(); input.focus();
    });
    render();
});
