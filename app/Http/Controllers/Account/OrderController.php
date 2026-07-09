<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Historique des commandes. Le domaine Commande arrive en Phase 3 — cette
     * page existe dès maintenant (cahier des charges, section 2.3) mais
     * affiche un état vide tant que le tunnel de commande n'est pas
     * implémenté, plutôt que de créer un modèle Order à moitié fonctionnel.
     */
    public function index(): View
    {
        return view('account.orders');
    }
}
