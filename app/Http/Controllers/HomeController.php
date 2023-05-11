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
use App\Models\Comment;
use App\Models\Reply;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    public function index(){
        // first import model for product then show which page...
        // $product = Product::all();
        // paginating
        $product = Product::paginate(9);
        // $comment = Comment::all();
        $comment = Comment::orderby('id','desc')->get();
        $reply = Reply::all();
        return view('home.userpage', compact('product','comment','reply'));
    }

    // this is for admin or user dashboard...
    public function redirect(){
        // get column data from db
        $usertype = Auth::user()->usertype;
        
        if ($usertype == '1') {
            // show some data from db in home page
            $total_product = Product::all()->count(); 
            $total_order = Order::all()->count(); 
            $total_user = User::all()->count(); 
            $order = Order::all();
            $total_revenue = 0;
            foreach($order as $order){
                $total_revenue = $total_revenue + $order->price;
            } 

            $total_delivered = Order::where('delivery_status', '=','delivered')->get()->count();

            $total_processing = Order::where('delivery_status', '=','processing')->get()->count();
            // goto home page
            return view('admin.home' , compact('total_product','total_order','total_user','total_revenue', 'total_delivered','total_processing'));
        } else {
            $product = Product::paginate(9);
            // $comment = Comment::all();
            $comment = Comment::orderby('id','desc')->get();
            $reply = Reply::all();
            return view('home.userpage', compact('product','comment','reply'));
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
            // get user login id
            $userid = $user->id;
            //dd($user);
            $product = product::find($id);
            //dd($product); | now existing product only increase quantity
            $product_exist_id = cart::where('product_id','=',$id)->where('user_id','=',$userid)->get('id')->first();
            if ($product_exist_id) {
                $cart = cart::find($product_exist_id)->first();
                $quantity = $cart->quantity;
                $cart->quantity = $quantity + $request->quantity;

                if ($product->discount_price != null) {
                    $cart->price = $product->discount_price * $cart->quantity;
                } else {
                    $cart->price = $product->price * $cart->quantity;    
                }

                $cart->save();
                return redirect()->back()->with('message','Product Added Successfully');
            } else{
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
                // sweet alert
                Alert::success('Product Added Successfully','We have added product to the cart');
                // return redirect()->back()->with('message','Product Added Successfully');
                return redirect()->back();
            }


            
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



    //search Product 
    public function product_search(Request $request){

        $comment = Comment::orderby('id','desc')->get();
        $reply = Reply::all();
        $search_text = $request->search;
        // $product = Product::where('title','LIKE','%$search_text%')->get();
        $product = Product::where('title','LIKE',"%$search_text%")->orWhere('catagory','LIKE',"$search_text")->paginate(10);
        return view('home.userpage', compact('product','comment','reply'));
    }


    public function show_order(){
         if(Auth::id()){
            // which user is login
            $user = Auth::user();
            $userid = $user->id;
            $order = order::where('user_id','=',$userid)->get();

            return view('home.order', compact('order'));
         } else {
            return redirect('login');
         }
    }



    public function cancel_order($id){
        $order = order::find($id);
        $order->delivery_status = 'You canceled the order';
        $order->save();
        return redirect()->back();
    }


    public function add_comment(Request $request){
        // check user is login or not 
        if (Auth::id()) {
            $comment = new comment;
            // get user name from auth
            $comment->name = Auth::user()->name;
            // get user id
            $comment->user_id = Auth::user()->id;
            // get comment from front 
            $comment->comment = $request->comment;
            $comment->save();
            return redirect()->back();
        } else {
            return redirect('login');
        }
    }

    public function add_reply(Request $request){
        if (Auth::id()) {
            $reply = new Reply;  
            $reply->name = Auth::user()->name;  
            $reply->user_id = Auth::user()->id;  
            $reply->comment_id = $request->commentId;  
            $reply->reply = $request->reply;  
            $reply->save();
            return redirect()->back();  
        } else{
            return redirect('login');
        }
    }

    public function products(){
        $product = Product::paginate(9);
        $comment = Comment::orderby('id','desc')->get();
        $reply = Reply::all();
        return view('home.all_product', compact('product','comment','reply'));
    }



    //search Product 
    public function search_product(Request $request){

        $comment = Comment::orderby('id','desc')->get();
        $reply = Reply::all();
        $search_text = $request->search;
        // $product = Product::where('title','LIKE','%$search_text%')->get();
        $product = Product::where('title','LIKE',"%$search_text%")->orWhere('catagory','LIKE',"$search_text")->paginate(10);
        return view('home.all_product', compact('product','comment','reply'));
    }

}
