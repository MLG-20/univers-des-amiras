<?php

namespace App\Services;

use App\Models\Customer\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddressService
{
    /**
     * Crée une adresse pour l'utilisateur. La toute première adresse d'un
     * compte est automatiquement définie par défaut, même si l'utilisateur
     * n'a pas coché la case — un compte sans adresse par défaut n'aurait
     * aucun sens au moment de passer commande.
     */
    public function create(User $user, array $data): Address
    {
        $data['is_default'] = $data['is_default'] ?? false;

        return DB::transaction(function () use ($user, $data) {
            if ($data['is_default'] || ! $user->addresses()->exists()) {
                $user->addresses()->update(['is_default' => false]);
                $data['is_default'] = true;
            }

            return $user->addresses()->create($data);
        });
    }

    /**
     * Met à jour une adresse déjà vérifiée comme appartenant à l'utilisateur
     * (l'appelant doit avoir fait ce contrôle — voir
     * AddressController::authorizeOwnership()).
     */
    public function update(Address $address, array $data): Address
    {
        return DB::transaction(function () use ($address, $data) {
            if ($data['is_default'] ?? false) {
                $address->user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update($data);

            return $address->fresh();
        });
    }

    /**
     * Supprime l'adresse. Si elle était la valeur par défaut, en réassigne
     * une autre automatiquement pour qu'un compte avec des adresses ne se
     * retrouve jamais sans adresse par défaut.
     */
    public function delete(Address $address): void
    {
        DB::transaction(function () use ($address) {
            $wasDefault = $address->is_default;
            $user = $address->user;

            $address->delete();

            if ($wasDefault) {
                $user->addresses()->orderBy('id')->first()?->update(['is_default' => true]);
            }
        });
    }
}
