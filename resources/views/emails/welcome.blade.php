@extends('emails.layout')

@section('content')
    <div class="email-greeting">
        ¡Bienvenido(a), {{ $userName }}!
    </div>
    
    <div class="email-content">
        <p>
            Tu cuenta ha sido creada exitosamente en la plataforma <strong>PS-EDU</strong> 
            de la Facultad de Educación de la UNCP.
        </p>
        
        <p>
            <strong>Rol asignado:</strong> {{ $roleName }}
        </p>
    </div>
    
    <div class="credentials-box">
        <p><strong>📧 Email:</strong></p>
        <p class="credential-value">{{ $userEmail }}</p>
        
        <div style="height: 12px;"></div>
        
        <p><strong>🔑 Contraseña temporal:</strong></p>
        <p class="credential-value">{{ $temporaryPassword }}</p>
    </div>
    
    <div class="alert-box">
        <p>
            <strong>⚠️ Importante:</strong> Por seguridad, te recomendamos cambiar tu contraseña 
            después del primer inicio de sesión.
        </p>
    </div>
    
    <div class="button-container">
        <a href="{{ $loginUrl }}" class="button">
            Iniciar Sesión
        </a>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <p style="font-size: 14px; color: #6b7280;">
            Si tienes alguna duda o problema para acceder, contacta con el administrador del sistema 
            en <a href="mailto:upeducacionuncp@gmail.com" style="color: #2563eb;">upeducacionuncp@gmail.com</a>
        </p>
    </div>
@endsection
