<!DOCTYPE html>
<html lang="en">
<head>
@include('admin.css')
<style type="text/css">
    .div_center{
        text-align:center;
        padding-top:40px;
    }

    .center{
        margin: auto;
        width: 50%;
        text-align: center;
        margin-top: 30px;
        border: 3px solid white;
    }
    .input_color{
        color:black;
    }

    .h2_font{
        font-size:40px;
        padding-bottom: 40px;
    }
</style>
</head>
<body>
<div class="container-scroller">
<!-- partial:partials/_sidebar.html -->
@include('admin.sidebar')
<!-- partial -->
@include('admin.header')

<div class="main-panel">
    <div class="content-wrapper">

    <!-- this code is for show message from controller -->
    @if(session()->has('message') )
    <div class="alert alert-success">
        <!-- this code is for close the above message -->
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"> x </button>
        {{ session()->get('message') }}
    </div>
    @endif
        <div class="div_center">
            <h2 class="h2_font">Catagory</h2>

            <form action="{{ url('/add_catagory') }}" method="POST">
                @csrf
                <input class="input_color" type="text" name="name" placeholder="Write Catagory Name">
                <input class="btn btn-primary" type="submit" name="submit" value="Add Catagory" >
            </form>
        </div>


        <!-- now show data from db -->
        <table class="center">
            <tr>
                <td>Catagory Name</td>
                <td>Action</td>
            </tr>
            
            @foreach($data as $data)

            <tr>
                <td>{{ $data->catagory_name }}</td>
                <td><a onclick="return confirm('Are You Sure To Delete This')" class="btn btn-danger" href="{{ url('delete_catagory',$data->id) }}">Delete</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

<!-- container-scroller -->
@include('admin.script')
</body>
</html>