<?php

namespace App\Repositories;

use App\Model\User;
use IntercaseDefault\Repositories\Contracts\RepositoryAbstract;

class UserRepository extends RepositoryAbstract
{
    public function __construct( $model = '' )
    {
        parent::__construct( User::class, $model );
        return $this;
    }

    public function createOrUpdate($arrayValores)
    {
        // TODO: Implement createOrUpdate() method.
    }
}