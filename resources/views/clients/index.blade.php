@extends('layouts.app', ['title' => __('Clients')])

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Clients') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('clients.export') }}" class="btn btn-sm btn-outline-primary">{{ __('Export CSV') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        @include('partials.flash')
                    </div>

                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Email') }}</th>
                                    <th scope="col">{{ __('Creation Date') }}</th>
                                    @if(config('settings.enable_birth_date_on_register'))
                                        <th scope="col">{{ __('Birth Date') }}</th>
                                    @endif
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td>
                                            <img class="avatar avatar-md rounded-circle mr-3" src="https://www.gravatar.com/avatar/{{ md5($client->email) }}?d=mp" />
                                            <a href="{{ route('clients.edit', $client) }}"> {{ $client->name }}</a></td>
                                        <td>
                                            <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                                        </td>
                                        <td>{{ $client->created_at->format(config('settings.datetime_display_format')) }}</td>
                                        <td class="text-right">
                                            
                                            <div class="dropdown">
                                               
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a href="{{ route('clients.edit', $client) }}" type="button" class="dropdown-item">
                                                        {{ __('Info') }}
                                                    </a>
                                                    @hasrole('admin')
                                                        <form action="{{ route('clients.destroy', $client) }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to deactivate this user?") }}') ? this.parentElement.submit() : ''">
                                                                {{ __('Deactivate') }}
                                                            </button>
                                                        </form>
                                                    @endhasrole
                                                </div>
                                                
                                            </div> 
                                            
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-4">
                        <nav class="d-flex justify-content-end" aria-label="...">
                            {{ $clients->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
