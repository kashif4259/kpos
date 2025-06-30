<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ProductReportResourceInventory;
use App\Http\Resources\ProductReportsResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductReportsController extends Controller
{
    protected Product $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProductReports(Request $request,$reportType)
    {

        // page=1&limit=10&column=title&order=asc&search=test&startDate=2024-09-01&endDate=2024-09-30

        $request->page ??=  "";
        $request->limit ??=  10;
        $request->column ??= "id";
        $request->order ??=  "desc";
        $request->search ??=  "";
        $request->startDate ??=  "";
        $request->endDate ??=  "";


        // dump(
        //     $request->page, 
        //         $request->limit,
        //         $request->column,
        //         $request->order,
        //         $request->search,
        //         $request->startDate,
        //         $request->endDate
        // );

        // $query = $this->product->orderBy($request->column, $request->order);
        // $products = $query->paginate($request->per_page);
        if($reportType == "profit_loss"){
            $products = $this->product->getProductReportProfitLoss(
                $request->page, 
                $request->limit,
                $request->column,
                $request->order,
                $request->search,
                $request->startDate,
                $request->endDate
            );
            return ProductReportsResource::collection($products);
        }
        else if($reportType == "inventory"){
            $products = $this->product->getProductReportInventory(
                $request->offset, 
                $request->limit,
                $request->column,
                $request->order,
                $request->search,
                $request->startDate,
                $request->endDate
            );
            return ProductReportResourceInventory::collection($products);
        }
        else{
            $products = [];
        }

        
        return ProductReportsResource::collection($products);
        
    }
}
