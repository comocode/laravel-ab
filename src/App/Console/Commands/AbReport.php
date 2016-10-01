<?php

namespace ComoCode\LaravelAb\App\Console\Commands;

use Illuminate\Console\Command;

class AbReport extends Command
{
    protected $signature = 'ab:report
    {experiment? : Name of the experiment to report on}
    {--list : list experiments in database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'provides statistic on experiments';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $experiment = $this->argument('experiment', false);
        $list = $this->option('list', false);

        if ($list == true) {
            $this->prettyPrint($this->listReports());

            return true;
        }

        if (!empty($experiment)) {
            $this->prettyPrint($this->printReport($experiment));
        } else {
            $reports = $this->listReports();
            $info = [];
            foreach ($reports as $report) {
                $info[$report->experiment] = $this->printReport($report->experiment);
            }
            $this->prettyPrint($info);
        }
    }

    public function prettyPrint($info)
    {
        $this->info(json_encode($info, JSON_PRETTY_PRINT));
    }

    public function printReport($experiment)
    {
        $info = [];

        $full_count =
            \DB::table('ab_events')
                ->select(\DB::raw('ab_events.value,count(*) as hits'))
                ->where('ab_events.name', '=', (string) $experiment)
                ->groupBy('ab_events.value')
                ->get();

        foreach ($full_count as $record) {
            $info[$record->value] = [
                'condition' => $record->value,
                'hits' => $record->hits,
                'goals' => 0,
                'conversion' => 0,
            ];
        }

        $goal_count = \DB::table('ab_events')
            ->select(\DB::raw('ab_events.value,count(ab_events.value) as goals'))
            ->join('ab_goal', 'ab_goal.instance_id', '=', 'ab_events.instance_id')
            ->where('ab_events.name', '=', (string) $experiment)
            ->groupBy('ab_events.value')
            ->get();

        foreach ($goal_count as $record) {
            $info[$record->value]['goals'] = $record->goals;
            $info[$record->value]['conversion'] = ($record->goals / $info[$record->value]['hits']) * 100;
        }

        usort($info, function ($a, $b) {
            return $a['conversion'] < $b['conversion'];
        });

        return $info;
    }

    public function listReports()
    {
        $info =
            \DB::table('ab_experiments')
                ->join('ab_events', 'ab_events.experiments_id', '=', 'ab_experiments.id')
                ->select(\DB::raw('ab_experiments.experiment, count(*) as hits'))
                ->groupBy('ab_experiments.id')
                ->get();

        return $info;
    }
}
