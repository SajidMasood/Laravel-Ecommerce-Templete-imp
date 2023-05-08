<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use Session;
use Stripe;
class HomeController extends Controller
{
    public function index(){
        // first import model for product then show which page...
        // $product = Product::all();
        // paginating
        $product = Product::paginate(9);
        return view('home.userpage', compact('product'));
    }

    // this is for admin or user dashboard...
    public function redirect(){
        // get column data from db
        $usertype = Auth::user()->usertype;
        
        if ($usertype == '1') {
            return view('admin.home');
        } else {
            $product = Product::paginate(9);
        return view('home.userpage', compact('product'));
        }
    }




    public function product_details($id){
        $product = product::find($id);
        return view('home.product_details', compact('product') );
    }

    public function add_cart(Request $request, $id){
        // import Auth class
        if (Auth::id()) {
            $user = Auth::user();
            //dd($user);
            $product = product::find($id);
            //dd($product);
            $cart = new cart;
            $cart->user_id = $user->id;
            $cart->name = $user->name;
            $cart->email = $user->email;
            $cart->phone = $user->phone;
            $cart->address = $user->address;

            $cart->product_title = $product->title;
            if ($product->discount_price != null) {
                $cart->price = $product->discount_price * $request->quantity;
            } else {
                $cart->price = $product->price * $request->quantity;    
            }
            
            $cart->image = $product->image;
            $cart->product_id = $product->id;

            $cart->quantity = $request->quantity;

            $cart->save();

            return redirect()->back();
        } else {
            return redirect('login');
        }
    }



    public function show_cart(){
        // if user click on cart first check login
        if (Auth::id()) {
            $id = Auth::user()->id;
            $cart = cart::where('user_id','=',$id)->get();
            return view('home.show_cart', compact('cart') );    
        } else {
            return redirect('login');
        }
        
    }

    public function remove_cart($id){
        $cart = cart::find($id);
        $cart->delete();
        return redirect()->back();
    }

    public function cash_order(){
        // import model class order | then check login
        $user = Auth::user();
        $userid = $user->id;
        // dd($userid);

        // get same user id from cart table
        $data = cart::where('user_id','=',$userid)->get();
        //dd($data);

        // store multiple data in another table 
        foreach($data as $data){
            $order = new order;
            $order->name = $data->name;
            $order->email = $data->email;
            $order->phone = $data->phone;
            $order->address = $data->address;
            $order->user_id = $data->user_id;

            $order->product_title = $data->product_title;
            $order->quantity = $data->quantity;
            $order->price = $data->price;
            $order->image = $data->image;
            $order->product_id = $data->product_id;

            $order->payment_status ='Cash on Delivery';
            $order->delivery_status = 'Processing';

            $order->save();
            // now remove this product data from cart table
            $cart_id = $data->id;
            $cart = cart::find($cart_id);
            $cart->delete();
            
        }
        return redirect()->back()->with('message','We have Received your Order. We will connect with you soon...');
    }

    //  stripe
    public function stripe($totalprice){
        return view('home.stripe', compact('totalprice') );
    }

    public function stripePost(Request $request, $totalprice ){
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create([
            "amount" => $totalprice * 100,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => "Thanks for payment.",
        ]);

        // start
        // import model class order | then check login
        $user = Auth::user();
        $userid = $user->id;
        // dd($userid);

        // get same user id from cart table
        $data = cart::where('user_id','=',$userid)->get();
        //dd($data);

        // store multiple data in another table 
        foreach($data as $data){
            $order = new order;
            $order->name = $data->name;
            $order->email = $data->email;
            $order->phone = $data->phone;
            $order->address = $data->address;
            $order->user_id = $data->user_id;

            $order->product_title = $data->product_title;
            $order->quantity = $data->quantity;
            $order->price = $data->price;
            $order->image = $data->image;
            $order->product_id = $data->product_id;

            $order->payment_status ='Paid';
            $order->delivery_status = 'Processing';

            $order->save();
            // now remove this product data from cart table
            $cart_id = $data->id;
            $cart = cart::find($cart_id);
            $cart->delete();
            
        } 
        // end

        Session::flash('success','Payment successful!');
        return back();
    }
}
