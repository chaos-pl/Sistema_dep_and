@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editId = @json($errors->any() && old('modal_type') === 'edit' ? old('modal_id') : request('editar'));
    const showId = @json(request('ver'));
    const id = editId ? 'modalEditTutor' + editId : (showId ? 'modalShowTutor' + showId : null);
    if (id) {
        const modal = document.getElementById(id);
        if (modal) bootstrap.Modal.getOrCreateInstance(modal).show();
    }
});
</script>
@endpush
