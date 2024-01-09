@extends('layouts.front', ['class' => ''])

@section('content')
   <section class="section">
    <div class="container">
        <br /><br />
        <div class="alert alert-danger" role="alert">
            Install is ok. But looks like you are running the site under subdomain. 
        </div>
        <p>
            When you run the site in subdomain, you need to declare that subdomain in Site setting->Setup->Subdomains and add your domain there<br />
        <br />
        <a href="https://mobidonia.gitbook.io/qr-menu-maker/faq/faq#install-the-project-in-subdomain" type="button" class="btn btn-success">Read more in the docs</a>
        </p>
    </div>
   </section>
@endsection