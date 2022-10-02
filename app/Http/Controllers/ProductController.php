<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $productData = Product::with([
                            'ProductVariantPrice'=>function($q) use($request){
                                $q->when($request->price_from,function($qu)use($request){
                                    $qu->where('price','>=', $request->price_from ?? 0)
                                    ->orWhere('price','<=',$request->price_to ?? 0);
                                });
                            },
                            'ProductVariant'=>function($q) use($request){
                                $q->when($request->variant,function($qu)use($request){
                                    $qu->Where('variant', 'like', '%' . $request->variant . '%');
                                });
                            },
                            'ProductImage'
                        ])
                        ->when($request->title,function($q)use($request){
                            $q->Where('title', 'like', '%' . $request->title . '%');
                        })
                        ->when($request->date,function($q)use($request){
                            $q->Where('created_at', '>=', $request->date);
                        })
                        ->get()->toArray();

        foreach($productData as $key=>$val){
            foreach($val['product_variant_price'] as $in=>$vl){
                if(!empty($vl['product_variant_one'])){
                    $productData[$key]['product_variant_price'][$in]['product_variant_one'] =
                    ProductVariant::select('variant')->where('id',$vl['product_variant_one'])->first()->variant;
                }
                if(!empty($vl['product_variant_two'])){
                    $productData[$key]['product_variant_price'][$in]['product_variant_two'] =
                    ProductVariant::select('variant')->where('id',$vl['product_variant_two'])->first()->variant;
                }
                if(!empty($vl['product_variant_three'])){
                    $productData[$key]['product_variant_price'][$in]['product_variant_three'] =
                    ProductVariant::select('variant')->where('id',$vl['product_variant_three'])->first()->variant;
                }
            }
        }

        $variant = ProductVariant::with('Variant')->select('id','variant')->distinct()->get();

        return view('products.index',compact('productData','variant'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try{

        $productData = [
            'title'=>$request->title,
            'sku'=>$request->description,
            'description'=>$request->description,
            'created_at'=> Carbon::now(),
            'created_at'=> Carbon::now()
        ];

         $productSaved = Product::create($productData);

        if($productSaved){

            $productVariantData = [];
            $productVariantSave = false;
            foreach($request->product_variant as $key=>$value){
                foreach($value['tags'] as $index=>$item){
                    $productVariantData[$index]['variant_id'] = $value['option'] ?? null;
                    $productVariantData[$index]['variant'] = $item ?? null;
                    $productVariantData[$index]['product_id'] = $productSaved->id ?? null?? null;
                    $productVariantData[$index]['created_at'] = Carbon::now();
                    $productVariantData[$index]['updated_at'] = Carbon::now();
                }
                $productVariantSave = DB::table('product_variants')->insert($productVariantData);
            }


            if($productVariantSave){
                $savedProductVariantData = ProductVariant::select('id','variant')->where('product_id',$productSaved->id)->get();
                $newProductVariantData = $savedProductVariantData->toArray();

                $variantPriceTable = [];

                foreach($request->product_variant_prices as $index=>$data){
                    $title = explode('/', $data['title']);
                    array_pop($title);

                    foreach($newProductVariantData as $key=>$val){
                        if(isset($title[0]) && $title[0] == $val['variant']){
                            $variantPriceTable[$index]['product_variant_one'] = $val['id']?? null;
                        }
                        if(isset($title[1]) && $title[1] == $val['variant']){
                            $variantPriceTable[$index]['product_variant_two'] = $val['id']?? null;
                        }
                        if(isset($title[2]) && $title[2] == $val['variant']){
                            $variantPriceTable[$index]['product_variant_three'] = $val['id']?? null;
                        }
                    }
                    $variantPriceTable[$index]['price'] = $data['price'] ?? null;
                    $variantPriceTable[$index]['stock'] = $data['stock'] ?? null;
                    $variantPriceTable[$index]['product_id'] = $productSaved->id;
                    $variantPriceTable[$index]['created_at'] = Carbon::now();

                }
                    $productVariantPrice = DB::table('product_variant_prices')->insert($variantPriceTable);
            }
        }

        DB::commit();

        if($productVariantPrice){
            return response()->json(['status'=>'success','msg'=>'Product Inserted Successfull']);
        }


    }catch(Exception $e){
        DB::rollback();
        dd($e);

        return response()->json(['status'=>'error','msg'=>'Product Not Inserted Successfully'.$e->getMessage()]);
    }

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {

        $productData = Product::with([
            'ProductVariantPrice',
            'ProductVariant',
            'ProductImage'
        ])
        ->where('id',$id)
        ->first()->toArray();

        $variants = Variant::all();
        // dd($variants,json_encode($productData));
        return view('products.edit', compact('variants','productData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
