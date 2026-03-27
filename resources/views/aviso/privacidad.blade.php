@extends('layouts.app')

@section('title', 'Aviso de privacidad')
@section('page-title', 'Aviso de privacidad')
@section('page-subtitle', 'Tratamiento responsable y confidencial de la información')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="app-card p-4 p-md-5">
                <h2 class="fw-bold mb-4">Aviso de privacidad</h2>

                <p>
                    La plataforma DEPAND recaba información relacionada con evaluaciones emocionales, respuestas clínicas
                    y registros textuales con la finalidad de apoyar la detección oportuna y el acompañamiento institucional.
                </p>

                <p>
                    La identidad del estudiante se protege mediante el uso de un código anónimo durante los procesos de
                    evaluación, análisis y visualización clínica.
                </p>

                <p>
                    La información será visualizada únicamente por perfiles autorizados conforme a las funciones del sistema,
                    y será utilizada con fines de seguimiento, atención y análisis.
                </p>

                <div class="app-card p-4 mt-4" style="background:#faf7ff;">
                    <h5 class="fw-bold mb-3">Compromisos del sistema</h5>
                    <ul class="mb-0">
                        <li>Resguardar la confidencialidad de las respuestas.</li>
                        <li>Reducir la exposición innecesaria de datos personales.</li>
                        <li>Permitir atención profesional cuando exista riesgo detectado.</li>
                        <li>Favorecer un entorno digital respetuoso y seguro.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
