<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'PS-EDU - FAEDU' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f3f4f6;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 20px;
        }
        
        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .email-header p {
            color: #e0e7ff;
            font-size: 14px;
            margin-top: 8px;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .email-greeting {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        .email-content {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .email-content p {
            margin-bottom: 16px;
        }
        
        .credentials-box {
            background-color: #f9fafb;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .credentials-box p {
            margin: 8px 0;
            font-size: 15px;
        }
        
        .credentials-box strong {
            color: #1f2937;
            font-weight: 600;
        }
        
        .credentials-box .credential-value {
            color: #2563eb;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 600;
        }
        
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .alert-box p {
            color: #92400e;
            font-size: 14px;
            margin: 0;
        }
        
        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .info-box p {
            color: #1e40af;
            font-size: 14px;
            margin: 0;
        }
        
        .button-container {
            text-align: center;
            margin: 32px 0;
        }
        
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s;
        }
        
        .button:hover {
            transform: translateY(-2px);
        }
        
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .email-footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 8px 0;
        }
        
        .email-footer a {
            color: #2563eb;
            text-decoration: none;
        }
        
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 24px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-body {
                padding: 30px 20px;
            }
            
            .email-footer {
                padding: 20px;
            }
            
            .email-logo {
                max-width: 140px;
            }
            
            .email-header h1 {
                font-size: 20px;
            }
            
            .button {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header con Logo -->
        <div class="email-header">
            <img src="{{ $message->embed(public_path('logo/logo-educacion.png')) }}" alt="Logo FAEDU" class="email-logo">
            <h1>PS-EDU</h1>
            <p>Facultad de Educación - UNCP</p>
        </div>
        
        <!-- Contenido del Email -->
        <div class="email-body">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>Facultad de Educación - UNCP</strong></p>
            <p>Sistema de Gestión Académica PS-EDU</p>
            <p>Email: <a href="mailto:upeducacionuncp@gmail.com">upeducacionuncp@gmail.com</a></p>
            <p style="margin-top: 16px; font-size: 12px; color: #9ca3af;">
                Este es un email automático, por favor no responder a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
