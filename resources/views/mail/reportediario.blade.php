<html>

<body>
    <div style="width: 100%; text-align: center">
        <img style="width: 300px" src="https://intranet.joremet.cl/logo_joremet.png" />
    </div>
    <div style="">
        <p>Estimado <b>{{ $usuario }}</b>,</p>

        <p>Este es un recordatorio diario del estado actual de los documentos pendientes de categorizar en la empresa
            <b>{{ $empresa }}</b>.</p>

        <p>🔍 Cantidad de documentos pendientes al día de hoy ({{ date('d/m/Y') }}): <b>{{ $contador }}</b></p>

        <p>Te recordamos la importancia de mantener al día la categorización para asegurar un flujo de trabajo ágil y
            organizado.</p>

        <p>Si tienes alguna duda o necesitas soporte, no dudes en contactarnos.</p>

        <p>Saludos,</p>
        <p><b>SolucionTotal Chile Limitada</b></p>
        NOTA: Este correo fue generado en forma automática. No responda este mail porque su respuesta será ignorada.
    </div>
</body>

</html>
