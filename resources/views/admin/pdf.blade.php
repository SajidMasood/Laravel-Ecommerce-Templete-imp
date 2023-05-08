<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order PDF</title>
</head>
<body>
    <h1>Order Details</h1>

    <h3>Customer Name : {{ $order->name }} </h3>
    <h3>Customer Email : {{ $order->email }} </h3>
    <h3>Customer Phone : {{ $order->phone }} </h3>
    <h3>Customer Address : {{ $order->address }} </h3>
    <h3>Customer ID : {{ $order->user_id }} </h3>

    <h3>Product Name : {{ $order->product_title }} </h3>
    <h3>Product Quantity : {{ $order->quantity }} </h3>
    <h3>Payment Status : {{ $order->payment_status }} </h3>
    <h3>Product ID : {{ $order->product_id }} </h3>

    <!-- image -->
    <br><br>
    <img src="product/{{$order->image}}" alt="" height="250" width="250">
</body>
</html>