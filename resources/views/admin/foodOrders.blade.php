@extends('layouts.adminbase')

@section('title', 'Speakers')

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
                    {{-- <h1 class="mt-4">Dashboard</h1> --}}
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Activities</li>
                    </ol>
                    <div class="row">
                        @if (session()->has('success'))
                            <div class="arlert alert-success">
                                <button class="close" type="button" data-dismiss="alert">X</button>
                                {{ session()->get('success') }}
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="arlert alert-danger">
                                <button class="close" type="button" data-dismiss="alert">X</button>
                                {{ session()->get('error') }}
                            </div>
                        @endif
                    </div>

                    <div class="container">
                        <h1>All Orders</h1>
                
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Names</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Ordered Items</th>
                                    <th>Total Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->created_at }}</td>
                                        <td>{{ $order->names }}</td>
                                        <td>{{ $order->phone }}</td>
                                        <td>{{ $order->address }}</td>
                                        <td>
                                            <ul>
                                                @foreach($order->items as $item)
                                                    <li>{{ $item->product }} ({{ $item->quantity }} x {{ $item->price }} RWf)</li>
                                                    <li hidden="">PrepLocation: {{ $item->preplocation }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>{{ $order->total }}</td>
                                        <td>{{ $order->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody> --}}
                        </table>
                    </div>

                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>

@section('scripts')



@endsection
