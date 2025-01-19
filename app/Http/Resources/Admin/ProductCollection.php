<?php

namespace App\Http\Resources\Admin;

use App\Facades\UserSettings;
use App\Helpers\GridDataHelper;
use App\Models\Categories;
use App\Repositories\Admin\ProductsRepository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ProductCollection extends ResourceCollection
{
    private $user;
    private $itemsCount = 0;
    private $requestData = [];

    public function __construct($resource, $user)
    {
        parent::__construct($resource);

        $this->user = $user;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $this->requestData = $request->all();
        if (array_key_exists('page', $this->requestData)) {
            unset($this->requestData['page']);
        }

        $productPrices = ProductsRepository::getMinMaxPrice();
        $this->itemsCount = count($this->collection);

        $userCurrency = UserSettings::getCurrency($request->user());
        $defaultCurrency = getSetting('currency');
        return [
            'data' => $this->getData($userCurrency),
            'gridData' => [
                'headerItems' => $this->getHeaderItems(),
                'rowsItems' => $this->getRowsItems(),
                'bulkActions' => $this->getBulkActions(),
                'options' => [
                    'enableDraggble' => true,
                ],
                'filters' => $this->getFilters(),
            ],
            'minPrice' => currency((int)$productPrices['min'], $defaultCurrency, $userCurrency, false),
            'maxPrice' => currency((int)$productPrices['max'], $defaultCurrency, $userCurrency, false),
        ];
    }

    /**
     * @param null $currency
     *
     * @return Collection
     */
    public function getData($currency = null)
    {
        return $this->collection->map(function ($collection) use ($currency) {
            return new ProductResource($collection, $currency);
        });
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/products/bulk-actions',
            'actions' => [
                'hide' => __('system.hide'),
                'show' => __('system.show'),
                'deleteAll' => __('system.delete')
            ]
        ];
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'name' => [
                'value' => __('products.table.name'),
                'sort' => true
            ],
            'id_number' => [
                'value' => __('products.table.id_number'),
                'sort' => true
            ],
            'categories' => __('products.table.category'),
            'courses' => [
                'value' => __('products.table.courses'),
                'sort' => true
            ],
            'price' => [
                'value' => __('products.table.price'),
                'sort' => true
            ],
            'purchased_times' => __('products.table.purchased_times'),
            'featured' => __('products.table.featured'),
            'actions' => ''
        ];
    }

    /**
     * @return Collection
     */
    public function getRowsItems(): Collection
    {
        return $this->collection->map(function ($collection, $index) {
            $cResource = (new ProductResource($collection))->toArray(Container::getInstance()->make('request'));
            $rows = [
                'name' => [
                    'value' => $cResource['name'],
                    'type' => 'link',
                    'params' => [
                        'routeName' => 'product-show',
                        'routeParams' => ['id' => $cResource['id']],
                    ]
                ],
                'id_number' => [
                    'value' => $cResource['id_number']
                ],
                'category' => [
                    'value' => implode(', ', $collection->categories()->pluck('name')->all()),
                ],
                'courses' => [
                    'value' => $collection->courses()->count()
                ],
                'price' => [
                    'value' => $cResource['formatted_price']
                ],
                'purchased_times' => [
                    'value' => $cResource['purchased_times']
                ],
                'index' => $cResource['id'], // not show in grid
                'activeRow' => $cResource['visible']
            ];

            if ((int)getSetting('enable_featured_products')) {
                $fIcon = $cResource['featured'] ? 'star-fill' : 'star';
                $rows['featured'] = [
                    'type' => 'action',
                    'value' => GridDataHelper::generateAction(
                        __('products.featured'),
                        GridDataHelper::ACTION_TYPE_VUE_METHOD,
                        [
                            'methodName' => 'actionApi',
                            'params' => [
                                'route' => '/admin/products/actions/' . $cResource['id'],
                                'method' => 'post',
                                'routeParams' => ['action' => 'featured']
                            ],
                            'icon' => $fIcon
                        ]
                    )
                ];
            }

            $rows['actions'] = $this->getActions($cResource, $index);
            return $rows;
        });
    }

    /**
     * @param App\Http\Resources\Admin\ProductResource $record
     * @return array
     */
    public function getActions($record, &$k)
    {
        $actions = [];

        if ($this->user->can('canEditProduct')) {
            $actions[] = GridDataHelper::generateEditButton(
                'admin-product-edit',
                $record['id'],
                __('system.edit')
            );

            $sAction = $record['visible'] ? 'hide' : 'show';
            $actions[] = GridDataHelper::generateActionApi(__('system.' . $sAction),
                '/admin/products/actions/' . $record['id'],
                [
                    'action' => $sAction,
                    'status' => $record['visible'],
                ]);

            $actions[] = GridDataHelper::generateActionLink(
                __('products.actions.assign_cources'),
                'admin-product-assign-course',
                ['id' => $record['id']]
            );
        }

        if ($this->user->can('manageVendors')) {
            $actions[] = GridDataHelper::generateActionLink(
                __('products.actions.assign_vendors'),
                'admin-product-vendors-assigns',
                ['id' => $record['id']]
            );
        }

        //TODO add actions  after adding functionality
//        - Assign Roles (if setting enabled)
//        - Enrollments (if setting enabled)
//        - Manage Sessions (if setting enabled)

        if ($this->user->can('canDeleteProduct')) {
            $actions[] = GridDataHelper::generateDeleteButton(
                '/admin/products/' . $record['id'],
                __('system.delete')
            );
        }

        return $actions;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $filters = GridDataHelper::getDefaultFilters();
        $filters['visible']['options']['value'] = UserSettings::get($this->user, 'products_filter_status');

        $categories = Arr::prepend(
            Categories::pluck('name', 'id')->toArray(),
            __('system.select_all'),
            ''
        );
        $filters['categories'] = GridDataHelper::generateFilter('select', [
            'className' => 'w-25',
            'placeholder' => __('system.categories'),
            'options' => $categories,
            'value' => null
        ]);

        return $filters;
    }
}
