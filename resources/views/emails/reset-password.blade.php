@extends('emails.layout')

@section('content')
    <div class="email-greeting">
        Restablecer Contraseña
    </div>
    
    <div class="email-content">
        <p>
            Hola,
        </p>
        
        <p>
            Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>PS-EDU</strong>.
        </p>
        
        <p>
            Haz clic en el siguiente botón para crear una nueva contraseña:
        </p>
    </div>
    
    <div class="button-container">
        <a href="{{ $resetUrl }}" class="button">
            Restablecer Contraseña
        </a>
    </div>
    
    <div class="info-box">
        <p>
            <strong>ℹ️ Información:</strong> Este enlace expirará en <strong>60 minutos</strong>.
        </p>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <p style="font-size: 14px; color: #6b7280;">
            Si no solicitaste este cambio, ignora este email. Tu contraseña actual seguirá siendo válida.
        </p>
        
        <p style="font-size: 13px; color: #9ca3af; margin-top: 20px;">
            Si tienes problemas para hacer clic en el botón, copia y pega el siguiente enlace en tu navegador:
        </p>
        
        <p style="font-size: 12px; color: #2563eb; word-break: break-all; background-color: #f3f4f6; padding: 12px; border-radius: 6px; font-family: 'Courier New', monospace;">
            {{ $resetUrl }}
        </p>
    </div>
@endsection
