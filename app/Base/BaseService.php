<?php

namespace App\Base;

abstract class BaseService
{
    /**
     * @throws \Throwable
     */
    protected function handleException(\Throwable $throwable)
    {
        logger()->error($throwable->getMessage(), [
            'exception' => $throwable,
            'trace' => $throwable->getTrace(),
        ]);

        throw $throwable;
    }
}
