

@extends('layouts.frontbase')

@section('content')

    <!-- Page Header Start -->
    <div class="container-fluid page-header parallax-bg py-5 mb-5" style="background-image: url('{{asset('assets')}}/img/welcome.jpg'); background-size: 100% 100%; background-position: center; object-fit:cover;">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6">
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
            </div>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
                    k
                </ol>
            </nav>
        </div>
        </div>
        <!-- Page Header End -->

<div class="container-xxl py-5  mb-50">
    @section('content')
    <div class="container">
        <h1>Cart</h1>
        <div class="row">
            <div class="col-lg-6 col-sm-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($cartItems as $item)

                        @php
                            $subtotal = $item->price * $item->quantity;
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>
                                <input type="text" name="product" hidden="" value="{{ $item->product }}">
                                <input type="text" name="preplocation" hidden="" value="{{ $item->preplocation }}">
                                {{ $item->product }} - {{ $item->preplocation }}
                            </td>
                            <td>
                                <input type="text" name="price" hidden="" value="{{ $item->price }}">
                                {{ $item->price }} RWf</td>
                            <td>
                                <form action="{{ route('cart', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="quantity" hidden="" value="{{ $item->quantity }}">
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" class="form-control" style="width: 70px; display: inline;">
                                    <button type="submit" class="btn btn-link">Update</button>
                                </form>
                            </td>
                            <td>{{ $subtotal }} RWf</td>
                            <td>
                                <a href="{{ route('removeFood', $item->id) }}" onclick="return confirm('Are you sure to remove this item from your cart?')"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                            <td>{{ $total }} RWf</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <a href="{{ route('OurMenu') }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i>Add more Items</a>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer's Contacts</h5>
                        <form class="form" action="{{ route('confirmOrder') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">            
                            <div class="row mb-3">
                                <div class="col-lg-6 col-sm-12">
                                    <label for="title" class="form-label">Customer Names</label>
                                    <input type="text" name="names" class="form-control"
                                        id="title" value="{{ auth()->user()->name }}" required="">
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="title" class="form-label">Customer Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                        id="title" placeholder="Cell Phone" required="">
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="title" class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control"
                                        id="title" placeholder="Eg. Kicukiro, Kagarama, kk434 " required="">
                                </div>

                            </div>
            
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="summernote" class="form-label">Any other request please?</label>
                                    {{-- <textarea class="form-control" id="blogBody" rows="5" name="body"></textarea> --}}
                                    <textarea id="activity" rows="5" class="form-control" name="description"></textarea>
                                </div>
                            </div>
            
                        </div>
            
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary text-black">
                                <i class="fa fa-save"></i> Confirm Your Order
                            </button>
            
                        </div>
                    </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">


        </div>
    </div>
    @endsection


</div>

{{-- @include('frontend.layouts.reservation') --}}
@endsection