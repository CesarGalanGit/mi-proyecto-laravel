<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $fuel = (string) $request->query('fuel', '');
        $transmission = (string) $request->query('transmission', '');
        $source = (string) $request->query('source', '');
        $maxPrice = (int) $request->query('max_price', 0);
        $sort = (string) $request->query('sort', 'latest');

        $carsQuery = Car::query()
            ->where('status', 'available')
            ->when($fuel !== '', function ($query) use ($fuel) {
                $query->where('fuel_type', $fuel);
            })
            ->when($transmission !== '', function ($query) use ($transmission) {
                $query->where('transmission', $transmission);
            })
            ->when($source !== '', function ($query) use ($source) {
                $query->where('source_name', $source);
            })
            ->when($maxPrice > 0, function ($query) use ($maxPrice) {
                $query->where('price', '<=', $maxPrice);
            });

        if ($search !== '') {
            // Generar un token de usuario único para analytics (por sesión)
            $userToken = $request->session()->get('algolia_user_token');
            if (! $userToken) {
                $userToken = Str::uuid()->toString();
                $request->session()->put('algolia_user_token', $userToken);
            }

            $searchIds = Car::search($search)
                ->options([
                    'analytics' => true,
                    'userToken' => $userToken,
                    'clickAnalytics' => true,
                ])
                ->take(1000)
                ->keys()
                ->all();

            if ($searchIds === []) {
                $carsQuery->whereRaw('1 = 0');
            } else {
                $carsQuery->whereIn('id', $searchIds);
            }
        }

        if ($sort === 'price_asc') {
            $carsQuery->orderBy('price');
        } elseif ($sort === 'price_desc') {
            $carsQuery->orderByDesc('price');
        } elseif ($sort === 'mileage_asc') {
            $carsQuery->orderBy('mileage');
        } else {
            $carsQuery->latest();
        }

        $cars = $carsQuery->paginate(9)->withQueryString();

        $featuredCars = Car::query()
            ->where('status', 'available')
            ->where('featured', true)
            ->when($source !== '', function ($query) use ($source) {
                $query->where('source_name', $source);
            })
            ->latest()
            ->limit(3)
            ->get();

        return view('shop.index', [
            'cars' => $cars,
            'featuredCars' => $featuredCars,
            'filters' => [
                'search' => $search,
                'fuel' => $fuel,
                'transmission' => $transmission,
                'source' => $source,
                'max_price' => $maxPrice,
                'sort' => $sort,
            ],
        ]);
    }

    public function show(Car $car): View
    {
        abort_if($car->status !== 'available', 404);

        $relatedCars = Car::query()
            ->where('status', 'available')
            ->where('id', '!=', $car->id)
            ->where(function ($query) use ($car) {
                $query
                    ->where('brand', $car->brand)
                    ->orWhere('fuel_type', $car->fuel_type);
            })
            ->latest()
            ->limit(3)
            ->get();

        return view('shop.show', [
            'car' => $car,
            'relatedCars' => $relatedCars,
        ]);
    }

    public function outbound(Request $request, Car $car): RedirectResponse
    {
        abort_if($car->status !== 'available', 404);
        abort_if(blank($car->source_url), 404);

        DB::transaction(function () use ($request, $car): void {
            $car->referralClicks()->create([
                'user_id' => $request->user()?->id,
                'source_name' => $car->source_name,
                'destination_url' => (string) $car->source_url,
                'referrer' => $request->headers->get('referer'),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'clicked_at' => now(),
            ]);

            $car->increment('outbound_clicks');
        });

        return redirect()->away((string) $car->source_url);
    }
}
