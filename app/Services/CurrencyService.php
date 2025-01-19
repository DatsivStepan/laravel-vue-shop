<?php

namespace App\Services;

use App\Models\Currency;
use App\Repositories\Admin\CurrencyRepository;
use App\Repositories\Admin\SettingsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CurrencyService
{
    /**
     * @var CurrencyRepository
     */
    private $repository;
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    public function __construct(CurrencyRepository $repository, SettingsRepository $settingsRepository)
    {
        $this->repository = $repository;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param string $code
     * @param array $data
     * @param bool $makeDefault
     *
     * @return Model
     * @throws \Exception
     */
    public function add(string $code, array $data, bool $makeDefault = false)
    {
        /** @var Currency $currency */
        $currency = $this->repository->findByCode($code);
        $data['active'] = true;

        return $this->update($currency, $data, $makeDefault);
    }

    /**
     * @param Currency $currency
     * @param array $data
     * @param bool $makeDefault
     *
     * @return Model
     * @throws \Exception
     */
    public function update(Currency $currency, array $data, bool $makeDefault = false)
    {
        DB::beginTransaction();
        try {
            $currency = $this->repository->update($currency, $data);

            if ($makeDefault) {
                $this->settingsRepository->updateByKey('currency', $currency->code);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }

        return $currency;
    }
}
