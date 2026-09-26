<input type="hidden" name="draft_version" value="{{ $draftVersion }}">
<div class="draft-toolbar mb-4">
    <div><strong><i class="bi bi-cloud-check" aria-hidden="true"></i> Tu progreso</strong>
        <p class="mb-0 small" data-draft-status role="status" aria-live="polite">{{ count($draftAnswers) ? 'Borrador recuperado. Continúa donde te quedaste.' : 'Tus respuestas se guardarán mientras avanzas.' }}</p>
    </div>
    <button type="button" class="btn btn-outline-primary" data-save-draft>Guardar borrador</button>
</div>
<noscript><p class="alert alert-info">El guardado automático requiere JavaScript. Puedes responder y enviar el cuestionario completo.</p></noscript>
