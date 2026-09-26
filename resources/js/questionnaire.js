// Respuestas únicamente en memoria de la página y en el servidor autenticado.
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-draft-url]');
    if (!form) return;
    const steps = [...form.querySelectorAll('.question-step')];
    const version = form.querySelector('[name="draft_version"]');
    const status = form.querySelector('[data-draft-status]');
    const saveButton = form.querySelector('[data-save-draft]');
    let current = Math.max(0, steps.findIndex(step => !step.querySelector('input:checked')));
    let dirty = form.dataset.initialDirty === '1', timer, saving = null, revision = 0, finishing = false, finalizing = false;
    let conflict = false;
    const say = (message) => { status.textContent = message; };
    function render(focus = false) {
        if (focus && window.anime && !window.prometeoReducedMotion?.()) {
            window.anime.remove(steps[current]);
            window.anime({targets:steps[current],translateY:[12,0],opacity:[0,1],duration:260,easing:'easeOutQuad'});
        }
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === current);
            step.setAttribute('aria-hidden', String(i !== current));
            const answered = Boolean(step.querySelector('input:checked'));
            step.querySelectorAll('.option-tile').forEach(tile => tile.classList.toggle('active', tile.querySelector('input').checked));
            const next = step.querySelector('.btn-next');
            if (next) { next.disabled = !answered; next.style.opacity = answered ? '1' : '.5'; next.style.pointerEvents = 'auto'; }
        });
        const complete = steps.every(step => step.querySelector('input:checked'));
        const submit = form.querySelector('#btn-submit');
        submit.classList.remove('d-none');
        submit.disabled = !complete || finalizing;
        document.querySelector('#progress-text').textContent = current + 1;
        document.querySelector('#progress-bar').style.width = (100 * steps.filter(step => step.querySelector('input:checked')).length / steps.length) + '%';
        if (focus) {
            const title = steps[current].querySelector('h2');
            title.tabIndex = -1;
            title.focus();
        }
    }
    async function persist() {
        clearTimeout(timer);
        if (conflict) throw new Error('El cuestionario cambió en otra pestaña. Recarga para continuar.');
        if (saving) { await saving; if (dirty) return persist(); return; }
        if (!dirty) return;
        const sentRevision = revision;
        const answers = {};
        form.querySelectorAll('.step-radio:checked').forEach(input => { answers[input.name.match(/\[(\d+)\]/)[1]] = Number(input.value); });
        say('Guardando…');
        saving = (async () => {
            const response = await fetch(form.dataset.draftUrl, {
                method: 'PUT', credentials: 'same-origin',
                headers: {'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value},
                body: JSON.stringify({answers, draft_version: Number(version.value)})
            });
            if (!response.ok) {
                let data = {}; try { data = await response.json(); } catch {}
                if (data.errors?.draft_version) conflict = true;
                throw new Error(conflict ? 'El cuestionario cambió en otra pestaña. Recarga para continuar.' :
                    (response.status === 419 || response.status === 401 ? 'Tu sesión venció. Inicia sesión para guardar.' : 'No se pudo guardar. Conserva esta página y vuelve a intentarlo.'));
            }
            const data = await response.json();
            if (!Number.isInteger(data.version)) throw new Error('No se confirmó el guardado. Vuelve a intentarlo.');
            version.value = data.version;
            dirty = revision !== sentRevision;
            say(dirty ? 'Hay cambios pendientes de guardar.' : 'Borrador guardado. Puedes continuar más tarde.');
        })();
        try { await saving; } catch (error) { say(error.message); throw error; }
        finally { saving = null; }
        if (dirty) return persist();
    }
    form.addEventListener('change', event => {
        if (!event.target.matches('.step-radio')) return;
        revision++; dirty = true; say('Cambios pendientes de guardar.'); render();
        clearTimeout(timer); timer = setTimeout(() => persist().catch(() => {}), 700);
    });
    steps.forEach((step, i) => {
        step.querySelector('.btn-prev')?.addEventListener('click', () => { current = Math.max(0, i - 1); render(true); });
        step.querySelector('.btn-next')?.addEventListener('click', () => {
            if (step.querySelector('input:checked')) { current = Math.min(steps.length - 1, i + 1); render(true); }
        });
    });
    saveButton.addEventListener('click', async () => {
        saveButton.disabled = true;
        try { await persist(); if (!dirty) say('Borrador guardado. Puedes continuar más tarde.'); }
        catch {} finally { saveButton.disabled = false; }
    });
    document.querySelectorAll('.btn-cancelar-eval').forEach(link => link.addEventListener('click', async event => {
        event.preventDefault();
        try { await persist(); finishing = true; window.location.assign(link.href); } catch {}
    }));
    form.addEventListener('submit', async event => {
        if (finishing) return;
        event.preventDefault();
        if (finalizing) return;
        if (!steps.every(step => step.querySelector('input:checked'))) { say('Responde todas las preguntas antes de enviar.'); return; }
        finalizing = true; render();
        try {
            await persist();
            finishing = true;
            // El servidor valida todas las respuestas y la versión antes de puntuar.
            HTMLFormElement.prototype.submit.call(form);
        } catch { finalizing = false; render(); }
    });
    window.addEventListener('beforeunload', event => { if (dirty && !finishing) { event.preventDefault(); event.returnValue = ''; } });
    if (dirty) say('Hay respuestas pendientes de guardar.');
    render();
});
