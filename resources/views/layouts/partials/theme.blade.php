<style>
    :root {
        --bs-primary: #9B72CF;
        --bs-primary-rgb: 155, 114, 207;
        --bs-secondary: #A388D4;
        --bs-secondary-rgb: 163, 136, 212;
        --bs-body-bg: #F4F4F8;
        --bs-body-color: #374151;
        --bs-border-color: #E9DFF7;
        --bs-light: #FAF5FF;
        --bs-light-rgb: 250, 245, 255;
        --bs-warning: #F3D9A5;
        --bs-warning-rgb: 243, 217, 165;
        --app-bg: #F4F4F8;
        --app-surface: #FFFFFF;
        --app-surface-soft: #FAF5FF;
        --app-sidebar: #9B72CF;
        --app-sidebar-dark: #8760BD;
        --app-sidebar-text: #F9F7FD;
        --app-text: #374151;
        --app-muted: #6B7280;
        --app-border: #E9DFF7;
        --app-hover: #F1EAFE;
        --app-card-shadow: 0 18px 45px rgba(155, 114, 207, 0.10);
        --app-warning-soft: #FFF3DA;
        --app-danger-soft: #FCEBEA;
        --app-success-soft: #EAF8EF;
        --app-info-soft: #EEF2FF;
    }

    body {
        background-color: var(--app-bg);
        color: var(--app-text);
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .text-muted-soft { color: var(--app-muted); }
    .app-shell { min-height: 100vh; background: radial-gradient(circle at top left, rgba(163, 136, 212, 0.12), transparent 30%), var(--app-bg); }
    .app-sidebar {
        width: 290px; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1030;
        background: linear-gradient(180deg, var(--app-sidebar) 0%, var(--app-sidebar-dark) 100%);
        color: var(--app-sidebar-text); padding: 1.5rem 1rem; box-shadow: 0 12px 32px rgba(91, 62, 146, 0.20);
    }
    .app-sidebar .brand-mark, .mobile-brand-mark {
        width: 52px; height: 52px; border-radius: 1.25rem; background: rgba(255, 255, 255, 0.18);
        display: inline-flex; align-items: center; justify-content: center;
    }
    .sidebar-link {
        color: rgba(255, 255, 255, 0.94); border-radius: 1rem; padding: 0.9rem 1rem; margin-bottom: 0.45rem;
        display: flex; align-items: center; gap: 0.8rem; font-weight: 500; text-decoration: none; transition: 0.22s ease;
    }
    .sidebar-link:hover, .sidebar-link.active { background: rgba(255, 255, 255, 0.16); color: #fff; }
    .sidebar-footer-card { background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.14); border-radius: 1.25rem; }
    .app-main { margin-left: 290px; min-height: 100vh; }
    .app-topbar { position: sticky; top: 0; z-index: 1020; backdrop-filter: blur(12px); background: rgba(244, 244, 248, 0.82); border-bottom: 1px solid var(--app-border); }
    .page-content { padding: 1.5rem; }
    .app-card { background: var(--app-surface); border: 1px solid var(--app-border); border-radius: 1.5rem; box-shadow: var(--app-card-shadow); }
    .app-card-soft { background: var(--app-surface-soft); border: 1px solid var(--app-border); border-radius: 1.5rem; }
    .metric-icon {
        width: 54px; height: 54px; border-radius: 1rem; display: inline-flex; align-items: center; justify-content: center;
        background: #F1EAFE; color: #7E57B7; font-size: 1.3rem;
    }
    .soft-badge { display: inline-flex; align-items: center; gap: 0.35rem; border-radius: 999px; padding: 0.45rem 0.8rem; font-size: 0.8rem; font-weight: 700; }
    .soft-primary { background: #F1EAFE; color: #7B53B5; }
    .soft-warning { background: var(--app-warning-soft); color: #8A6500; }
    .soft-danger { background: var(--app-danger-soft); color: #A4475A; }
    .soft-success { background: var(--app-success-soft); color: #287D46; }
    .soft-info { background: var(--app-info-soft); color: #4E5EBB; }
    .btn { border-radius: 1rem; font-weight: 600; padding: 0.78rem 1.05rem; }
    .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); }
    .btn-primary:hover, .btn-primary:focus { background-color: #8960c1; border-color: #8960c1; }
    .btn-soft-primary { background: #F1EAFE; color: #7B53B5; border: 1px solid #E2D4F5; }
    .btn-soft-primary:hover { background: #E9DDFB; color: #7147AF; }
    .form-control, .form-select, .form-check-input, .input-group-text { border-radius: 1rem; border-color: var(--app-border); }
    .form-control, .form-select, textarea.form-control { padding: 0.88rem 1rem; box-shadow: none !important; }
    .form-control:focus, .form-select:focus, .form-check-input:focus { border-color: #C5A9EC; box-shadow: 0 0 0 0.18rem rgba(155, 114, 207, 0.16) !important; }
    .form-check-input:checked { background-color: var(--bs-primary); border-color: var(--bs-primary); }
    .welcome-gradient { background: linear-gradient(135deg, #F8F2FF 0%, #FFFFFF 60%); }
    .step-pill {
        min-width: 42px; height: 42px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; background: #F1EAFE; color: #7B53B5;
    }
    .question-option { border: 1px solid var(--app-border); border-radius: 1.25rem; padding: 1rem; transition: 0.2s ease; cursor: pointer; background: #fff; }
    .question-option:hover, .question-option.active { border-color: #C5A9EC; background: #F8F2FF; box-shadow: 0 12px 24px rgba(155, 114, 207, 0.10); }
    .journal-box { min-height: 190px; resize: vertical; }
    .timeline-alert { border-left: 4px solid #D5B8F4; }
    .table thead th { color: var(--app-muted); font-size: 0.84rem; text-transform: uppercase; letter-spacing: 0.04em; border-bottom-color: var(--app-border); }
    .table tbody td { border-color: #F1E9FB; vertical-align: middle; }
    .offcanvas.offcanvas-start { width: 290px; }
    .offcanvas-soft { background: linear-gradient(180deg, var(--app-sidebar) 0%, var(--app-sidebar-dark) 100%); color: #fff; }

    @media (max-width: 991.98px) {
        .app-main { margin-left: 0; }
        .page-content { padding: 1rem; }
    }
</style>
