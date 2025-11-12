<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acta de Verificación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .section-title {
            background-color: #d9d9d9;
            padding: 8px;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
        }
        
        .checked {
            background-color: #000;
        }
        
        .firma-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-top: 25px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mb-3 {
            margin-bottom: 15px;
        }
        
        .prioridad-seleccionada {
            background-color: #d9d9d9;
            font-weight: bold;
        }
        
        .firma-table {
            width: 100%;
            border: 1px solid #000;
        }
        
        .firma-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
            height: 60px;
        }
        
        .prioridad-table {
            width: 100%;
            border: 1px solid #000;
        }
        
        .prioridad-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 10px;
        }
        
        .empty-row {
            height: 20px;
        }
    </style>
</head>
<body>
    <!-- Primera página: Formato EXACTO de la primera imagen -->
    <table>
        <tr>
            <td colspan="5" class="section-title">ACTA DE VERIFICACIÓN</td>
        </tr>
        <tr>
            <td width="25%"><strong>DENOMINACIÓN DEL CENTRO DE TRABAJO</strong></td>
            <td colspan="4">Instituto Tecnológico Superior de Nochistlán</td>
        </tr>
        <tr>
            <td rowspan="4"><strong>DOMICILIO</strong></td>
            <td width="15%"><strong>Calle y No.</strong></td>
            <td colspan="3">Carr. Los Sandovales Km 2.4</td>
        </tr>
        <tr>
            <td><strong>Colonia</strong></td>
            <td colspan="3">Los Sandovales</td>
        </tr>
        <tr>
            <td><strong>Municipio</strong></td>
            <td width="25%">Nochistlán</td>
            <td width="15%"><strong>Entidad Federativa</strong></td>
            <td width="20%">Zacatecas</td>
        </tr>
        <tr>
            <td><strong>Ciudad</strong></td>
            <td>Nochistlán</td>
            <td><strong>C.P.</strong></td>
            <td>99900</td>
        </tr>
        <tr>
            <td><strong>NÚMERO CONSECUTIVO DEL ACTA</strong></td>
            <td>1</td>
            <td><strong>NÚMERO DE TRABAJADORES</strong></td>
            <td colspan="2">66</td>
        </tr>
    </table>
    
    <div class="mb-3">
        <strong>FECHA Y HORA DEL RECORRIDO DE VERIFICACIÓN:</strong><br>
        <table>
            <tr>
                <td width="15%"><strong>Fecha</strong></td>
                <td width="20%">23 al 27 de agosto 2025</td>
                <td width="15%"><strong>Hora de Inicio</strong></td>
                <td width="15%">09:00 hrs.</td>
                <td width="15%"><strong>Hora de Término</strong></td>
                <td width="20%">12:00 hrs.</td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="30%"><strong>TIPO DE RECORRIDO DE VERIFICACIÓN</strong></td>
                <td width="70%">
                    <span class="checkbox checked"></span> Ordinario
                    <span class="checkbox" style="margin-left: 15px;"></span> Extraordinario
                </td>
            </tr>
        </table>
    </div>
    
    <div class="section-title">
        LOS AGENTES, CONDICIONES Y ACTOS PELIGROSOS O INSEGUROS DETECTADOS DURANTE EL RECORRIDO DE VERIFICACIÓN:
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="20%">Área</th>
                <th width="75%">Agente, Condición ó Acto Inseguro detectado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riesgos as $index => $riesgo)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{!! nl2br(e($riesgo->lugar)) !!}</td>
                <td>{!! nl2br(e($riesgo->peligro)) !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Segunda página: Formato EXACTO de la segunda imagen -->
    <div class="page-break"></div>
    
    <div class="section-title">
        SOLUCIÓN RECOMENDADA
    </div>
    
    <!-- Tabla de soluciones recomendadas con prioridad en tabla interna dividida en 4 campos -->
    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="55%">Solución</th>
                <th colspan="4" class="text-center">Prioridad de atención (meses)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($solucionesRecomendadas as $index => $solucion)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{!! nl2br(e($solucion['descripcion'])) !!}</td>
                <td width="10%" class="text-center @if($solucion['prioridad'] == 'inmediata') prioridad-seleccionada @endif" style="border: 1px solid #000; padding: 4px;">
                    <strong>INMEDIATA</strong><br>
                    <strong>1</strong>
                </td>
                <td width="10%" class="text-center @if($solucion['prioridad'] == 'alta') prioridad-seleccionada @endif" style="border: 1px solid #000; padding: 4px;">
                    <strong>ALTA</strong><br>
                    <strong>(2 a 3)</strong>
                </td>
                <td width="10%" class="text-center @if($solucion['prioridad'] == 'media') prioridad-seleccionada @endif" style="border: 1px solid #000; padding: 4px;">
                    <strong>MEDIA</strong><br>
                    <strong>(4 a 5)</strong>
                </td>
                <td width="10%" class="text-center @if($solucion['prioridad'] == 'baja') prioridad-seleccionada @endif" style="border: 1px solid #000; padding: 4px;">
                    <strong>BAJA</strong><br>
                    <strong>6</strong>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">
        SEGUIMIENTO A RECOMENDACIONES ANTERIORES
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="65%">Avance de recomendaciones anteriores</th>
                <th width="30%">Causa de las recomendaciones pendientes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seguimientoRecomendaciones as $index => $seguimiento)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{!! nl2br(e($seguimiento['avance'])) !!}</td>
                <td>{!! nl2br(e($seguimiento['causa'])) !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">
        ACTIVIDADES RELEVANTES Y ASUNTOS GENERALES DE LA COMISIÓN
    </div>
    
    <!-- Espacio para actividades relevantes -->
    <div style="height: 100px; border: 1px solid #000; margin-bottom: 20px;"></div>

    <div class="section-title">
        LUGAR Y FECHA DE CONCLUSIÓN DEL ACTA:
    </div>
    <p>Instituto Tecnológico Superior de Nochistlán, 25 de agosto de 2025</p>

    <div class="section-title">
        NOMBRE Y FIRMA DE LOS INTEGRANTES DE LA COMISIÓN QUE PARTICIPARON EN EL RECORRIDO DE VERIFICACIÓN
    </div>

    <!-- Tabla de firmas SIN encabezados -->
    <table class="firma-table">
        <tbody>
            <tr>
                <td width="60%"><strong>LIC. LIZBETH CATALINA VILLALOBOS DURÁN</strong><br>COORDINADORA</td>
                <td width="40%"></td>
            </tr>
            <tr>
                <td><strong>ING. ARACELI SANDOVAL SANDOVAL</strong><br>SECRETARIA</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>MTRA. SAHARA BERMÚDEZ RODRÍGUEZ</strong><br>VOCAL</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>C. ARTURO AVELAR LECHUGA</strong><br>VOCAL</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>MTRA. ANGÉLICA AVELAR VIELMAS</strong><br>VOCAL</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>ARQ. ARMANDO CUEVAS CORREA</strong><br>VOCAL</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>C. MÓNICA AVELAR REYES</strong><br>VOCAL</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>C. SANDRA TERESA PADILLA MEDRANO</strong><br>VOCAL</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>