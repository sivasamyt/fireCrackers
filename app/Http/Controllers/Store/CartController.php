<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\GiftBox;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        $totals = $cart->totals();

        return view('store.cart', [
            'items' => $totals['items'],
            'subtotal' => $totals['subtotal'],
            'discountTotal' => $totals['discount_total'],
            'grandTotal' => $totals['grand_total'],
            'cartCount' => $cart->count(),
        ]);
    }

    public function summary(CartService $cart): JsonResponse
    {
        return response()->json($cart->summary());
    }

    public function store(Request $request, CartService $cart): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => [Rule::requiredIf(! $request->filled('gift_box_id')), 'nullable', 'exists:products,id'],
            'gift_box_id' => ['nullable', 'exists:gift_boxes,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $quantity = (int) ($data['quantity'] ?? 1);
        $line = null;

        if ($request->filled('gift_box_id')) {
            $giftBox = GiftBox::query()->where('is_active', true)->findOrFail($data['gift_box_id']);
            $cart->addGiftBox($giftBox, $quantity);
            $line = $this->lineFromSummary($cart, 'gift_box', $giftBox->id);

            if ($this->wantsCartJson($request)) {
                return response()->json(array_merge($cart->summary(), [
                    'message' => 'Gift box added to cart.',
                    'line' => $line,
                ]));
            }

            return redirect()->route('cart.index')->with('success', 'Gift box added to cart.');
        }

        $product = Product::query()->where('is_active', true)->findOrFail($data['product_id']);
        $cart->add($product, $quantity);
        $line = $this->lineFromSummary($cart, 'product', $product->id);

        if ($this->wantsCartJson($request)) {
            return response()->json(array_merge($cart->summary(), [
                'message' => 'Added to cart.',
                'line' => $line,
            ]));
        }

        return redirect()->route('cart.index')->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartService $cart): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => [Rule::requiredIf(! $request->filled('gift_box_id')), 'nullable', 'exists:products,id'],
            'gift_box_id' => ['nullable', 'exists:gift_boxes,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        if ($request->filled('gift_box_id')) {
            $cart->updateGiftBox((int) $data['gift_box_id'], (int) $data['quantity']);
            $line = $this->lineFromSummary($cart, 'gift_box', (int) $data['gift_box_id']);
        } else {
            $cart->update((int) $data['product_id'], (int) $data['quantity']);
            $line = $this->lineFromSummary($cart, 'product', (int) $data['product_id']);
        }

        if ($this->wantsCartJson($request)) {
            return response()->json(array_merge($cart->summary(), [
                'message' => 'Cart updated.',
                'line' => $line,
            ]));
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, CartService $cart): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => [Rule::requiredIf(! $request->filled('gift_box_id')), 'nullable', 'exists:products,id'],
            'gift_box_id' => ['nullable', 'exists:gift_boxes,id'],
        ]);

        if ($request->filled('gift_box_id')) {
            $cart->removeGiftBox((int) $data['gift_box_id']);
        } else {
            $cart->remove((int) $data['product_id']);
        }

        if ($this->wantsCartJson($request)) {
            return response()->json(array_merge($cart->summary(), [
                'message' => 'Item removed.',
                'line' => null,
            ]));
        }

        return back()->with('success', 'Item removed.');
    }

    private function wantsCartJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax() || $request->wantsJson();
    }

    /**
     * @return array{type: string, id: int, name: string, image_url: string, quantity: int, unit_price: float, line_total: float}|null
     */
    private function lineFromSummary(CartService $cart, string $type, int $id): ?array
    {
        foreach ($cart->summary()['items'] as $item) {
            if ($item['type'] === $type && (int) $item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }
}
