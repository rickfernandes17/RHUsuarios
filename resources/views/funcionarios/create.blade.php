@extends('layouts.app')

@section('title', 'Cadastrar Funcionário - RHUsuarios')

@section('content')
<div class="header-actions">
    <div>
        <h1 class="page-title">Cadastrar Novo Funcionário</h1>
        <p class="page-subtitle">Insira as informações básicas. Ao salvar, a respectiva caixa de e-mail corporativo será provisionada no PostfixAdmin.</p>
    </div>
    <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">
        Voltar para a Listagem
    </a>
</div>

<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <form action="{{ route('funcionarios.store') }}" method="POST">
            @csrf
            
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; font-weight: 700; color: #fff;">
                Dados Pessoais & Cargo
            </h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: João" value="{{ old('nome') }}" required>
                </div>
                <div class="form-group">
                    <label for="sobrenome" class="form-label">Sobrenome</label>
                    <input type="text" name="sobrenome" id="sobrenome" class="form-control" placeholder="Ex: Silva" value="{{ old('sobrenome') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control" placeholder="Ex: 000.000.000-00" value="{{ old('cpf') }}" required>
                </div>
                <div class="form-group">
                    <label for="cargo" class="form-label">Cargo / Função</label>
                    <input type="text" name="cargo" id="cargo" class="form-control" placeholder="Ex: Analista de TI" value="{{ old('cargo') }}" required>
                </div>
            </div>

            <h3 style="margin-top: 2rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; font-weight: 700; color: #fff;">
                Conta de E-mail Corporativo (PostfixAdmin)
            </h3>

            <div class="form-group">
                <label for="empresa_id" class="form-label">Selecione a Empresa do Funcionário</label>
                @if($empresas->isEmpty())
                    <div style="padding: 1rem; border-radius: var(--radius-md); background-color: var(--error-bg); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--error);">
                        Nenhuma empresa cadastrada no sistema. <a href="{{ route('empresas.create') }}" style="color: #fff; text-decoration: underline; font-weight: 600;">Cadastre uma empresa primeiro</a> antes de prosseguir.
                    </div>
                @else
                    <select name="empresa_id" id="empresa_id" class="form-select" required>
                        <option value="" disabled selected>-- Selecione uma Empresa --</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}" data-domain="{{ $empresa->dominio }}" {{ (old('empresa_id') == $empresa->id || request('empresa_id') == $empresa->id) ? 'selected' : '' }}>
                                {{ $empresa->nome }} (@<span>{{ $empresa->dominio }}</span>)
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email_local" class="form-label">E-mail Corporativo</label>
                    <div class="email-input-group">
                        <input type="text" name="email_local" id="email_local" class="form-control" placeholder="Ex: joao.silva" value="{{ old('email_local') }}" required>
                        <span class="email-domain-addon" id="email_domain_display">@<span>selecione-empresa.com</span></span>
                    </div>
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">
                        Use apenas letras minúsculas, números, pontos e traços. O domínio será adicionado automaticamente.
                    </small>
                </div>

                <div class="form-group">
                    <label for="email_password" class="form-label">Senha do E-mail</label>
                    <div class="password-generator-container">
                        <input type="text" name="email_password" id="email_password" class="form-control" placeholder="Senha do e-mail" value="{{ old('email_password') }}" required>
                        <button type="button" class="btn btn-secondary" id="btn_generate_password" style="white-space: nowrap;">
                            Gerar Senha
                        </button>
                    </div>
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">
                        A senha será criptografada automaticamente em MD5 para gravação no PostfixAdmin.
                    </small>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2.5rem; justify-content: flex-end;">
                <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" {{ $empresas->isEmpty() ? 'disabled' : '' }}>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="margin-right: 4px;">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Cadastrar Funcionário
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const empresaSelect = document.getElementById('empresa_id');
        const emailLocalInput = document.getElementById('email_local');
        const emailDomainDisplay = document.getElementById('email_domain_display');
        const btnGeneratePassword = document.getElementById('btn_generate_password');
        const emailPasswordInput = document.getElementById('email_password');
        const nomeInput = document.getElementById('nome');
        const sobrenomeInput = document.getElementById('sobrenome');

        // Atualiza a exibição do domínio quando a empresa é selecionada
        function updateDomainDisplay() {
            const selectedOption = empresaSelect.options[empresaSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const domain = selectedOption.getAttribute('data-domain');
                emailDomainDisplay.innerHTML = `@${domain}`;
                emailDomainDisplay.style.color = 'var(--text-primary)';
            } else {
                emailDomainDisplay.innerHTML = '@<span>selecione-empresa.com</span>';
                emailDomainDisplay.style.color = 'var(--text-muted)';
            }
        }

        // Sugere e-mail local a partir de nome e sobrenome
        function suggestEmailLocal() {
            if (!emailLocalInput.value) { // Só sugere se o usuário ainda não tiver digitado nada
                const nome = nomeInput.value.trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^a-z0-9]/g, "");
                const sobrenome = sobrenomeInput.value.trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^a-z0-9]/g, "");
                
                if (nome && sobrenome) {
                    emailLocalInput.value = `${nome}.${sobrenome}`;
                } else if (nome) {
                    emailLocalInput.value = nome;
                }
            }
        }

        // Gera uma senha aleatória segura
        function generatePassword() {
            const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*()_-+=";
            let password = "";
            for (let i = 0; i < 12; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            emailPasswordInput.value = password;
        }

        // Limpa e-mail local de caracteres inválidos ao digitar
        emailLocalInput.addEventListener('input', function() {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9._-]/g, '');
        });

        empresaSelect.addEventListener('change', updateDomainDisplay);
        nomeInput.addEventListener('blur', suggestEmailLocal);
        sobrenomeInput.addEventListener('blur', suggestEmailLocal);
        btnGeneratePassword.addEventListener('click', generatePassword);

        // Inicializa a exibição se houver valor previamente selecionado (Ex. recarregamento de página por validação)
        updateDomainDisplay();
    });
</script>
@endsection
