@extends('layouts.app')

@section('title', 'Empresas & Domínios - RHUsuarios')

@section('content')
<div class="header-actions">
    <div>
        <h1 class="page-title">Empresas e Domínios</h1>
        <p class="page-subtitle">Gerencie as empresas cadastradas e os domínios associados para a criação automatizada de e-mails.</p>
    </div>
    <a href="{{ route('empresas.create') }}" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="margin-right: 4px;">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Nova Empresa
    </a>
</div>

<div class="card">
    @if($empresas->isEmpty())
        <div style="text-align: center; padding: 2rem 0; color: var(--text-secondary);">
            <svg width="60" height="60" viewBox="0 0 20 20" fill="currentColor" style="margin-bottom: 1rem; color: var(--text-muted);">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 210-2H7a1 1 0 210 2H4a1 1 0 110-2V4zm2 9a1 1 0 100-2h6a1 1 0 100 2H6zm0-4a1 1 0 100-2h6a1 1 0 100 2H6z" clip-rule="evenodd" />
            </svg>
            <p style="font-size: 1.1rem; font-weight: 600;">Nenhuma empresa cadastrada</p>
            <p style="font-size: 0.9rem; margin-top: 0.25rem;">Cadastre uma empresa para começar a associar novos funcionários a domínios de e-mail.</p>
            <a href="{{ route('empresas.create') }}" class="btn btn-primary" style="margin-top: 1.5rem;">Cadastrar Primeira Empresa</a>
        </div>
    @else
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nome da Empresa</th>
                        <th>Domínio de E-mail</th>
                        <th>Funcionários Ativos</th>
                        <th style="text-align: right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresas as $empresa)
                        <tr>
                            <td style="font-weight: 700; color: var(--text-secondary);">#{{ $empresa->id }}</td>
                            <td style="font-weight: 600; color: #fff;">{{ $empresa->nome }}</td>
                            <td>
                                <span class="badge badge-neutral" style="font-size: 0.85rem; letter-spacing: normal; text-transform: none;">
                                    @<span>{{ $empresa->dominio }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $empresa->funcionarios_count }}</span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('funcionarios.create') }}?empresa_id={{ $empresa->id }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                    Adicionar Funcionário
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
