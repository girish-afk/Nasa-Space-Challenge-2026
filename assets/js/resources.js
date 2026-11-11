document.addEventListener('DOMContentLoaded', () => {
    'use strict';
    const $ = (id) => document.getElementById(id);
    const storageKeys = {
        inventory: 'projectAstro.resources.inventory.v1',
        checklist: 'projectAstro.resources.checklist.v1'
    };
    const safeRead = (key, fallback) => {
        try { const value = JSON.parse(localStorage.getItem(key)); return value ?? fallback; }
        catch (_) { return fallback; }
    };
    const safeWrite = (key, value) => {
        try { localStorage.setItem(key, JSON.stringify(value)); return true; }
        catch (_) { return false; }
    };
    const escapeHTML = (value) => String(value).replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    const number = (value) => Number.isFinite(Number(value)) ? Number(value) : 0;

    // Scientific resource catalog is served by the project's PHP API.
    const catalog = $('resourceCatalog');
    fetch('api/resources.php', { headers: { 'Accept': 'application/json' } })
        .then(response => { if (!response.ok) throw new Error(`Catalog request failed (${response.status})`); return response.json(); })
        .then(payload => {
            if (!payload.success || !Array.isArray(payload.resources)) throw new Error('Unexpected catalog response.');
            catalog.innerHTML = payload.resources.map(item => `
                <article class="astro-card resource-catalog-card">
                    <div class="resource-card-top"><span class="resource-card-icon"><i class="bi ${escapeHTML(item.icon)}"></i></span><span class="resource-status-pill ${escapeHTML(item.status_type)}">${escapeHTML(item.status)}</span></div>
                    <span class="resource-category-label">${escapeHTML(item.category)}</span>
                    <h3>${escapeHTML(item.name)}</h3><p>${escapeHTML(item.description)}</p>
                    <div class="resource-uses"><strong>Potential applications</strong><ul>${item.uses.map(use => `<li>${escapeHTML(use)}</li>`).join('')}</ul></div>
                    <div class="resource-evidence"><strong>Evidence &amp; limitations</strong><p>${escapeHTML(item.evidence)}</p></div>
                    <a class="resource-link" href="${escapeHTML(item.url)}" target="_blank" rel="noopener noreferrer">${escapeHTML(item.source)} <i class="bi bi-box-arrow-up-right"></i></a>
                </article>`).join('');
            $('catalogStatus').textContent = `Catalog loaded from PROJECT ASTRO API · ${payload.resources.length} resource classes · ${payload.catalog_type.replaceAll('_', ' ')}`;
        })
        .catch(error => {
            catalog.innerHTML = `<div class="resource-api-error"><i class="bi bi-exclamation-triangle"></i><strong>Resource catalog unavailable</strong><span>${escapeHTML(error.message)}. Check that Apache is running and the project is opened through localhost.</span></div>`;
            $('catalogStatus').textContent = 'The local resource catalog API could not be reached.';
        });

    // Browser-local mission inventory. No server account/database is assumed by this project build.
    let inventory = safeRead(storageKeys.inventory, []);
    if (!Array.isArray(inventory)) inventory = [];
    const inventoryForm = $('inventoryForm');
    const inventoryBody = $('inventoryTableBody');
    const inventoryFeedback = $('inventoryFeedback');
    const resetInventoryForm = () => {
        inventoryForm.reset(); $('inventoryEditId').value = ''; $('inventoryFormTitle').textContent = 'Add inventory item';
        $('inventorySubmit').innerHTML = '<i class="bi bi-plus-lg"></i> Add to inventory'; $('inventoryCancel').hidden = true;
    };
    const renderInventory = () => {
        $('inventoryCount').textContent = inventory.length;
        $('inventoryTableCount').textContent = `${inventory.length} ITEM${inventory.length === 1 ? '' : 'S'}`;
        if (!inventory.length) {
            inventoryBody.innerHTML = '<tr><td colspan="5" class="inventory-empty">No supplies added yet. Add your first item.</td></tr>';
            $('inventorySummary').textContent = 'No inventory records'; return;
        }
        let low = 0;
        inventoryBody.innerHTML = inventory.map(item => {
            const isLow = number(item.quantity) <= number(item.minimum);
            if (isLow) low++;
            return `<tr><td><strong>${escapeHTML(item.name)}</strong><small>${escapeHTML(item.category)}</small></td><td class="inventory-number">${number(item.quantity).toLocaleString(undefined,{maximumFractionDigits:2})} ${escapeHTML(item.unit)}</td><td>${number(item.minimum).toLocaleString(undefined,{maximumFractionDigits:2})} ${escapeHTML(item.unit)}</td><td><span class="stock-status ${isLow ? 'low' : 'good'}">${isLow ? 'Low stock' : 'Adequate'}</span></td><td><div class="inventory-actions"><button type="button" data-edit="${escapeHTML(item.id)}" aria-label="Edit ${escapeHTML(item.name)}"><i class="bi bi-pencil-square"></i></button><button type="button" data-delete="${escapeHTML(item.id)}" aria-label="Delete ${escapeHTML(item.name)}"><i class="bi bi-trash3"></i></button></div></td></tr>`;
        }).join('');
        $('inventorySummary').textContent = `${inventory.length} supplies · ${low} at or below minimum reserve`;
    };
    inventoryForm.addEventListener('submit', event => {
        event.preventDefault();
        const name = $('inventoryName').value.trim();
        const quantity = Number($('inventoryQuantity').value);
        const minimum = Number($('inventoryMinimum').value);
        if (!name || !Number.isFinite(quantity) || quantity < 0 || !Number.isFinite(minimum) || minimum < 0) {
            inventoryFeedback.textContent = 'Enter a name and valid non-negative quantities.'; return;
        }
        const id = $('inventoryEditId').value;
        const record = { id: id || (window.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(16).slice(2)}`), name, category: $('inventoryCategory').value, unit: $('inventoryUnit').value, quantity, minimum };
        if (id) inventory = inventory.map(item => item.id === id ? record : item); else inventory.unshift(record);
        if (!safeWrite(storageKeys.inventory, inventory)) { inventoryFeedback.textContent = 'Browser storage is unavailable. Your changes may not persist.'; return; }
        resetInventoryForm(); inventoryFeedback.textContent = id ? 'Inventory item updated.' : 'Inventory item added.'; renderInventory();
    });
    $('inventoryCancel').addEventListener('click', () => { resetInventoryForm(); inventoryFeedback.textContent = ''; });
    inventoryBody.addEventListener('click', event => {
        const editButton = event.target.closest('[data-edit]');
        const deleteButton = event.target.closest('[data-delete]');
        if (editButton) {
            const item = inventory.find(entry => entry.id === editButton.dataset.edit); if (!item) return;
            $('inventoryEditId').value = item.id; $('inventoryName').value = item.name; $('inventoryCategory').value = item.category;
            $('inventoryUnit').value = item.unit; $('inventoryQuantity').value = item.quantity; $('inventoryMinimum').value = item.minimum;
            $('inventoryFormTitle').textContent = 'Edit inventory item'; $('inventorySubmit').innerHTML = '<i class="bi bi-check2"></i> Save changes'; $('inventoryCancel').hidden = false; $('inventoryName').focus();
        }
        if (deleteButton) {
            const item = inventory.find(entry => entry.id === deleteButton.dataset.delete); if (!item) return;
            if (!window.confirm(`Remove ${item.name} from this mission inventory?`)) return;
            inventory = inventory.filter(entry => entry.id !== item.id); safeWrite(storageKeys.inventory, inventory); renderInventory(); inventoryFeedback.textContent = 'Inventory item removed.';
        }
    });
    $('exportInventory').addEventListener('click', () => {
        if (!inventory.length) { inventoryFeedback.textContent = 'Add at least one supply before exporting.'; return; }
        const fields = ['name','category','quantity','unit','minimum'];
        const csv = [fields.join(','), ...inventory.map(item => fields.map(field => `"${String(item[field] ?? '').replace(/"/g,'""')}"`).join(','))].join('\r\n');
        const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'}); const url = URL.createObjectURL(blob);
        const link = document.createElement('a'); link.href = url; link.download = 'project-astro-mission-inventory.csv'; link.click(); URL.revokeObjectURL(url);
    });
    renderInventory();

    // Transparent, user-editable arithmetic model; defaults are assumptions, not official NASA standards.
    const consumptionInputs = ['crewSize','missionDays','waterRate','oxygenRate'];
    const calculateDemand = () => {
        const crew = Math.max(1, Math.min(12, Math.floor(number($('crewSize').value) || 1)));
        const sols = Math.max(1, Math.min(2000, number($('missionDays').value) || 1));
        const waterRate = Math.max(0, number($('waterRate').value));
        const oxygenRate = Math.max(0, number($('oxygenRate').value));
        $('crewSizeValue').textContent = crew;
        const water = crew * sols * waterRate, oxygen = crew * sols * oxygenRate;
        $('waterDemand').textContent = water.toLocaleString(undefined,{maximumFractionDigits:2});
        $('oxygenDemand').textContent = oxygen.toLocaleString(undefined,{maximumFractionDigits:2});
        $('consumptionEquation').textContent = `${crew} crew × ${sols} sols × ${waterRate} L = ${water.toLocaleString(undefined,{maximumFractionDigits:2})} L water`;
    };
    consumptionInputs.forEach(id => $(id).addEventListener('input', calculateDemand));
    $('resetConsumption').addEventListener('click', () => {
        $('crewSize').value = 4; $('missionDays').value = 30; $('waterRate').value = 3.5; $('oxygenRate').value = 0.84; calculateDemand();
    });
    calculateDemand();

    // Persist survey checklist progress on this browser.
    const checklist = $('resourceChecklist');
    const completed = safeRead(storageKeys.checklist, []);
    if (Array.isArray(completed)) checklist.querySelectorAll('input[type="checkbox"]').forEach(input => { input.checked = completed.includes(input.value); });
    const updateChecklist = () => {
        const boxes = [...checklist.querySelectorAll('input[type="checkbox"]')];
        const done = boxes.filter(input => input.checked).length; const percent = Math.round(done / boxes.length * 100);
        $('checklistProgress').textContent = `${percent}%`; $('checklistCaption').textContent = `${done} of ${boxes.length} tasks complete`;
        $('checklistFooterText').textContent = `${done} of ${boxes.length} tasks complete`; $('checklistProgressBar').style.width = `${percent}%`;
        safeWrite(storageKeys.checklist, boxes.filter(input => input.checked).map(input => input.value));
    };
    checklist.addEventListener('change', updateChecklist);
    $('resetChecklist').addEventListener('click', () => { checklist.querySelectorAll('input[type="checkbox"]').forEach(input => { input.checked = false; }); updateChecklist(); });
    updateChecklist();
});
