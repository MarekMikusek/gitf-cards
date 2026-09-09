<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CardController extends Controller
{
    private const RESULTS_PER_PAGE = 10;
    public function index(): AnonymousResourceCollection
    {
        $cards = Card::latest()->paginate(self::RESULTS_PER_PAGE);

        return CardResource::collection($cards);
    }

public function store(StoreCardRequest $request): JsonResponse
    {
        $card = Card::create($request->validated());

        return (new CardResource($card))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Card $card): CardResource
    {
        return new CardResource($card);
    }

    public function update(StoreCardRequest $request, Card $card):CardResource
    {
        $card->update($request->validated());
        return new CardResource($card);
    }

    public function delete(Card $card): JsonResponse
    {
        $card->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
