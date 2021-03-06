<?php

/**
 * This file is part of cloak.
 *
 * (c) Noritaka Horio <holy.shared.design@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use cloak\Result;
use cloak\result\Line;
use cloak\reporter\LcovReporter;
use \Mockery;
use \DateTime;

describe('LcovReporter', function() {

    describe('onStart', function() {
        before(function() {
            $this->reportFile = __DIR__ . '/../tmp/report.lcov';

            $this->reporter = new LcovReporter($this->reportFile);
            $this->reporter->onStart($this->startEvent);

            $this->startEvent = Mockery::mock('cloak\event\StartEventInterface');
            $this->startEvent->shouldReceive('getSendAt')->never();
        });
        it('check mock object expectations', function() {
            Mockery::close();
        });
    });

    describe('onStop', function() {
        before(function() {
            $this->reportFile = __DIR__ . '/../tmp/report.lcov';
            $this->reporter = new LcovReporter($this->reportFile);

            $this->source1 = realpath(__DIR__ . '/../fixture/Example1.php');
            $this->source2 = realpath(__DIR__ . '/../fixture/Example2.php');

            $this->result = Result::from([
                $this->source1 => [
                    10 => Line::EXECUTED,
                    11 => Line::EXECUTED
                ],
                $this->source2 => [
                    10 => Line::EXECUTED,
                    15 => Line::UNUSED
                ]
            ]);

            $this->stopEvent = Mockery::mock('\cloak\event\StopEventInterface');
            $this->stopEvent->shouldReceive('getResult')->once()->andReturn($this->result);

            $this->reporter->onStop($this->stopEvent);

            $output  = "";
            $output .= "SF:" . $this->source1 . PHP_EOL;
            $output .= "DA:10,1" . PHP_EOL;
            $output .= "DA:11,1" . PHP_EOL;
            $output .= "end_of_record" . PHP_EOL;

            $output .= "SF:" . $this->source2 . PHP_EOL;
            $output .= "DA:10,1" . PHP_EOL;
            $output .= "DA:15,0" . PHP_EOL;
            $output .= "end_of_record" . PHP_EOL;

            $this->output = $output;
        });
        after(function() {
            unlink($this->reportFile);
        });
        it('should output lcov report file', function() {
            $result = file_get_contents($this->reportFile);
            expect($result)->toEqual($this->output);
        });
        it('check mock object expectations', function() {
            Mockery::close();
        });
    });

});
