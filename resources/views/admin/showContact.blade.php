@extends('layouts.adminbase')

@section('title', 'Home Page')

@section('sidebar')

    @parent

@endsection

@section('content')

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4"></h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Inscriptions en ligne
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Noms</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Fonction /titre</th>
                                    <th>Société</th>
                                    <th>Secteur d'activité</th>
                                    <th>Pays</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $rs)
                                <tr>
                                    <td>{{$rs->created_at}}</td>
                                    <td>{{$rs->first_name}} {{$rs->last_name}}</td>
                                    <td>{{$rs->email}}</td>
                                    <td>{{$rs->phone}}</td>
                                    <td>{{$rs->function}}</td>
                                    <td>{{$rs->company}}</td>
                                    <td>{{$rs->field}}</td>
                                    <td>{{$rs->country}}</td>
                                    <td>{{$rs->message}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="" class="btn btn-primary"><i class="fa fa-pen"></i></a>
                                            <a href="" class="btn btn-warning"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Your Website 2021</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

@endsection
