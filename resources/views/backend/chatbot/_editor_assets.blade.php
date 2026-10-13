{{-- Shared styles + JS for the WhatsApp-style message editor and the options manager.
     Include once per page, after the form markup. --}}
<style>
    .cb-wrap { --cb-green:#128C7E; }
    .wa-editor { border:1px solid #e3e6ef; border-radius:.5rem; overflow:hidden; }
    .wa-toolbar { display:flex; gap:.25rem; padding:.4rem .5rem; background:#f8f9fb; border-bottom:1px solid #e3e6ef; flex-wrap:wrap; }
    .wa-toolbar button { border:1px solid #dfe3ec; background:#fff; border-radius:.35rem; min-width:34px; height:32px; font-size:.9rem; cursor:pointer; line-height:1; }
    .wa-toolbar button:hover { background:#eef1f6; }
    .wa-input { width:100%; border:0; padding:.75rem; resize:vertical; min-height:130px; font-family:inherit; outline:none; }
    .wa-preview-label { font-size:.72rem; letter-spacing:.04em; text-transform:uppercase; color:#8a94a6; padding:.35rem .6rem 0; }
    .wa-preview { margin:.4rem; padding:.7rem .9rem; background:#dcf8c6; border-radius:.6rem .6rem .6rem .1rem; white-space:pre-wrap; word-break:break-word; font-size:.92rem; color:#111; min-height:1.5rem; box-shadow:0 1px 1px rgba(0,0,0,.06); max-width:85%; }
    .opt-manager table { margin-bottom:.4rem; }
    .opt-manager .drag-handle { cursor:grab; user-select:none; }
    .opt-row.dragging { opacity:.5; }
</style>

<script>
(function () {
    /* ---------- WhatsApp text formatting + live preview ---------- */
    function wrapSelection(ta, marker) {
        var s = ta.selectionStart, e = ta.selectionEnd;
        var sel = ta.value.substring(s, e) || 'text';
        ta.value = ta.value.substring(0, s) + marker + sel + marker + ta.value.substring(e);
        ta.focus();
        ta.selectionStart = s + marker.length;
        ta.selectionEnd = e + marker.length;
        ta.dispatchEvent(new Event('input'));
    }
    function prefixLines(ta, prefix) {
        var s = ta.selectionStart, e = ta.selectionEnd;
        var before = ta.value.substring(0, s), sel = ta.value.substring(s, e) || 'item', after = ta.value.substring(e);
        var out = sel.split('\n').map(function (l) { return prefix + l; }).join('\n');
        ta.value = before + out + after;
        ta.focus();
        ta.dispatchEvent(new Event('input'));
    }
    function esc(t) { return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function renderWa(t) {
        var h = esc(t || '');
        h = h.replace(/\*(.+?)\*/g, '<strong>$1</strong>');
        h = h.replace(/_(.+?)_/g, '<em>$1</em>');
        h = h.replace(/~(.+?)~/g, '<s>$1</s>');
        h = h.replace(/^•\s?/gm, '• ');
        return h || '<span style="color:#8a94a6">Nothing to preview yet…</span>';
    }

    document.querySelectorAll('.wa-editor').forEach(function (box) {
        var ta = box.querySelector('.wa-input');
        var preview = box.querySelector('.wa-preview');
        box.querySelectorAll('[data-wa]').forEach(function (btn) {
            btn.addEventListener('click', function (ev) {
                ev.preventDefault();
                var cmd = btn.getAttribute('data-wa');
                if (cmd === 'bold') wrapSelection(ta, '*');
                else if (cmd === 'italic') wrapSelection(ta, '_');
                else if (cmd === 'strike') wrapSelection(ta, '~');
                else if (cmd === 'bullet') prefixLines(ta, '• ');
            });
        });
        function sync() { if (preview) preview.innerHTML = renderWa(ta.value); }
        ta.addEventListener('input', sync);
        sync();
    });

    /* ---------- Options manager (add / remove / drag-reorder / reindex) ---------- */
    document.querySelectorAll('.opt-manager').forEach(function (mgr) {
        var body = mgr.querySelector('.opt-body');
        var isMenu = mgr.getAttribute('data-menu') === '1';
        var addBtn = mgr.querySelector('.opt-add');

        function blankRow() {
            var tr = document.createElement('tr');
            tr.className = 'option-row';
            tr.innerHTML =
                '<td class="text-center text-muted drag-handle">↕</td>' +
                '<td><input type="hidden" class="opt-id" value="">' +
                '<input type="text" class="form-control form-control-sm opt-title" placeholder="Option label"></td>' +
                (isMenu ? '<td><input type="text" class="form-control form-control-sm opt-desc" placeholder="Short description"></td>' : '') +
                '<td class="text-center"><input type="checkbox" class="form-check-input opt-active" checked></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0 rm-option">✕</button></td>';
            return tr;
        }
        if (addBtn) addBtn.addEventListener('click', function () { body.appendChild(blankRow()); });
        body.addEventListener('click', function (e) {
            if (e.target.classList.contains('rm-option')) { var r = e.target.closest('tr'); if (r) r.remove(); }
        });

        var dragging = null;
        body.addEventListener('mousedown', function (e) {
            if (e.target.classList.contains('drag-handle')) { var r = e.target.closest('tr'); r.setAttribute('draggable','true'); dragging = r; }
        });
        body.addEventListener('dragstart', function (e) { if (dragging) { e.dataTransfer.effectAllowed='move'; dragging.classList.add('dragging'); } });
        body.addEventListener('dragover', function (e) {
            e.preventDefault();
            var over = e.target.closest('tr'); if (!over || over === dragging || !dragging) return;
            var rect = over.getBoundingClientRect();
            body.insertBefore(dragging, (e.clientY - rect.top) > rect.height/2 ? over.nextSibling : over);
        });
        body.addEventListener('dragend', function () { if (dragging) { dragging.classList.remove('dragging'); dragging.removeAttribute('draggable'); dragging = null; } });

        // On submit, name the inputs in DOM order so saved order = displayed order.
        var form = mgr.closest('form');
        form.addEventListener('submit', function () {
            body.querySelectorAll('tr.option-row').forEach(function (row, i) {
                var id = row.querySelector('.opt-id'), title = row.querySelector('.opt-title');
                var desc = row.querySelector('.opt-desc'), active = row.querySelector('.opt-active');
                if (id) id.setAttribute('name', 'options[' + i + '][id]');
                if (title) title.setAttribute('name', 'options[' + i + '][title]');
                if (desc) desc.setAttribute('name', 'options[' + i + '][description]');
                if (active) active.setAttribute('name', 'options[' + i + '][is_active]');
            });
        });
    });
})();
</script>
