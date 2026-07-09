<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AddressRequest;
use App\Models\Customer\Address;
use App\Services\AddressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(private readonly AddressService $addresses) {}

    public function index(Request $request): View
    {
        return view('account.addresses', [
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->orderBy('id')->get(),
        ]);
    }

    public function store(AddressRequest $request): RedirectResponse
    {
        $this->addresses->create($request->user(), $request->validated());

        return back()->with('status', 'address-created');
    }

    public function update(AddressRequest $request, Address $address): RedirectResponse
    {
        $this->authorizeOwnership($request, $address);

        $this->addresses->update($address, $request->validated());

        return back()->with('status', 'address-updated');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeOwnership($request, $address);

        $this->addresses->delete($address);

        return back()->with('status', 'address-deleted');
    }

    /**
     * Anti-IDOR : l'id d'adresse est auto-incrémenté et donc devinable —
     * sans ce contrôle, un utilisateur pourrait modifier/supprimer
     * l'adresse d'un autre en changeant l'id dans l'URL (cahier des
     * charges, section 3, "protection des données personnelles").
     */
    private function authorizeOwnership(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403);
    }
}
