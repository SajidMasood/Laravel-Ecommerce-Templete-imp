<section class="product_section layout_padding">
         <div class="container">
            <div class="heading_container heading_center">
               <h2>
                  Our <span>products</span>
               </h2>
            </div>
            <div class="row">

               @foreach($product as $pro)
               <div class="col-sm-6 col-md-4 col-lg-4">
                  <div class="box">
                     <div class="option_container">
                        <div class="options">
                           <a href="{{ url('product_details',$pro->id) }}" class="option1">
                           Product Details
                           </a>
                           
                           <!-- <a href="" class="option2">
                           Buy Now
                           </a> -->
                           <!-- if user login then addtocart other then login page -->
                           <form action="{{ url('add_cart',$pro->id) }}" method="POST">
                              @csrf
                              <div class="row">
                                 <div class="col-md-4">
                                    <input type="number" name="quantity" value="1" min="1" style="width:100px;">
                                 </div>
                                 <div class="col-md-4">
                                    <input type="submit" value="Add To Cart">
                                 </div>
                              </div>
                           </form>
                        </div>
                     </div>
                     <div class="img-box">
                        <img src="product/{{ $pro->image }}" alt="">
                     </div>
                     <div class="detail-box">
                        <h5>
                           {{$pro->title}}
                        </h5>

                        @if($pro->discount_price != null)
                        <h6 style="color:red;">
                           Discount Price : <br>
                           ${{$pro->discount_price}}
                        </h6>
                        
                        <h6 style="text-decoration:line-through; color:blue;">
                           Price : <br>
                           ${{$pro->price}}
                        </h6>
                        @else
                        <h6 style="color:blue;">
                           Price : <br>
                           ${{$pro->price}}
                        </h6>
                        @endif

                        
                     </div>
                  </div>
               </div>
               @endforeach

               <!-- paginating -->
               <span style="padding-top:20px;">
               {!!$product->withQueryString()->links('pagination::bootstrap-5')!!}
               </span>
               
            </div>
            <!-- <div class="btn-box">
               <a href="">
               View All products
               </a>
            </div> -->
         </div>
      </section>