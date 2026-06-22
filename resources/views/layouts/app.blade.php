<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'RHUsuarios - Sistema de Gestão de Contas')</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Folha de Estilos Customizados -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="{{ route('funcionarios.index') }}" class="navbar-brand">
                RH<span>Usuarios</span>
            </a>
            <ul class="navbar-nav">
                <li>
                    <a href="{{ route('funcionarios.index') }}" class="nav-link {{ Request::is('funcionarios*') ? 'active' : '' }}">
                        Funcionários
                    </a>
                </li>
                <li>
                    <a href="{{ route('empresas.index') }}" class="nav-link {{ Request::is('empresas*') ? 'active' : '' }}">
                        Empresas (Domínios)
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="app-container">
        
        <!-- Alertas de Feedback -->
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; font-weight:700;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Atenção: Por favor, corrija os erros abaixo.</span>
                    </div>
                    <ul class="alert-errors-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Rodapé -->
    <footer>
        <p>&copy; {{ date('Y') }} - RHUsuarios. Desenvolvido para Integração PostfixAdmin MySQL.</p>
    </footer>

    @yield('scripts')
</body>
</html>
