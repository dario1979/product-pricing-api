<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Catalog\DTOs\RegisterProductPriceData;
use App\Application\Catalog\UseCases\ListProductPrices;
use App\Application\Catalog\UseCases\RegisterProductPrice;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProductPriceController extends Controller
{
    public function index(
        int $id,
        ListProductPrices $listProductPrices,
    ): AnonymousResourceCollection {
        $prices = $listProductPrices->execute($id);

        return ProductPriceResource::collection(
            collect($prices)
        );
    }

    public function store(
        int $id,
        StoreProductPriceRequest $request,
        RegisterProductPrice $registerProductPrice,
    ): JsonResponse {
        $price = $registerProductPrice->execute(
            $id,
            RegisterProductPriceData::fromArray(
                $request->validated()
            )
        );

        return (new ProductPriceResource($price))
            ->response()
            ->setStatusCode(201);
    }
}

