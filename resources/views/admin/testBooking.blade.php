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
                        <li class="breadcrumb-item active">Booking</li>
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

                    <div class="card mb-4">
                        <form class="form" action="{{ route('roomTypeCreate') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">

                        <div class="row mt-3">
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <label for="name">Room</label>
                                <select class="form-control border-success" name="roomType" id="roomType" required="">
                                    <option>--Room Category--</option>
                                    @foreach(\Illuminate\Support\Facades\DB::table('room_types')->get() as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <label for="name">Check In Date</label>
                                <input type="date" id="checkin" class="form-control checkinDate" placeholder="CheckIn Date" name="checkin" required="">
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <label for="name">Check Out Date</label>
                                <input type="date" id="checkout" class="form-control checkoutDate" placeholder="CheckOut Date" name="checkout" required="">
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <label for="name">Availlable Rooms</label>
                                <select name="aRoom" class="roomList">

                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <label for="name">Number of Adults</label>
                                <input type="number" id="roomName" class="form-control" placeholder="When are you leaving ?" name="adults" required="">
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <label for="name">Number of Children</label>
                                <input type="number" id="roomName" class="form-control" placeholder="When are you leaving ?" name="children" required="">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <label for="name">Client Names</label>
                                <input type="text" id="roomName" class="form-control" placeholder="Type Room Name" name="name" required="">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-5 col-md-6 col-sm-6">
                                <label for="name">Client Email</label>
                                <input type="text" id="roomName" class="form-control" placeholder="Type Room Name" name="name" required="">
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <label for="name">Client Phone</label>
                                <input type="text" id="roomName" class="form-control" placeholder="Type Room Name" name="name" required="">
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <label for="name">Country</label>
                                <select name="country" id="">
                                    <option >--Select Country--</option>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                 <label for="summernote" class="form-label">Other Request</label>
                                    <textarea id="roomDescription" rows="5" class="form-control" name="description"></textarea>
                            </div>
                        </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary text-black">
                                <i class="fa fa-save"></i> Confirm Booking
                            </button>

                        </div>
                    </form>
                    </div>


                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>

@section('scripts')
<script type="text/javascript"> 
    $(document).ready(function(){
        $(".checkinDate").on('blur',function(){
            var chechindate = $(this).val();
            $.ajax({
                url:"{{ url('roomBookings') }}/availableRooms/" + chechindate,
                dataType:'json',
                type:'GET',
                success:function(data){
                    return response(data);
                }
                // beforeSend:function(){
                //     $(".roomList").html('<option>--- Loading ---</option>');
                // },
                // success:function(res){
                //     var _html='';
                //     $.each(res.data, function(index, row){
                //         _html+='<option value="'+row.id+'">'+row.roomName+'</option>';
                //     })
                //     $('.roomList').html(_html);
                // }
            })
        })
    })
</script>

@endsection


@endsection
