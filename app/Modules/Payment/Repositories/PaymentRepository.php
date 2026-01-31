<?php

namespace App\Modules\Payment\Repositories;

use App\Base\BaseRepository;
use App\Modules\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    /**
     * @return mixed
     */
    public function findByPaymentId(string $paymentId)
    {
        return $this->model->where('payment_id', $paymentId)->first();
    }

    /**
     * @return mixed
     */
    public function getByOrder(int $orderId)
    {
        return $this->model
            ->where('order_id', $orderId)
            ->with('order')
            ->latest()
            ->paginate(15);
    }

    /**
     * @return mixed
     */
    public function getByStatus(string $status, int $perPage = 15)
    {
        return $this->model
            ->where('status', $status)
            ->with('order')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @return Collection|Model|null
     */
    public function findWithOrder(int $id)
    {
        return $this->model::with('order')->find($id);
    }
}
