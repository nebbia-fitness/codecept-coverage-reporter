<?php

namespace Helper;

use Codeception\Events;
use SebastianBergmann\CodeCoverage\Node\File;
use Codeception\Lib\Console\Output;
use Codeception\Lib\Console\MessageFactory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableCell;

class CoverageReporter extends \Codeception\Coverage\Subscriber\Printer implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    use \Codeception\Subscriber\Shared\StaticEvents;

    private $threshold = 30;
    
    public static $events = [
        Events::RESULT_PRINT_AFTER => 'printResult'
    ];

    public function printResult(\Codeception\Event\PrintResultEvent $e)
    {
        $output = new Output([]);
        $this->crapReport($output);
    }

    private function crapReport($output)
    {
        $report = parent::$coverage->getReport();

        $rows = [];
        $rows[] = [
            '<bold>Method</bold>',
            '<bold>Crap</bold>',
//            '<bold>Crap Load</bold>',
            '<bold>Coverage</bold>',
            '<bold>Complexity</bold>'
        ];
        $rows[] = new TableSeparator();

        $fullMethodCount = 0;
        $fullCrapMethodCount = 0;
        $fullCrapLoad = 0;
        $fullCrap = 0;

        foreach ($report as $item) {
            $namespace = 'global';

            if (!$item instanceof File) {
                continue;
            }

            $classes = $item->getClassesAndTraits();
            foreach ($classes as $className => $class) {
                foreach ($class['methods'] as $methodName => $method) {
                    $crapLoad = $this->getCrapLoad($method['crap'], $method['ccn'], $method['coverage']);

                    $fullCrap += $method['crap'];
                    $fullCrapLoad += $crapLoad;
                    $fullMethodCount++;

                    if ($method['crap'] >= $this->threshold) {
                        $fullCrapMethodCount++;
                    }

                    if (!empty($class['package']['namespace'])) {
                        $namespace = $class['package']['namespace'];
                    }

                    //if ($crapLoad > 0) {
                        $rows[] = [
                            $namespace . '\\' . $className . '::' . $methodName,
                            '<bold>' . $method['crap'] . '</bold>',
//                            round($crapLoad, 2),
                            round($method['coverage'], 2),
                            $method['ccn']
                        ];
                    //}
                }
            }
        }

        if ($fullMethodCount > 0) {
            $crapMethodPercent = round((100 * $fullCrapMethodCount) / $fullMethodCount, 2);
        } else {
            $crapMethodPercent = 0;
        }

        // crap by methods
        $table = new Table($output);
        $table
            ->setHeaders([new TableCell('<bold>Project Risks</bold> <comment>(more info: http://www.crap4j.org/faq.html)</comment>', ['colspan' => 4])])
            ->setRows($rows)
        ;
        $table->render();

        // sumary crap
        $output->writeln('');
        $tableSummary = new Table($output);
        $tableSummary
            ->setHeaders([new TableCell('<bold>Overall Crap Stats</bold>', ['colspan' => 2])])
            ->setRows([
                ['<bold>Key</bold>', '<bold>Value</bold>'],
                new TableSeparator(),
                ['Method count', $fullMethodCount],
                ['Crap method count', $fullCrapMethodCount],
                ['Crap load', round($fullCrapLoad)],
                ['Crap method percent', $crapMethodPercent . '%'],
                ['Total crap', $fullCrap],
                new TableSeparator(),
                [new TableCell($fullCrapMethodCount ? '<error> This Code Is C.R.A.P. </error>' : '<success> This code is very nice, I love it. </success>', ['colspan' => 2])],
            ])
        ;
        $tableSummary->render();
        $output->writeln('');
    }

    /**
     * @param float $crapValue
     * @param int   $cyclomaticComplexity
     * @param float $coveragePercent
     *
     * @return float
     */
    private function getCrapLoad($crapValue, $cyclomaticComplexity, $coveragePercent)
    {
        $crapLoad = 0;

        if ($crapValue >= $this->threshold) {
            $crapLoad += $cyclomaticComplexity * (1.0 - $coveragePercent / 100);
            $crapLoad += $cyclomaticComplexity / $this->threshold;
        }

        return $crapLoad;
    }
}
