<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Mail;
use App\Mail\SendEmailProduct;
use Auth;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        if ($request->ajax()) {
    
            $data = Product::query();
    
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="View" class="me-1 btn btn-info btn-sm showProduct"><i class="fa-regular fa-eye"></i> View</a>';
                           $btn = $btn. '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct"><i class="fa-regular fa-pen-to-square"></i> Edit</a>';
      
                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct"><i class="fa-solid fa-trash"></i> Delete</a>';
                          
                           if($row->status==0){ 
                           $btn = $btn."<a href='javascript:void(0)'  data-toggle='tooltip' class=' ml-2  btn btn-success btn-sm' onclick='updatemessage(".$row->id.",1".")'>Active</a>";
                            
                           }else{
                            
                           $btn = $btn."<a href='javascript:void(0)' data-toggle='tooltip' class=' ml-2 mb-2  btn btn-danger btn-sm' onclick='updatemessage(".$row->id.",0".")'>UnActive</a>";
                           }
                           return $btn;
                    })
                    ->addColumn('cat_id', function($row){
                                $cat_ids=Category::where('id',$row->cat_id)->first();
                                if($cat_ids==null){
                                  $cat_id=null;
                                }
                                else{
                                    $cat_id= $cat_ids->name;
                                }
                              return  $cat_id;
                 })
                 ->addColumn('status', function($row){
                    if($row->status==0){
                        $statuscheck =  'Active';
                    }
                    else{
                        $statuscheck =  'UnActive';
                    }
                    
                    return $statuscheck;
                })
                    ->rawColumns(['action','cat_id','status'])
                    ->make(true);
        }
        $category=Category::get();
        return view('product.index',compact('category'));
    }
         
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            
            'cat_id' => 'required',
            'product_name' => 'required|min:3|max:50',
            'product_description' => 'required|min:10',
            'price' => 'required|numeric|min:1|max:6',
            'phone_number' => 'required|numeric|digits:10|',
            
        ]);
        $dp=str_replace("public/","",$request->product_image);
        $mailData = [
            'title' => 'Mail from Product Detail ',
            'product_name' => 'Product Name'.$request->product_name,
            'product_price' => 'Product Price'.$request->price,
            'phone_number'=> 'Phone Number'.$request->phone_number,
            'product_description'=> 'Product Description'.$request->product_description,
            'product_image'=>$dp,
        ];
           
        Mail::to(Auth::user()->email)->send(new SendEmailProduct($mailData));
         
          
       $check=Product::where('id',$request->product_id)->first();
       if($check==null){

        $request->validate([
            
             
            'product_image'=>'required|image|max:5120'

        ]);
      
        $file = $request->file('product_image');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('public/uploads', $fileName);


        $product = new Product;
        $product->cat_id = $request->cat_id;
        $product->product_name = $request->product_name;
        $product->product_description = $request->product_description;
        $product->price=$request->price;
        $product->phone_number=$request->phone_number;
        $product->product_image = $filePath;
        $product->save();





        
       }
       else{
        $product = Product::find($request->product_id);
        $product->product_name = $request->product_name;
        $product->product_description = $request->product_description;
        $product->price=$request->price;
        $product->phone_number=$request->phone_number;
        if($request->hasFile('product_image')){
            $file = $request->file('product_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/uploads', $fileName);

           $product->product_image= $filePath;
            //Delete the old photo
             

        }
        else{
            $oldFilename = $product->product_image;
            $product->product_image= $oldFilename;
        }
        
        $product->save();
            }
        return response()->json(['success'=>'Product saved successfully.']);
    }

    public function update(Request $request,$id)
    {
        $todo = Product::findOrFail($id);
        $todo->status = $request->status;
        $todo->save();
         
        
        return response()->json(['success'=>'Product  Status saved successfully.']);
       
    }
  
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
        return response()->json($product);
    }
  
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id): JsonResponse
    {
        $product = Product::find($id);
        return response()->json($product);
    }
      
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {
        Product::find($id)->delete();
        
        return response()->json(['success'=>'Product deleted successfully.']);
    }
}