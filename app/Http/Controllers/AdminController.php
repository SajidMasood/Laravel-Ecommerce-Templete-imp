<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catagory;
use App\Models\Product;
use App\Models\Order;
use PDF;
use Notification;
use App\Notifications\SendEmailNotification;

class AdminController extends Controller
{
    public function view_catagory(){

        //show data from db| import Model class 
        $data = catagory::all();
        return view('admin.catagory', compact('data') );
    }

    // first add Model class
    public function add_catagory(Request $req){
        // to get from db table column name 
        $data = new catagory;
        // table column name = html form name
        $data->catagory_name=$req->name;
        // insert
        $data->save();
        // same page
        return redirect()-> back()->with('message','Catagory added successfully');
    }


    // to delete catagory by id
    public function delete_catagory($id){
        $data = catagory::find($id);
        $data->delete();
        return redirect()->back()->with('message','Catagory Deleted Successfully!');
    }



    // add product...
    public function view_product(){
        // get all data from db 
        $catagory = catagory::all();
        return view('admin.product', compact('catagory') );
    }
    // insert in db
    public function add_product(Request $req){
        // first import model ... product
        $product = new product;
        $product->title = $req->title;
        $product->description = $req->description;
        $product->price = $req->price;
        $product->quantity = $req->quantity;
        $product->discount_price = $req->dis_price;
        $product->catagory = $req->catagory;
        // for image
        $img = $req->image;
        $imgName = time().'.'.$img->getClientOriginalExtension();
        $req->image->move('product', $imgName);
        $product->image = $imgName;

        $product->save();
        return redirect()->back()->with('message','Product Added Successfully...');
    }

    // show product
    public function show_product(){
        $product = product::all();
        return view('admin.show_product', compact('product') );
    }

    // delete product
    public function delete_product($id){
        // import models class
        $product = product::find($id);
        $product->delete();
        return redirect()->back()->with('message','Product Deleted Successfully...');
    }

    // update product...
    public function update_product($id){
        $product = product::find($id);
        $catagory = catagory::all();
        return view('admin.update_product' , compact('product','catagory') );
    }
    public function update_product_confirm(Request $req, $id){
        $product = product::find($id);
        $product->title = $req->title;
        $product->description = $req->description;
        $product->price = $req->price;
        $product->quantity = $req->quantity;
        $product->discount_price = $req->dis_price;
        $product->catagory = $req->catagory;
        // for image
        $image  = $req->image;
        if ($image) {
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $req->image->move('product',$imageName);
            $product->image = $imageName;
        }
        $product->save();
        return redirect()->back()->with('message','Product Updated Successfully...');
    }


    public function order(){
        $order = order::all();

        return view('admin.order', compact('order'));
    }

    public function delivered($id){
        $order = order::find($id);
        $order->delivery_status = "delivered";
        $order->payment_status = "Paid";
        $order->save();
        return redirect()->back();
    }

    public function print_pdf($id){
        // now get order details...
        $order = order::find($id);
        // this only for pdf... 
        $pdf = PDF::loadView('admin.pdf',compact('order'));
        return $pdf->download('order_details.pdf');
    }

    // send email
    public function send_email($id){
        $order = order::find($id);
        return view('admin.send_email', compact('order'));
    }


    public function send_user_email(Request $request,$id){
        $order = order::find($id);
        $details = [
            'greeting' => $request->greeting,
            'firstline' => $request->firstline,
            'body' => $request->body,
            'button' => $request->button,
            'url' => $request->url,
            'lastline' => $request->lastline,
        ];

        Notification::send($order, new SendEmailNotification($details));
        return redirect()->back();
    }
}
