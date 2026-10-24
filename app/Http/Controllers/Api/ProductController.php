<?php

namespace App\Http\Controllers\Api;

use App\Application\Catalog\DTOs\CreateProductData;
use App\Application\Catalog\UseCases\CreateProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use App\Application\Catalog\UseCases\GetProduct;
use App\Application\Catalog\UseCases\ListProducts;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Application\Catalog\DTOs\UpdateProductData;
use App\Application\Catalog\UseCases\UpdateProduct;
use App\Http\Requests\UpdateProductRequest;
use App\Application\Catalog\UseCases\DeleteProduct;
use Illuminate\Http\Response;

final class ProductController extends Controller
{

    public function index(
        ListProducts $listProducts,
    ): AnonymousResourceCollection {
        $products = $listProducts->execute();

        return ProductResource::collection(
            collect($products)
        );
    }

    public function show(
        int $id,
        GetProduct $getProduct,
    ): ProductResource {
        $product = $getProduct->execute($id);

        return new ProductResource($product);
    }

    public function store(
        StoreProductRequest $request,
        CreateProduct $createProduct,
    ): JsonResponse {
        $product = $createProduct->execute(
            CreateProductData::fromArray(
                $request->validated()
            )
        );

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        int $id,
        UpdateProductRequest $request,
        UpdateProduct $updateProduct,
    ): ProductResource {
        $product = $updateProduct->execute(
            $id,
            UpdateProductData::fromArray(
                $request->validated()
            )
        );

        return new ProductResource($product);
    }

    public function destroy(
        int $id,
        DeleteProduct $deleteProduct,
    ): Response {
        $deleteProduct->execute($id);

        return response()->noContent();
    }
}
