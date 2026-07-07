<?php

namespace App\Exceptions;

use RuntimeException;

class CartException extends RuntimeException
{
    public static function variantRequired(): self
    {
        return new self('Merci de sélectionner une variante avant d\'ajouter ce produit au panier.');
    }

    public static function variantUnavailable(): self
    {
        return new self('Cette variante n\'est plus disponible.');
    }

    public static function productUnavailable(): self
    {
        return new self('Ce produit n\'est plus disponible.');
    }

    public static function insufficientStock(int $available): self
    {
        return new self($available > 0
            ? "Il ne reste que {$available} exemplaire(s) en stock pour cette variante."
            : 'Cette variante est en rupture de stock.');
    }
}
