<!DOCTYPE html>
<html lang="en">
  <head>
    @include('admin.css')

    <style type="text/css">
        .title_deg{
            text-align:center;
            font-size:25px;
            font-weight:bold;
            padding-bottom:40px;
        }
        .table_deg{
            border:2px solid white;
            width:100%;
            margin:auto;
            
            text-align:center;
        }
        .th_deg{
            background-color:skyblue;
        }
        table, th, td {
  border: 1px solid;
}
.image{
    width:100px;
    height:100px;
}
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      @include('admin.sidebar')
      <!-- partial -->
      @include('admin.header')
        <!-- partial -->
    
        <div class="main-panel">
            <div class="content-wrapper">

            <div>
                <h1 class="title_deg">
                    All Orders
                </h1>
            </div>


            <div>
                <table class="table_deg">
                    <tr class="th_deg">
                        <th style="padding:10px;">Name </th>
                        <th style="padding:10px;">Email</th>
                        <th style="padding:10px;">Address</th>
                        <th style="padding:10px;">Phone</th>
                        <th style="padding:10px;">Product Title</th>
                        <th style="padding:10px;">Quantity</th>
                        <th style="padding:10px;">Price</th>
                        <th style="padding:10px;">Payment Status</th>
                        <th style="padding:10px;">Delivery Status</th>
                        <th style="padding:10px;">Image</th>
                        <th style="padding:10px;">Delivered</th>
                        <th style="padding:10px;">Print PDF</th>
                        <th style="padding:10px;">Send Email</th>
                    </tr>

                    @foreach($order as $order)
                    <tr>
                        <td>{{ $order->name }} </td>
                        <td>{{ $order->email }} </td>
                        <td>{{ $order->address }} </td>
                        <td>{{ $order->phone }} </td>
                        <td>{{ $order->product_title }} </td>
                        <td>{{ $order->quantity }} </td>
                        <td>{{ $order->price }} </td>
                        <td>{{ $order->payment_status }} </td>
                        <td>{{ $order->delivery_status }} </td>
                        <td>
                            <img class="image" src="/product/{{$order->image}}" alt="">
                        </td>
                        <td>
                            @if($order->delivery_status == 'Processing')
                            <a onclick="return confirm('Are You sure this Product is Delivered!!!')" class="btn btn-primary" href="{{ url('delivered',$order->id) }}">
                                Delivered
                            </a>
                            @else
                            <p style="color:green;">Delivered </p>
                            @endif
                        </td>
                        <td>
                            <a href="{{ url('print_pdf',$order->id) }}" class="btn btn-secondary">Print PDF</a>
                        </td>
                        <td>
                            <a href="{{url('send_email',$order->id)}}" class="btn btn-info">Send Email</a>
                        </td>

                    </tr>
                    @endforeach

                </table>
            </div>

            </div>
        </div>


    <!-- container-scroller -->
    @include('admin.script')
  </body>
</html>