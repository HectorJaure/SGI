@extends('layouts.app')

@section('title', 'Instructivo Matriz de Seguridad - Sistema SGSST')

@section('header-title', 'Instructivo Matriz de Seguridad')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-file-word me-2"></i>
                Instructivo para la Matriz de Seguridad y Salud Ocupacional
            </h4>
        </div>
        <div class="card-body">
            <div class="document-content">
                <div class="document-section">
                    <h5 class="section-title">1. Propósito</h5>
                    <p>Conocer las indicaciones para la correcta interpretación y llenado de los requisitos establecidos en la Matriz de Seguridad para orientar y sistematizar las actividades.</p>
                </div>

                <div class="document-section">
                    <h5 class="section-title">2. Alcance</h5>
                    <p>El presente documento es aplicable para la gestión, implementación y seguimiento de los requisitos establecidos en la norma ISO 45001:2018 en materia de seguridad y salud ocupacional u otros criterios normativos aplicables.</p>
                </div>

                <div class="document-section">
                    <h5 class="section-title">3. Políticas de operación</h5>
                    
                    <h6 class="subsection-title">De la matriz de seguridad</h6>
                    <ol type="a">
                        <li>Los(as) Representantes de Dirección (RD) de los Institutos que integran el Grupo 2 Multisitios, deberán gestionar la integración o actualización de la Comisión Mixta de Seguridad y Salud Ocupacional del Tecnológico al que pertenecen y capacitar conforme a este documento, para el llenado de la "Matriz de Seguridad".</li>
                        <li>Para gestionar la información que se debe integrar en la Matriz de Seguridad, la Comisión Mixta de Seguridad y Salud Ocupacional serán los responsables de actualizar y dar seguimiento al documento de la matriz, conforme a los recorridos de verificación programados trimestralmente (referente NOM-019-STPS-2011) de acuerdo a los cambios de infraestructura planificados en el instituto.</li>
                        <li>
                            <strong>Del llenado de la información, la Matriz requisita los siguientes temas:</strong>
                            <ol type="i">
                                <li><strong>Lugar o área:</strong> Se debe colocar el nombre del edificio y espacio que fue revisado durante el recorrido de verificación, por ejemplo: laboratorio de cómputo, taller de industrial, oficina de planeación, etc.</li>
                                <li><strong>Actividad:</strong> Se deberá colocar, conforme al área revisada, la(as) actividad(es) que se desarrolla(an) y que pueden tener un impacto a la seguridad y salud del personal.</li>
                                <li><strong>Peligro:</strong> Conforme a la actividad, se debe analizar cuales son las causas que pueden poner en peligro a la(as) persona(as), por ejemplo: Practicas académicas-manejo de equipo industrial o sustancias químicas, Almacenamiento-levantamiento de objetos pesados.</li>
                                <li><strong>Tipo de riesgo:</strong> Se debe seleccionar si el peligro identificado es una causa interna o si es un factor externo.</li>
                                <li><strong>Otros factores:</strong> Se debe seleccionar si el peligro identificado impacta a factores psicosociales, carga de trabajo, victimización o acoso, inmediaciones del lugar, situaciones no controlables o simplemente no aplica.</li>
                                <li><strong>Clasificación del peligro:</strong> Deberá marcar con una "x", si la situación impacta a la seguridad o a la salud de la persona.</li>
                                <li><strong>Evaluación del riesgo:</strong> Referente al peligro y al riesgo identificado, se deberá realizar la evaluación considerando los valores de las tablas de significancia, respecto a la probabilidad (Tiempo de exposición, No. de personas expuestas, Probabilidad de ocurrencia) y la consecuencia (Peligro a infraestructura y/o equipos, Consecuencia a la persona), la cual se anexa en la hoja 2 del documento de la matriz.</li>
                            </ol>
                        </li>
                    </ol>

                    <h6 class="subsection-title">De la matriz del marco legal</h6>
                    <ol type="a">
                        <li>Una vez realizado el diagnóstico de los peligros y riesgos, la Comisión Mixta de Seguridad y Salud deberá realizar el análisis del marco legal aplicable en la materia, se podrá hacer uso del "Asistente Normativo de la Secretaría de Trabajo y Previsión Social (STPS), no limitando la consulta únicamente de las normativas de este órgano gubernamental, sino además consultar otras normativas en materia que pudieran aplicar.</li>
                        <li>
                            Se deberá gestionar la matriz del marco legal aplicable integrando la siguiente información:
                            <ol type="i">
                                <li><strong>Norma:</strong> el código de la norma a la que hace referencia, ejemplo: NOM-001-STPS-2008.</li>
                                <li><strong>Titulo:</strong> se coloca el nombre o descripción de la norma.</li>
                                <li><strong>Tipo de requisito:</strong> se describe si el requisito aplicable hace referencia a un estudio, programa, medidas de seguridad, autorización, capacitación, programa específico, es de reconocimiento, evaluación y control, registro administrativo o equipo de protección personal.</li>
                                <li><strong>No. De requisito:</strong> Anotar el número de requisito de la norma aplicable, ejemplo: 5.2, 6.5, etc.</li>
                                <li><strong>Descripción:</strong> Copiar y pegar la descripción del requisito aplicable.</li>
                                <li><strong>Complimiento:</strong> Se debe realizar el análisis, marcando con una x en la celda "SI", en el caso que se cuente con la evidencia de cumplimiento de ese requisito y en la celda de "evidencia", hacer una descripción de esta.</li>
                            </ol>
                        </li>
                    </ol>
                </div>

                <div class="document-section">
                    <h5 class="section-title">4. Glosario</h5>
                    <div class="glossary-terms">
                        <div class="term">
                            <strong>Seguridad y salud:</strong> Son los programas, procedimientos, medidas y acciones de reconocimiento, evaluación y control que se aplican en los centros laborales para prevenir accidentes y enfermedades de trabajo, con el objeto de preservar la vida, salud e integridad física de los trabajadores, así como de evitar cualquier posible deterioro al centro de trabajo.
                        </div>
                        <div class="term">
                            <strong>Peligro:</strong> Fuente o situación con potencial de daño en términos de lesión o enfermedad, a la propiedad, al ambiente de trabajo o la combinación de estos.
                        </div>
                        <div class="term">
                            <strong>Riesgo:</strong> Los accidentes y enfermedades a que están expuestos los trabajadores en ejercicio o con motivo de su trabajo.
                        </div>
                        <div class="term">
                            <strong>Accidente:</strong> Toda lesión orgánica o perturbación funcional, inmediata o posterior, o la muerte, producida repentinamente en ejercicio o con motivo del trabajo, cualesquiera que sean el lugar y el tiempo en que se preste.
                        </div>
                        <div class="term">
                            <strong>Incidente:</strong> Los acontecimientos que pueden o no ocasionar daños dando lugar a un accidente de los trabajadores.
                        </div>
                    </div>
                </div>

                <div class="document-section">
                    <h5 class="section-title">5. Registros</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Información de registro</th>
                                    <th>Tiempo de resguardo</th>
                                    <th>Responsable del registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Acta instalación de la comisión mixta</td>
                                    <td>2 años</td>
                                    <td>Coordinador o secretaria(o) de la Comisión Mixta de Seguridad y Salud</td>
                                </tr>
                                <tr>
                                    <td>Matriz de seguridad</td>
                                    <td>Permanente (actualización trimestral)</td>
                                    <td>Coordinador o secretaria(o) de la Comisión Mixta de Seguridad y Salud</td>
                                </tr>
                                <tr>
                                    <td>Matriz del marco legal actualizado</td>
                                    <td>Permanente (actualización trimestral)</td>
                                    <td>Coordinador o secretaria(o) de la Comisión Mixta de Seguridad y Salud</td>
                                </tr>
                                <tr>
                                    <td>Informe de "Asinom" o Acta del análisis del marco legal aplicable</td>
                                    <td>1 año</td>
                                    <td>Coordinador o secretaria(o) de la Comisión Mixta de Seguridad y Salud</td>
                                </tr>
                                <tr>
                                    <td>Lista de verificación o acta de recorrido de verificación</td>
                                    <td>1 año</td>
                                    <td>Personal de mantenimiento / Coordinador o secretaria(o) Comisión Mixta de Seguridad y Salud</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ asset('documents/instructivo_matriz_seguridad.docx') }}" 
                   class="btn btn-success"
                   download="Instructivo_Matriz_Seguridad_ITSN.docx">
                    <i class="fas fa-download me-2"></i> Descargar Documento Word
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.document-content {
    line-height: 1.6;
    font-size: 14px;
}

.document-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}

.section-title {
    color: #2c5282;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #4299e1;
}

.subsection-title {
    color: #4a5568;
    font-weight: 600;
    margin: 20px 0 10px 0;
}

.glossary-terms {
    margin-left: 20px;
}

.term {
    margin-bottom: 15px;
    padding-left: 10px;
    border-left: 3px solid #cbd5e0;
}

.table th {
    background-color: #2c5282;
    color: white;
    font-weight: 600;
}

ol {
    margin-left: 20px;
}

ol li {
    margin-bottom: 8px;
}

.btn-success {
    background-color: #38a169;
    border-color: #38a169;
}

.btn-success:hover {
    background-color: #2f855a;
    border-color: #2f855a;
    transform: translateY(-1px);
}

@media print {
    .btn {
        display: none !important;
    }
    
    .card-header {
        background-color: #2c5282 !important;
        color: white !important;
    }
    
    .document-section {
        border-bottom: 1px solid #000 !important;
    }
}
</style>
@endsection