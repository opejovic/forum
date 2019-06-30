<?php 

namespace App\Traits;

use App\Models\Activity;

trait RecordsActivity
{
	/**
	 * summary
	 *
	 * @return void
	 * @author 
	 */
	protected static function bootRecordsActivity()
	{
        if (auth()->guest()) return;
        
		static::getActivitiesToRecord()->each(function ($event) {
		    static::$event(function ($model) use ($event) {
	            $model->recordActivity($event);
	        });
		});

        static::deleting(function ($model) {
            $model->activities()->delete();
        });
	}

	/**
	 * summary
	 *
	 * @return void
	 * @author 
	 */
	protected static function getActivitiesToRecord()
	{
	    return collect(['created']);
	}

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function recordActivity($event)
    {
        $this->activities()->create([
            'user_id' => auth()->id(),
            'type' => "{$event}_{$this->getActivityModel()}",
        ]);
    }

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function getActivityModel()
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

}
