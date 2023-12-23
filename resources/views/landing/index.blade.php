@extends('layouts.app', ['title' => __('Orders')])
@section('admin_title')
@endsection
@section('content')
    <div class="header bg-primary pb-6 pt-5 pt-md-8">
    </div>
    <div class="container-fluid mt--6">
      <div class="row">
        <div class="col">
          <div class="card">
            <!-- Card header -->
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="mb-0">{{ __('Landing Page') }}</h3>
                </div>
                <div class="col text-right">
                  <div class="dropdown">
                    <a href="#" class="btn btn-default btn-sm dropdown-toggle " data-toggle="dropdown" id="navbarDropdownMenuLink2">
                        <!--<img src="{{ asset('images') }}/icons/flags/{{ strtoupper(config('app.locale'))}}.png" />--> {{ $currentLanguage }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                        @foreach ($availableLanguages as $languageKey => $languageName)
                        <li>
                            <a class="dropdown-item" href="?lang={{ strtolower($languageKey) }}">
                                <img src="{{ asset('images') }}/icons/flags/{{ strtoupper($languageKey)}}.png" /> {{$languageName}}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                </div>
              </div>
            </div>
            <!-- Light table -->
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col" class="sort" data-sort="name">{{ __('Sections') }}</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody class="list">
                    @foreach($sections as $key => $section)
                    <tr>
                        <th scope="row">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <a href="{{ route('admin.landing.posts',['type'=>strtolower($section)]) }}"><span class="name mb-0 text-sm">{{ __($key) }}</span></a>
                                </div>
                            </div>
                        </th>
                        <td class="text-right">
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
     
      </div>
    </div>
@endsection

