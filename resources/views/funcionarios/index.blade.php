@extends('layouts.app')

@section('title', 'Funcionários - RHUsuarios')

@section('content')
<div class="header-actions">
    <div>
        <h1 class="page-title">Quadro de Funcionários</h1>
        <p class="page-subtitle">Visualize os funcionários cadastrados no sistema e suas respectivas caixas de e-mail integradas ao PostfixAdmin.</p>
    </div>
    <a href="{{ route('funcionarios.create') }}" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="margin-right: 4px;">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Cadastrar Funcionário
    </a>
</div>

<div class="card">
    @if($funcionarios->isEmpty())
        <div style="text-align: center; padding: 2rem 0; color: var(--text-secondary);">
            <svg width="60" height="60" viewBox="0 0 20 20" fill="currentColor" style="margin-bottom: 1rem; color: var(--text-muted);">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
            </svg>
            <p style="font-size: 1.1rem; font-weight: 600;">Nenhum funcionário cadastrado</p>
            <p style="font-size: 0.9rem; margin-top: 0.25rem;">Cadastre um novo funcionário para provisionar automaticamente uma conta no PostfixAdmin.</p>
            <a href="{{ route('funcionarios.create') }}" class="btn btn-primary" style="margin-top: 1.5rem;">Cadastrar Primeiro Funcionário</a>
        </div>
    @else
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nome Completo</th>
                        <th>Empresa</th>
                        <th>Cargo</th>
                        <th>E-mail Corporativo</th>
                        <th>Status E-mail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($funcionarios as $funcionario)
                        <tr>
                            <td style="font-weight: 700; color: var(--text-secondary);">#{{ $funcionario->id }}</td>
                            <td style="font-weight: 600; color: #fff;">
                                {{ $funcionario->nome }} {{ $funcionario->sobrenome }}
                                <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: normal; margin-top: 0.25rem;">
                                    CPF: {{ $funcionario->cpf }}
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 500; font-size: 0.9rem;">
                                    {{ $funcionario->empresa->nome }}
                                </span>
                            </td>
                            <td>{{ $funcionario->cargo }}</td>
                            <td>
                                <span style="color: var(--accent); font-weight: 600; font-size: 0.95rem;">
                                    {{ $funcionario->email_corporativo }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" style="margin-right: 4px;">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Criado e Ativo
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
