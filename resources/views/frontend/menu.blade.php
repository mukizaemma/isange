@extends('layouts.frontbase')

@section('content')

    <!-- Page Header Start -->
    <div class="container-fluid page-header parallax-bg py-5 mb-5" style="background-image: url('{{asset('assets')}}/img/welcome.jpg'); background-size: 100% 100%; background-position: center; object-fit:cover;">
        <div class="container py-5">
            @if ($categories->count() > 0)
            <h1 class="display-3 text-white mb-3 animated slideInDown text-center">Our Menu</h1><br>           
            @endif
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
    
                </ol>
            </nav>
        </div>
        </div>
        <!-- Page Header End -->

<div class="container-xxl py-5  mb-50">
    <div class="container">

        <!-- resources/views/menu/index.blade.php -->

@section('content')
<div class="container">
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

    <div class="row mb-5" style="background-color:#light;">
        <form method="GET" action="{{ route('OurMenu') }}">
            <label for="menucategory_id">Select a category:</label>
            <select name="menucategory_id" id="menucategory_id"  style="background-color:#134045; color:#fff; padding:5px;">
               <option value="">All</option>
                @foreach ($categories as $menuCategory)
                    <option value="{{ $menuCategory->id }}">{{ $menuCategory->name }}</option>
                @endforeach
            </select>
            <button type="submit" style="background-color:#134045; color:#fff; padding:5px;">Show Menu Items</button>
            <a href="{{ route('showCart') }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Cart[{{ $cartCount }}]</a>
        </form>
    
        @if (isset($menuItems))
            @if($menuItems->count() >0)
            <h3 style="color:warning">Menu for Selected Category </h2>
            @else
                
            @endif

 
            <div class="row" style="display:flex; flex-wrap: wrap; margin-top:10px;">
                @foreach ($menuItems as $menuitem)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card mb-3">
                        <img class="card-img-top" src="{{ asset('storage/images/menu/' . $menuitem->image) }}" alt="{{ $menuitem->name }}" style="height:200px;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $menuitem->name }}</h5>
                            <p class="card-text"  style="line-height: 0.5;"><strong>Ingredients:</strong>{!! $menuitem->description !!}</p>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="card-text"><strong>Price:</strong> {{ $menuitem->price }} RWf</p>
                                </div>
                                <div class="col-lg-12">
                                    <form action="{{ route('addCart', $menuitem->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $menuitem->name }}">
                                        <input type="hidden" name="price" value="{{ $menuitem->price }}">
                                        <input type="hidden" name="preplocation" value="{{ $menuitem->preplocation }}">
                                        <input type="number" name="qty" value="1" min="1" style="width:50px;">
                                        <button type="submit" class="btn btn-primary">Order</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
            <div class="row" style="display:flex; flex-wrap: wrap; margin-top:10px;">
                <h3 style="color:warning">All Items </h2>
                @foreach ($randomMenuItems as $menuitem)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card mb-3">
                        <img class="card-img-top" src="{{ asset('storage/images/menu/' . $menuitem->image) }}" alt="{{ $menuitem->name }}" style="height:200px;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $menuitem->name }}</h5>
                            <p class="card-text"><strong>Ingredients:</strong>{!! $menuitem->description !!}</p>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="card-text"><strong>Price:</strong> {{ $menuitem->price }} RWf</p>
                                </div>
                                <div class="col-lg-12">
                                    <form action="{{ route('addCart', $menuitem->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $menuitem->name }}">
                                        <input type="hidden" name="price" value="{{ $menuitem->price }}">
                                        <input type="number" name="qty" value="1" min="1" style="width:50px;">
                                        <button type="submit" class="btn btn-primary">Order</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
      </div>
      
    </div>
    
    

    {{-- <div class="row">
        <ul class="nav nav-tabs">
            @foreach($categories as $category)
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#{{ $category->slug }}">{{ $category->name }}</a>
                </li>
            @endforeach
        </ul>
        @foreach($menuItems as $menuitem)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card mb-3">
                    <img class="card-img-top" src="{{ asset('storage/images/menu/' . $menuitem->image) }}" alt="{{ $menuitem->name }}" style="height:200px;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $menuitem->name }}</h5>
                        <p class="card-text"><strong>Ingredients:</strong>{!! $menuitem->description !!}</p>
                        <div class="row">
                            <div class="col-lg-12">
                                <p class="card-text"><strong>Price:</strong> {{ $menuitem->price }}</p>
                            </div>
                            <div class="col-lg-12">
                                <a href="#" class="btn btn-primary">Order Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div> --}}
</div>
@endsection


    </div>
</div>

{{-- @include('frontend.layouts.reservation') --}}
@endsection