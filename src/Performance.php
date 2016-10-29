<?php namespace Wys\Laravel;

class Performance
{
    protected $benchmarks = [];

    protected $benchmarks_time = [];

    protected $name = null;

    protected $queries = [];

    protected $is_bind_events = false;

    /**
     * The current globally available performance (if any).
     *
     * @var static
     */
    protected static $instance;


    /**
     * Set the globally available instance of the performance.
     *
     * @param string $name
     *
     * @return \Angejia\Services\Monitor\Performance|static
     */
    public static function getInstance()
    {
        if (static::$instance) {
            return static::$instance;
        }

        return static::$instance = new static;
    }

    /**
     * Auto determine if the benchmarks is start or end.
     *
     * @param $name
     */
    public function auto($name)
    {
        if ($this->hasStarted($name)) {
            $this->end($name);
        } else {
            $this->start($name);
        }

        return $this;
    }

    /**
     * Benchmark performance start.
     *
     * @param $name
     */
    public function start($name)
    {
        $this->benchmarks[$name] = microtime(true);
    }

    /**
     * Benchmark performance end.
     *
     * @param $name
     */
    public function end($name)
    {
        if ($this->hasStarted($name)) {
            $this->benchmarks_time[$name][] = $this->getElapsedMicroSeconds($this->benchmarks[$name]);
            $this->benchmarks[$name] = null;
        }
    }

    /**
     * Determine if has the name start benchmark
     *
     * @param $name
     *
     * @return bool
     */
    public function hasStarted($name)
    {
        return isset($this->benchmarks[$name]);
    }

    /**
     * Get benchmark performance time.
     *
     * @param $name
     */
    public function getBenchmarkTime()
    {
        return $this->benchmarks_time;
    }

    /**
     * Set performance name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function setName($name)
    {
        return $this->name = $name;
    }

    /**
     * Get performance name;
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Start listening to eloquent queries
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function startRecordQueries()
    {
        if ($this->is_bind_events !== true) {
            app('events')->listen('illuminate.query', [$this, 'registerQuery']);
        }

        $this->flushQueries();

        $this->is_bind_events = true;
    }

    /**
     * Log the query into the internal store
     *
     * @param $query
     * @param $bindings
     * @param $time
     * @param $connection
     *
     */
    public function registerQuery($query, $bindings, $time, $connection)
    {
        $this->queries[] = compact('query', 'bindings', 'time', 'connection');
    }

    /**
     * Returns an array of runnable queries and their durations from the internal array
     *
     * @return array
     */
    public function getDatabaseQueries()
    {
        return $this->queries;
    }

    /**
     * Get intact database sql.
     *
     * $return []
     */
    public function getSql()
    {
        $sql = [];
        foreach ($this->queries as $query) {
            if (count($query['bindings']) > 0) {
                $sql[] = preg_replace(
                    array_fill(0, count($query['bindings']), '/\?/'),
                    array_map(function ($item) {
                        return is_numeric($item) ? $item : "'" . $item . "'";
                    }, $query['bindings']),
                    $query['query'], 1);
            } else {
                $sql[] = $query['query'];
            }
        }

        return $sql;
    }

    /**
     * FLush database query log.
     *
     * @return $this
     */
    public function flushQueries()
    {
        $this->queries = [];

        return $this;
    }

    /**
     * Get benchmark performance micro seconds since a given starting point.
     *
     * @param  int $start
     *
     * @return float
     */
    protected function getElapsedMicroSeconds($start)
    {
        return round((microtime(true) - $start) * 1000, 2);
    }
}
