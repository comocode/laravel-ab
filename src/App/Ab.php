<?php

namespace ComoCode\LaravelAb\App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Ab
{
    /**
     * @var static $session
     * Instance Object to identify user's session
     */
    protected static $session;

    /**
     * @var $instance
     *
     * Tracks every $experiment->fired condition the view is initiating
     * and event key->value pais for the instance
     */
    protected static $instance = [];

    /*
     * Individual Test Parameters
     */
    protected $name;
    protected $conditions = [];
    protected $fired;
    protected $goal;
    protected $metadata_callback;
    /**
     * @var Request
     */
    private $request;

    /**
     * Create a instance for a user session if there isnt once.
     * Load previous event -> fire pairings for this session if exist.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->ensureUser(false);
    }

    public function ensureUser($forceSession = false)
    {
        if (!Session::has(config('laravel-ab.cache_key')) || $forceSession) {
            $uid = md5(uniqid().$this->request->getClientIp());
            $laravel_ab_id = $this->request->cookie(config('laravel-ab.cache_key'), $uid);
            if(version_compare(App()->version(), '5.4.0', '>=')) {
                Session::put(config('laravel-ab.cache_key'), $uid);
            } else {
                Session::set(config('laravel-ab.cache_key'), $uid);
            }
        }

        if (empty(self::$session)) {
            self::$session = Instance::firstOrCreate([
                'instance' => Session::get(config('laravel-ab.cache_key')),
                'identifier' => $this->request->getClientIp(),
                'metadata' => (function_exists('laravel_ab_meta') ? call_user_func('laravel_ab_meta') : null),
            ]);
        }
    }

    /**
     * @param array $session_variables
     *                                 Load initial session variables to store or track
     *                                 Such as variables you want to track being passed into the template.
     */
    public function setup(array $session_variables = array())
    {
        foreach ($session_variables as $key => $value) {
            $experiment = new self();
            $experiment->experiment($key);
            $experiment->fired = $value;
            $experiment->instanceEvent();
        }
    }

    /**
     * When the view is rendered, this funciton saves all event->firing pairing to storage.
     */
    public static function saveSession()
    {
        if (!empty(self::$instance)) {
            foreach (self::$instance as $event) {
                $experiment = Experiments::firstOrCreate([
                    'experiment' => $event->name,
                    'goal' => $event->goal,
                ]);

                $event = Events::firstOrCreate([
                    'instance_id' => self::$session->id,
                    'name' => $event->name,
                    'value' => $event->fired,
                ]);

                $experiment->events()->save($event);
                self::$session->events()->save($event);
            }
        }

        return  Session::get(config('laravel-ab.cache_key'));
    }

    /**
     * @param $experiment
     *
     * @return $this
     *
     * Used to track the name of the experiment
     */
    public function experiment($experiment)
    {
        $this->name = $experiment;
        $this->instanceEvent();

        return $this;
    }

    /**
     * @param $goal
     *
     * @return string
     *
     * Sets the tracking target for the experiment, and returns one of the conditional elements for display
     */
    public function track($goal)
    {
        $this->goal = $goal;

        ob_end_clean();

        $conditions = [];
        foreach ($this->conditions as $key => $condition) {
            if (preg_match('/\[(\d+)\]/', $key, $matches)) {
                foreach (range(1, $matches[1]) as $index) {
                    $conditions[] = $key;
                }
            }
        }
        if (empty($conditions)) {
            $conditions = array_keys($this->conditions);
        }
        /// has the user fired this particular experiment yet?
        if ($fired = $this->hasExperiment($this->name)) {
            $this->fired = $fired;
        } else {
            shuffle($conditions);
            $this->fired = current($conditions);
        }

        return $this->conditions[$this->fired];
    }

    /**
     * @param $goal
     * @param goal $value
     *
     * Insert a simple goal tracker to know if user has reach a milestone
     */
    public function goal($goal, $value = null)
    {
        $goal = Goal::create(['goal' => $goal, 'value' => $value]);

        self::$session->goals()->save($goal);

        return $goal;
    }

    /**
     * @param $condition
     * @returns void
     *
     * Captures the HTML between AB condtions  and tracks them to their condition name.
     * One of these conditions will be randomized to some ratio for display and tracked
     */
    public function condition($condition)
    {
        $reference = $this;

        if (count($this->conditions) !== 0) {
            ob_end_clean();
        }

        $reference->saveCondition($condition, ''); /// so above count fires after first pass

        ob_start(function ($data) use ($condition, $reference) {
            $reference->saveCondition($condition, $data);
        });
    }

    /**
     * @param bool $forceSession
     *
     * @return mixed
     *
     * Ensuring a user session string on any call for a key to be used.
     */
    public static function getSession()
    {
        return self::$session;
    }
    /**
     * @param $condition
     * @param $data
     * @returns void
     *
     * A setter for the condition key=>value pairing.
     */
    public function saveCondition($condition, $data)
    {
        $this->conditions[$condition] = $data;
    }

    /**
     * @param $experiment
     * @param $condition
     *
     * Tracks at an instance level which event was selected for the session
     */
    public function instanceEvent()
    {
        self::$instance[$this->name] = $this;
    }

    /**
     * @param $experiment
     *
     * @return bool
     *
     * Determines if a user has a particular event already in this session
     */
    public function hasExperiment($experiment)
    {
        $session_events = self::$session->events()->get();
        foreach ($session_events as $event) {
            if ($event->name == $experiment) {
                return $event->value;
            }
        }

        return false;
    }

    /**
     * Simple method for resetting the session variable for development purposes.
     */
    public function forceReset()
    {
        self::resetSession();
        $this->ensureUser(true);
    }

    public function toArray()
    {
        return [$this->name => $this->fired];
    }

    public function getEvents()
    {
        return self::$instance;
    }

    public static function resetSession()
    {
        self::$session = false;
    }
}
