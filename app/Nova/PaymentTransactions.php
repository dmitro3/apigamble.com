<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use App\Apikeys;
use App\PaymentOptions;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;

class PaymentTransactions extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
	 
	public static $group = "Payment API";  
	
	public static function label()
	{
		return 'Transactions';
	}
	
	public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return $request->user()->access == "administrator";
    }
	 
    public static $model = \App\PaymentTransactions::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];
	
	public static function indexQuery(NovaRequest $request, $query)
    {
		if($request->user()->access != 'administrator') {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'paykey')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = $data->apikey;
			}
			return $query->whereIn('apikey', $apioperator);
		} else {
			return $query;
		}
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
           DateTime::make(__('Timestamp'), 'created_at')
                    ->sortable(), 
			Text::make('From')
                ->sortable()
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
			Text::make('To')
                ->sortable()
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
            Text::make('amount')
                ->rules('required', 'max:32', 'min:3')
                ->readonly(),
			Text::make('Currency')
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
			Text::make('Amount', 'amount', function () {
				return ($this->amount).' '.$this->currency;
				})
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
			Text::make('USD', 'amountusd', function () {
				return ($this->amountusd).'$';
				})
                ->sortable()
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
