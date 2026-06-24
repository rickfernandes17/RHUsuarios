@extends('layouts.app')

@section('title', 'Cadastrar Empresa - RHUsuarios')

@section('content')
<div class="header-actions">
    <div>
        <h1 class="page-title">Cadastrar Nova Empresa</h1>
        <p class="page-subtitle">Insira o nome da empresa e o domínio de e-mail exclusivo a ser provisionado no PostfixAdmin.</p>
    </div>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary">
        Voltar para a Listagem
    </a>
</div>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <form action="{{ route('empresas.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nome" class="form-label">Nome da Empresa</label>
                <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Acme Corporation Ltda" value="{{ old('nome') }}" required>
            </div>

            <div class="form-group">
                <label for="dominio" class="form-label">Domínio de E-mail</label>
                <div class="email-input-group">
                    <span class="email-domain-addon" style="border-top-left-radius: var(--radius-md); border-bottom-left-radius: var(--radius-md); border-right: none; border-left: 1px solid var(--border-color);">@</span>
                    <input type="text" name="dominio" id="dominio" class="form-control" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" placeholder="Ex: acme.com.br" value="{{ old('dominio') }}" required>
                </div>
                <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">
                    Não digite o símbolo '@', apenas o domínio (ex: acme.com ou acme.com.br). Este domínio será usado para criar as caixas de e-mail.
                </small>
            </div>

            <div class="form-group">
                <label for="ou_dn" class="form-label">OU no Active Directory</label>
                <input
                    type="text"
                    name="ou_dn"
                    id="ou_dn"
                    class="form-control"
                    placeholder="Ex: OU=Empresas,DC=empresa,DC=local"
                    value="{{ old('ou_dn', 'OU=Empresas,DC=meudominio,DC=local') }}"
                    required>

                <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">
                    Informe a OU pai onde será criada a OU desta empresa.
                    Exemplo: OU=Empresas,DC=empresa,DC=local
                </small>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2.5rem; justify-content: flex-end;">
                <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="margin-right: 4px;">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Cadastrar Empresa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection