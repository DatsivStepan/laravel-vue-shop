<?php

namespace App\Repositories\Admin;

use App\Events\Subscriptions\Deleted;
use App\Events\Subscriptions\Updated;
use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionRepository
{
    /**
     * @param array $payload
     * @return Subscription
     */
    public function create(array $payload): Subscription
    {
        return Subscription::query()->create($payload);
    }

    /**
     * @param int|null $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = null): LengthAwarePaginator
    {
        return Subscription::query()->paginate($perPage);
    }

    /**
     * @return Subscription[]|Collection
     */
    public function all()
    {
        return Subscription::all();
    }

    /**
     * @param int $id
     * @return Subscription
     */
    public function find(int $id): Subscription
    {
        return Subscription::query()->find($id);
    }

    /**
     * @param int $userId
     * @param int|null $perPage
     * @return LengthAwarePaginator
     */
    public function getUserSubscription(int $userId, int $perPage = null): LengthAwarePaginator
    {
        return Subscription::query()->where('user_id', $userId)->paginate($perPage);
    }

    /**
     * @param Subscription|int $subscription
     * @param array $payload
     * @return Subscription
     */
    public function update($subscription, array $payload): Subscription
    {
        /** @var Subscription $subscription */
        $subscription = $this->check($subscription);

        $subscription->update($payload);

        event(new Updated($subscription));

        return $subscription;
    }

    /**
     * @param array|int $ids
     */
    public function delete($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];

        Subscription::query()->whereIn('id', $ids)->each(function (Subscription $subscription) {
            $subscription->delete();
            event(new Deleted($subscription));
        });
    }

    /**
     * @param $subscription
     * @return Subscription
     */
    public function check($subscription): Subscription
    {
        return $subscription instanceof Subscription ? $subscription : Subscription::query()->findOrFail($subscription);
    }
}
