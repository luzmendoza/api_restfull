<?php

namespace App\Policies;

use App\User;
use App\Transaction;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization, AdminActions;//AdminActions da permiso a administrador

    /**
     * Determine whether the user can view the transaction.
     *
     * @param  \App\User  $user
     * @param  \App\Transaction  $transaction
     * @return mixed
     */
    public function view(User $user, Transaction $transaction)
    {
        //si el usuario es comprador o vendedor
        return $user->id === $transaction->buyer->id || $user->id === $transaction->product->seller->id;
    }
}